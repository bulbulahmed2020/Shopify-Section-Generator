<?php

namespace App\Services;

use App\Services\Providers\{AIProviderInterface, OpenAIProvider, OpenRouterProvider, GeminiProvider, GrokProvider};

class AIProviderFactory
{
    /**
     * Create an AI provider instance based on configuration
     */
    public static function create(string $provider = null): AIProviderInterface
    {
        $provider = $provider ?? env('AI_PROVIDER', 'openai');

        return match (strtolower($provider)) {
            'gemini' => new GeminiProvider(),
            'grok' => new GrokProvider(),
            'openrouter' => new OpenRouterProvider(),
            'openai' => new OpenAIProvider(),
            default => new OpenAIProvider(), // Default fallback
        };
    }

    /**
     * Get available providers
     */
    public static function getAvailableProviders(): array
    {
        $providers = [];

        $openai = new OpenAIProvider();
        $openrouter = new OpenRouterProvider();
        $gemini = new GeminiProvider();
        $grok = new GrokProvider();

        if ($openai->isConfigured()) {
            $providers['openai'] = 'OpenAI (GPT-4o)';
        }

        if ($openrouter->isConfigured()) {
            $providers['openrouter'] = 'OpenRouter';
        }

        if ($gemini->isConfigured()) {
            $providers['gemini'] = 'Google Gemini';
        }

        if ($grok->isConfigured()) {
            $providers['grok'] = 'Grok';
        }

        return $providers;
    }

    /**
     * Get provider details for the current provider
     */
    public static function getProviderInfo(string $provider = null): array
    {
        $instance = self::create($provider);
        
        return [
            'name' => $instance->getName(),
            'configured' => $instance->isConfigured(),
        ];
    }
}
