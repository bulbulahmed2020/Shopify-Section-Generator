<?php

namespace App\Services;

class SectionGeneratorService
{
    private $provider;

    public function __construct(string $providerName = null)
    {
        $this->provider = AIProviderFactory::create($providerName);
    }

    /**
     * Generate Shopify section from user prompt
     */
    public function generateSection(string $prompt, string $model = null): array
    {
        if ($model) {
            $this->provider->setModel($model);
        }

        return $this->provider->generateSection($prompt);
    }
    
    /**
     * Get models for a specific provider
     */
    public function getModelsForProvider(string $provider): array
    {
        return AIProviderFactory::getModelsForProvider($provider);
    }
    
    /**
     * Get available providers
     */
    public function getAvailableProviders(): array
    {
        return AIProviderFactory::getAvailableProviders();
    }

    /**
     * Get current provider info
     */
    public function getCurrentProvider(): array
    {
        return AIProviderFactory::getProviderInfo();
    }

    /**
     * Get preset templates
     */
    public function getPresets(): array
    {
        return [
            [
                'id' => 'faq',
                'label' => '❓ FAQ Section',
                'description' => 'A FAQ section with collapsible items',
                'prompt' => 'Create a Shopify FAQ section with collapsible items. Each item should have a question and answer. Make it look professional with smooth animations.',
            ],
            [
                'id' => 'banner',
                'label' => '📍 Hero Banner',
                'description' => 'A full-width hero banner section',
                'prompt' => 'Create a Shopify hero banner section with a background image, headline, description, and CTA button. It should be responsive and look professional.',
            ],
            [
                'id' => 'product',
                'label' => '🛍️ Featured Product',
                'description' => 'A featured product display section',
                'prompt' => 'Create a Shopify featured product section that displays a product image, title, price, rating, description, and add to cart button. Make it professional and mobile-responsive.',
            ],
            [
                'id' => 'testimonial',
                'label' => '⭐ Testimonials',
                'description' => 'A customer testimonials carousel',
                'prompt' => 'Create a Shopify testimonials section with a carousel showing customer reviews. Include customer name, image, stars rating, and testimonial text. Make it responsive and elegant.',
            ],
            [
                'id' => 'cta',
                'label' => '🎯 Call-to-Action',
                'description' => 'A simple CTA section',
                'prompt' => 'Create a Shopify call-to-action section with a headline, description, and customizable button. Make it visually appealing with background color options.',
            ],
        ];
    }
}
