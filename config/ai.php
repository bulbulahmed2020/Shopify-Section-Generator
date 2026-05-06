<?php

return [
    /**
     * Default AI Provider: 'openai', 'openrouter', 'gemini', or 'grok'
     */
    'default' => env('AI_PROVIDER', 'openai'),

    /**
     * Available providers and their configuration
     */
    'providers' => [
        'openai' => [
            'name' => 'OpenAI',
            'description' => 'OpenAI GPT-4 and GPT-3.5',
            'enabled' => !empty(env('OPENAI_API_KEY')),
            'models' => [
                'gpt-4o' => 'GPT-4o (Recommended)',
                'gpt-4-turbo' => 'GPT-4 Turbo',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            ],
        ],
        'openrouter' => [
            'name' => 'OpenRouter',
            'description' => 'OpenRouter - Access to multiple AI models',
            'enabled' => !empty(env('OPENROUTER_API_KEY')),
            'models' => [
                'openai/gpt-4o' => 'GPT-4o (Recommended)',
                'openai/gpt-4-turbo' => 'GPT-4 Turbo',
                'openai/gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                'anthropic/claude-3.5-sonnet' => 'Claude 3.5 Sonnet',
                'anthropic/claude-3-haiku' => 'Claude 3 Haiku',
                'meta-llama/llama-3.1-70b-instruct' => 'Llama 3.1 70B',
                'google/gemini-pro' => 'Gemini Pro',
            ],
        ],
        'gemini' => [
            'name' => 'Google Gemini',
            'description' => 'Google Generative AI',
            'enabled' => !empty(env('GEMINI_API_KEY')),
            'models' => [
                'gemini-2.0-flash' => 'Gemini 2.0 Flash (Recommended)',
                'gemini-2.0-flash-lite' => 'Gemini 2.0 Flash Lite',
                'gemini-2.5-flash' => 'Gemini 2.5 Flash (Latest)',
            ],
        ],
        'grok' => [
            'name' => 'Grok',
            'description' => 'Grok AI (X.AI)',
            'enabled' => !empty(env('GROK_API_KEY')),
            'models' => [
                'grok-2-1212' => 'Grok 2 (Latest)',
                'grok-vision-beta' => 'Grok Vision Beta',
            ],
        ],
    ],

    /**
     * Temperature for generation (0-2)
     * Higher = more creative, Lower = more consistent
     */
    'temperature' => 0.7,

    /**
     * Maximum tokens for generation
     */
    'max_tokens' => 2000,

    /**
     * Whether to cache responses
     */
    'cache' => false,
    'cache_ttl' => 3600, // 1 hour
];
