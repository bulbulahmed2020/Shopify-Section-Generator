<?php

namespace App\Services\Providers;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Log;

class OpenRouterProvider implements AIProviderInterface
{
    private HttpClient $client;
    private string $apiKey;
    private string $model;

    public function __construct(string $model = null)
    {
        $this->apiKey = env('OPENROUTER_API_KEY', '');

        $this->model = $model ?? env(
            'OPENROUTER_MODEL',
            'deepseek/deepseek-chat'
        );

        $this->client = new HttpClient([

            'base_uri' => 'https://openrouter.ai/api/v1/',

            'headers' => [

                'Authorization' =>
                    'Bearer ' . $this->apiKey,

                'Content-Type' =>
                    'application/json',

                'HTTP-Referer' =>
                    env('APP_URL', 'http://localhost:8000'),

                'X-Title' =>
                    env(
                        'APP_NAME',
                        'Shopify Section Generator'
                    ),
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

            $response = retry(
                3,

                function () use (
                    $systemPrompt,
                    $prompt
                ) {

                    return $this->client->post(
                        'chat/completions',

                        [

                            'json' => [

                                'model' =>
                                    $this->model,

                                'messages' => [

                                    [
                                        'role' => 'system',

                                        'content' =>
                                            $systemPrompt,
                                    ],

                                    [
                                        'role' => 'user',

                                        'content' =>
                                            $prompt,
                                    ],
                                ],

                                // Lower temperature
                                // = better Shopify syntax
                                'temperature' => 0.3,

                                'max_tokens' => 1000,
                            ],
                        ]
                    );
                },

                // Retry delay
                3000
            );

            $body = (string)
                $response->getBody();

            $statusCode =
                $response->getStatusCode();

            Log::info(
                'OpenRouter API Response',

                [
                    'status' => $statusCode,

                    'body_length' =>
                        strlen($body),

                    'model' =>
                        $this->model,
                ]
            );

            if ($statusCode !== 200) {

                Log::error(
                    'OpenRouter Non-200 Response',

                    [
                        'body' => $body
                    ]
                );

                return [

                    'error' =>
                        'OpenRouter API returned non-200 status: '
                        . $statusCode,

                    'raw_response' =>
                        substr($body, 0, 1000),
                ];
            }

            $data = json_decode(
                $body,
                true
            );

            if (
                json_last_error()
                !== JSON_ERROR_NONE
            ) {

                Log::error(
                    'OpenRouter JSON Parse Error',

                    [
                        'body' => $body
                    ]
                );

                return [

                    'error' =>
                        'OpenRouter API returned invalid JSON',

                    'raw_response' =>
                        substr($body, 0, 1000),

                    'json_error' =>
                        json_last_error_msg(),
                ];
            }

            $content =
                $data['choices'][0]['message']['content']
                ?? '';

            if (empty($content)) {

                Log::error(
                    'OpenRouter Empty Content',

                    [
                        'data' => $data
                    ]
                );

                return [

                    'error' =>
                        'OpenRouter API returned empty content',

                    'raw_response' =>
                        substr($body, 0, 1000),
                ];
            }

            Log::info(
                'OpenRouter Raw Content',

                [
                    'content' => $content
                ]
            );

            return $this->parseResponse($content);

        } catch (\Throwable $e) {

            Log::error(
                'OpenRouter API Exception',

                [
                    'error' =>
                        $e->getMessage(),

                    'file' =>
                        $e->getFile(),

                    'line' =>
                        $e->getLine(),
                ]
            );

            return [

                'error' =>
                    'OpenRouter Error: '
                    . $e->getMessage(),
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

    public function getModels(): array
    {
        return [
            'deepseek/deepseek-chat' => 'DeepSeek Chat',
            'mistralai/mixtral-8x7b' => 'Mixtral 8x7B',
            'meta-llama/llama-3-70b' => 'Llama 3 70B',
            'meta-llama/llama-3.1-8b' => 'Llama 3.1 8B',
            'openai/gpt-4o' => 'GPT-4o (via OpenRouter)',
            'openai/gpt-4o-mini' => 'GPT-4o Mini (via OpenRouter)',
            'anthropic/claude-3.5-sonnet' => 'Claude 3.5 Sonnet',
            'google/gemini-2.0-flash' => 'Gemini 2.0 Flash (via OpenRouter)',
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
4. No triple backticks
5. Include:
   - section_name
   - schema
   - liquid
   - css

6. Generate REAL Shopify-compatible sections

7. NEVER generate:
   - {% section %}
   - {% endsection %}
   - parse_json
   - textarea JSON parsing

8. ALWAYS generate valid Shopify section structure:
   HTML/Liquid
   CSS
   JavaScript
   {% schema %}
   JSON
   {% endschema %}

9. For repeatable content like:
   - FAQs
   - Testimonials
   - Features
   - Sliders
   - Accordions
   - Reviews
   - Tabs
   - Team members

ALWAYS use:
- section.blocks
- block.settings

10. NEVER use:
- textarea JSON inputs
- custom JSON parsing
- unsupported Shopify Liquid filters

11. Schema MUST follow Shopify standards

12. Include at least one preset

13. Liquid code must be production-ready

14. CSS should be valid and responsive

Required JSON format:

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

            $content = trim($content);

            // Remove markdown
            $content = preg_replace(
                '/```json/i',
                '',
                $content
            );

            $content = preg_replace(
                '/```/',
                '',
                $content
            );

            $content = trim($content);

            // Escape raw newlines/tabs
            $content = preg_replace_callback(

                '/"((?:\\\\.|[^"\\\\])*)"/s',

                function ($matches) {

                    $string = $matches[1];

                    $string = str_replace(

                        ["\r", "\n", "\t"],

                        ['\\r', '\\n', '\\t'],

                        $string
                    );

                    return '"' . $string . '"';
                },

                $content
            );

            $decoded = json_decode(
                $content,
                true
            );

            if (
                json_last_error()
                !== JSON_ERROR_NONE
            ) {

                return [

                    'error' =>
                        'JSON Decode Error: '
                        . json_last_error_msg(),

                    'raw_json' =>
                        substr($content, 0, 5000),
                ];
            }

            return $this->validateResponse($decoded);

        } catch (\Throwable $e) {

            return [

                'error' =>
                    'Failed to parse OpenRouter response: '
                    . $e->getMessage(),

                'raw_response' =>
                    substr($content, 0, 5000),
            ];
        }
    }

    private function validateResponse(array $data): array
    {
        return [

            'section_name' =>
                $data['section_name']
                ?? 'unnamed-section',

            'schema' =>
                $data['schema'] ?? [],

            'liquid' =>
                $data['liquid'] ?? '',

            'css' =>
                $data['css'] ?? '',

            'provider' => 'OpenRouter',
        ];
    }
}