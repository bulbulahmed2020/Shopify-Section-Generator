<?php

namespace App\Services\Providers;

use OpenAI\Client;

class OpenAIProvider implements AIProviderInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = \OpenAI::client(env('OPENAI_API_KEY'));
    }

    public function generateSection(string $prompt): array
    {
        $systemPrompt = $this->getSystemPrompt();

        try {
            $response = $this->client->chat()->create([
                'model' => env('OPENAI_MODEL', 'gpt-4o'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

            $content = $response->choices[0]->message->content ?? '';
            
            return $this->parseResponse($content);
        } catch (\Exception $e) {
            return [
                'error' => 'OpenAI Error: ' . $e->getMessage(),
            ];
        }
    }

    public function getName(): string
    {
        return 'OpenAI';
    }

    public function isConfigured(): bool
    {
        return !empty(env('OPENAI_API_KEY'));
    }

    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
You are a Shopify expert. Generate a valid Shopify section with schema and Liquid code.

Your response MUST be valid JSON in this exact format:
{
  "section_name": "Name of the section",
  "schema": {
    "name": "Section Name",
    "settings": [
      {"type": "text", "id": "heading", "label": "Heading"}
    ],
    "blocks": [
      {"type": "item", "name": "Item", "settings": []}
    ],
    "presets": [
      {"name": "Default", "settings": {}, "blocks": []}
    ]
  },
  "liquid": "{% section 'section-name' %}\n<div>{{ section.settings.heading }}</div>\n{% endsection %}",
  "css": "section { padding: 20px; }"
}

Requirements:
- Schema MUST follow Shopify standards
- Include configurable blocks and settings
- Settings must be editable in the Shopify theme editor
- Liquid code must be production-ready
- Only return valid JSON, nothing else
- Include at least one preset in schema
- CSS is optional but if provided, should be valid

User request follows:
PROMPT;
    }

    private function parseResponse(string $content): array
    {
        try {
            $jsonMatch = preg_match('/\{[\s\S]*\}/', $content, $matches);
            
            if ($jsonMatch) {
                $decoded = json_decode($matches[0], true);
                
                if (is_array($decoded) && !isset($decoded['error'])) {
                    return $this->validateResponse($decoded);
                }
            }
            
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                return $this->validateResponse($decoded);
            }
            
            return [
                'error' => 'Invalid response format from OpenAI',
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to parse OpenAI response: ' . $e->getMessage(),
            ];
        }
    }

    private function validateResponse(array $data): array
    {
        return [
            'section_name' => $data['section_name'] ?? 'Unnamed Section',
            'schema' => $data['schema'] ?? [],
            'liquid' => $data['liquid'] ?? '',
            'css' => $data['css'] ?? '',
            'provider' => 'OpenAI',
        ];
    }
}
