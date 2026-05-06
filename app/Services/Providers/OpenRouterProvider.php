<?php

namespace App\Services\Providers;

use GuzzleHttp\Client as HttpClient;

class OpenRouterProvider implements AIProviderInterface
{
    private HttpClient $client;
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = env('OPENROUTER_API_KEY', '');
        $this->model = env('OPENROUTER_MODEL', 'openai/gpt-4o');

        $this->client = new HttpClient([
            'base_uri' => 'https://openrouter.ai/api/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => env('APP_URL', 'http://localhost:8000'),
                'X-Title' => env('APP_NAME', 'Shopify Section Generator'),
            ],
            'timeout' => 60,
            'connect_timeout' => 20,
            'http_errors' => true,
        ]);
    }

    public function generateSection(string $prompt): array
    {
        $systemPrompt = $this->getSystemPrompt();

        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $this->model,
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

            $body = (string) $response->getBody();
            $statusCode = $response->getStatusCode();
            
            // Log the response for debugging
            \Log::info('OpenRouter API Response', [
                'status' => $statusCode,
                'body_length' => strlen($body),
                'model' => $this->model,
            ]);

            if ($statusCode !== 200) {
                \Log::error('OpenRouter Non-200 Response', ['body' => $body]);
                return [
                    'error' => 'OpenRouter API returned non-200 status: ' . $statusCode,
                    'raw_response' => substr($body, 0, 1000),
                ];
            }

            $data = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('OpenRouter JSON Parse Error', ['body' => $body]);
                return [
                    'error' => 'OpenRouter API returned invalid JSON',
                    'raw_response' => substr($body, 0, 1000),
                    'json_error' => json_last_error_msg(),
                ];
            }
            
            $content = $data['choices'][0]['message']['content'] ?? '';
            
            if (empty($content)) {
                \Log::error('OpenRouter Empty Content', ['data' => $data]);
                return [
                    'error' => 'OpenRouter API returned empty content',
                    'raw_response' => substr($body, 0, 1000),
                ];
            }

            return $this->parseResponse($content);
        } catch (\Exception $e) {
            \Log::error('OpenRouter API Exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return [
                'error' => 'OpenRouter Error: ' . $e->getMessage(),
            ];
        }
    }

    public function getName(): string
    {
        return 'OpenRouter';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
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
            $content = trim($content);

            // Remove markdown code blocks if present
            $content = preg_replace('/```json/i', '', $content);
            $content = preg_replace('/```/', '', $content);
            $content = trim($content);

            // Try to find JSON object by looking for balanced braces
            $json = null;
            $start = strpos($content, '{');
            
            if ($start !== false) {
                $braceCount = 0;
                $inString = false;
                $escapeNext = false;
                
                for ($i = $start; $i < strlen($content); $i++) {
                    $char = $content[$i];
                    
                    if ($escapeNext) {
                        $escapeNext = false;
                        continue;
                    }
                    
                    if ($char === '\\' && !$escapeNext) {
                        $escapeNext = true;
                        continue;
                    }
                    
                    if ($char === '"' && !$escapeNext) {
                        $inString = !$inString;
                        continue;
                    }
                    
                    if (!$inString) {
                        if ($char === '{') {
                            $braceCount++;
                        } elseif ($char === '}') {
                            $braceCount--;
                            if ($braceCount === 0) {
                                $json = substr($content, $start, $i - $start + 1);
                                break;
                            }
                        }
                    }
                }
            }

            if ($json) {
                $decoded = json_decode($json, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $this->validateResponse($decoded);
                }
                
                return [
                    'error' => 'JSON Decode Error: ' . json_last_error_msg(),
                    'raw_json' => $json,
                ];
            }

            // Fallback: try regex extraction
            if (preg_match('/\{.*\}/s', $content, $matches)) {
                $json = $matches[0];
                $decoded = json_decode($json, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $this->validateResponse($decoded);
                }
            }

            return [
                'error' => 'No JSON found in OpenRouter response',
                'raw_response' => $content,
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to parse OpenRouter response: ' . $e->getMessage(),
                'raw_response' => $content,
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
            'provider' => 'OpenRouter',
        ];
    }
}
