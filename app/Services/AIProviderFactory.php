<?php

namespace App\Services;

use App\Services\Providers\{AIProviderInterface, OpenAIProvider, OpenRouterProvider, GeminiProvider, GrokProvider};

class AIProviderFactory
{
    /**
     * Create an AI provider instance based on configuration
     */
    public static function create(string $provider = null, string $model = null): AIProviderInterface
    {
        $provider = $provider ?? env('AI_PROVIDER', 'openai');

        return match (strtolower($provider)) {
            'gemini' => new GeminiProvider($model),
            'grok' => new GrokProvider($model),
            'openrouter' => new OpenRouterProvider($model),
            'openai' => new OpenAIProvider($model),
            default => new OpenAIProvider($model), // Default fallback
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
            $providers['openai'] = 'OpenAI (GPT)';
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

    /**
     * Get models for a specific provider
     */
    public static function getModelsForProvider(string $provider): array
    {
        $instance = self::create($provider);
        
        if ($instance->isConfigured()) {
            return $instance->getModels();
        }

        return [];
    }
}
