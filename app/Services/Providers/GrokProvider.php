<?php

namespace App\Services\Providers;

use GuzzleHttp\Client as HttpClient;

class GrokProvider implements AIProviderInterface
{
    private HttpClient $client;

    public function __construct()
    {
        $this->client = new HttpClient([
            'base_uri' => 'https://api.x.ai/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . env('GROK_API_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function generateSection(string $prompt): array
    {
        $systemPrompt = $this->getSystemPrompt();

        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => env('GROK_MODEL', 'grok-vision-beta'),
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
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $content = $data['choices'][0]['message']['content'] ?? '';
            
            return $this->parseResponse($content);
        } catch (\Exception $e) {
            return [
                'error' => 'Grok Error: ' . $e->getMessage(),
            ];
        }
    }

    public function getName(): string
    {
        return 'Grok';
    }

    public function isConfigured(): bool
    {
        return !empty(env('GROK_API_KEY'));
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
                'error' => 'Invalid response format from Grok',
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to parse Grok response: ' . $e->getMessage(),
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
            'provider' => 'Grok',
        ];
    }
}
