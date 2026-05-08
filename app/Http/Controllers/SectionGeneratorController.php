<?php

namespace App\Http\Controllers;

use App\Services\SectionGeneratorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionGeneratorController extends Controller
{
    private SectionGeneratorService $service;

    public function __construct(SectionGeneratorService $service)
    {
        $this->service = $service;
    }

    /**
     * Show Generator Page
     */
    public function index(): View
    {
        $presets = $this->service->getPresets();

        $providers = $this->service->getAvailableProviders();

        $currentProvider = $this->service->getCurrentProvider();

        $models = [];
        $configuredProviders = $this->service->getAvailableProviders();
        foreach ($configuredProviders as $key => $name) {
            $models[$key] = $this->service->getModelsForProvider($key);
        }

        return view('generator', [
            'presets' => $presets,
            'providers' => $providers,
            'currentProvider' => $currentProvider,
            'models' => $models,
        ]);
    }

    /**
     * Generate Shopify Section
     */
    public function generate(Request $request)
    {
        // Validation
        $validated = $request->validate([

            'prompt' => [
                'required',
                'string',
                'min:10',
                'max:2000'
            ],

            'provider' => [
                'nullable',
                'string',
                'in:openai,openrouter,gemini,grok'
            ],

            'model' => [
                'nullable',
                'string',
            ],

        ]);

        // Create Service
        $service = new SectionGeneratorService(
            $validated['provider'] ?? null
        );

        // Generate
        $result = $service->generateSection(
            $validated['prompt'],
            $validated['model'] ?? null
        );

        // Error handling
        if (isset($result['error'])) {

            return response()->json([
                'error' => $result['error'],
                'input' => $validated['prompt'],
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Build Final Shopify Liquid File
        |--------------------------------------------------------------------------
        */

        $liquid = stripcslashes(
            $result['liquid'] ?? ''
        );

        $css = stripcslashes(
            $result['css'] ?? ''
        );

        $schema = json_encode(
            $result['schema'] ?? [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );

        $finalLiquid =
            $liquid .

            (!empty($css)
                ? "\n\n<style>\n{$css}\n</style>"
                : '') .

            "\n\n{% schema %}\n" .
            $schema .
            "\n{% endschema %}";

        return response()->json([

            'success' => true,

            'section_name' =>
                $result['section_name'] ?? 'custom-section',

            'liquid_code' => $finalLiquid,

            'input' => $validated['prompt'],

            'provider' => $result['provider'] ?? 'Unknown',
        ]);
    }
}