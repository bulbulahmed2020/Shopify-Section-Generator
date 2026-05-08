<?php

namespace App\Services\Providers;

interface AIProviderInterface
{
    /**
     * Generate Shopify section from user prompt
     */
    public function generateSection(string $prompt): array;

    /**
     * Get provider name
     */
    public function getName(): string;

    /**
     * Check if provider is configured
     */
    public function isConfigured(): bool;

    /**
     * Get available models for this provider
     */
    public function getModels(): array;

    /**
     * Set the model to use
     */
    public function setModel(string $model): void;

    /**
     * Get current model
     */
    public function getModel(): string;
}