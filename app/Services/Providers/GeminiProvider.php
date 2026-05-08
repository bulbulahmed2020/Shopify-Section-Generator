<?php

namespace App\Services\Providers;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AIProviderInterface
{
    private HttpClient $client;
    private string $apiKey;
    private string $model;

    public function __construct(string $model = null)
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        $this->model = $model ?? env('GEMINI_MODEL', 'gemini-2.5-flash-lite');

        $this->client = new HttpClient([
            'timeout' => 60,
            'connect_timeout' => 20,
            'http_errors' => true,
        ]);
    }

    public function generateSection(string $prompt): array
    {
        $systemPrompt = $this->getSystemPrompt();

        try {

            $modelName = str_replace(
                'models/',
                '',
                $this->model
            );

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$this->apiKey}";

            $response = retry(
                3,

                function () use (
                    $url,
                    $systemPrompt,
                    $prompt
                ) {

                    return $this->client->post($url, [

                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],

                        'json' => [

                            'contents' => [
                                [
                                    'parts' => [
                                        [
                                            'text' => $systemPrompt . "\n\n" . $prompt
                                        ]
                                    ]
                                ]
                            ],

                            'generationConfig' => [
                                'temperature' => 0.3,
                                'maxOutputTokens' => 8192,

                                // Important
                                'responseMimeType' => 'application/json',
                            ],
                        ],
                    ]);
                },

                // Retry delay
                3000
            );

            $body = $response
                ->getBody()
                ->getContents();

            $data = json_decode($body, true);

            Log::info('Gemini Full Response', $data);

            $parts = $data['candidates'][0]['content']['parts'] ?? [];

            $content = '';

            foreach ($parts as $part) {

                if (isset($part['text'])) {

                    $content .= $part['text'];
                }
            }

            $content = trim($content);

            Log::info('Gemini Raw Content', [
                'content' => $content
            ]);

            if (empty($content)) {

                return [
                    'error' => 'Empty response from Gemini',
                    'response' => $data,
                ];
            }

            return $this->parseResponse($content);

        } catch (\Throwable $e) {

            $errorBody = null;

            if (
                method_exists($e, 'getResponse') &&
                $e->getResponse()
            ) {

                $errorBody = $e->getResponse()
                    ->getBody()
                    ->getContents();

                Log::error('Gemini API Error', [
                    'error' => $errorBody
                ]);
            }

            return [
                'error' => 'Gemini Error: ' . $e->getMessage(),
                'raw_error' => $errorBody,
            ];
        }
    }

    public function getName(): string
    {
        return 'Google Gemini';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function getModels(): array
    {
        return [
            'gemini-2.5-flash-lite' => 'Gemini 2.5 Flash Lite',
            'gemini-2.5-pro' => 'Gemini 2.5 Pro',
            'gemini-1.5-pro' => 'Gemini 1.5 Pro',
            'gemini-1.5-flash' => 'Gemini 1.5 Flash',
        ];
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function getModel(): string
    {
        return $this->model;
    }

   private function getSystemPrompt(): string
{
    return <<<'PROMPT'
You are a senior Shopify theme developer.

Generate complete production-ready Shopify sections.

IMPORTANT RULES:

1. Return ONLY valid JSON
2. No markdown
3. No explanations
4. Include:
   - section_name
   - schema
   - liquid
   - css

5. Generate REAL Shopify-compatible sections

6. For repeatable content like:
   - FAQs
   - Testimonials
   - Team members
   - Features
   - Sliders
   - Tabs
   - Accordions
   - Reviews

ALWAYS use Shopify blocks.

NEVER use:
- textarea JSON inputs
- parse_json
- custom JSON parsing

Use:
- section.blocks
- block.settings

Required format:

{
  "section_name": "",
  "schema": {},
  "liquid": "",
  "css": ""
}
PROMPT;
}

    private function parseResponse(string $content): array
    {
        try {

            // Remove markdown code fences if Gemini still returns them
            $content = preg_replace('/```(?:json)?\s*/i', '', $content);
            $content = trim($content);

            // Try direct JSON decode first
            $decoded = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->validateResponse($decoded);
            }

            // Fallback: extract JSON object from content
            if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
                $extracted = $matches[0];
                $decoded = json_decode($extracted, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    return $this->validateResponse($decoded);
                }
            }

            // Last resort: try to fix common JSON issues
            // Replace unescaped control characters
            $cleaned = preg_replace('/[^\x20-\x7E]/', '', $content);
            
            // Try extracting JSON from cleaned content
            if (preg_match('/\{[\s\S]*\}/', $cleaned, $matches)) {
                $decoded = json_decode($matches[0], true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    return $this->validateResponse($decoded);
                }
            }

            return [
                'error' => 'JSON Decode Error: ' . json_last_error_msg(),
                'raw_json' => substr($content, 0, 5000),
            ];

        } catch (\Throwable $e) {

            return [
                'error' => $e->getMessage(),
                'raw_response' => substr($content, 0, 5000),
            ];
        }
    }

    private function validateResponse(array $data): array
    {
        return [

            'section_name' => $data['section_name']
                ?? 'unnamed-section',

            'schema' => $data['schema'] ?? [],

            'liquid' => $data['liquid'] ?? '',

            'css' => $data['css'] ?? '',

            'provider' => 'Gemini',
        ];
    }
}