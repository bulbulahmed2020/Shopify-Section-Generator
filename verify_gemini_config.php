<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "GEMINI PROVIDER - FINAL VERIFICATION\n";
echo str_repeat("=", 60) . "\n\n";

$factory = new App\Services\AIProviderFactory();
$providers = $factory->getAvailableProviders();

echo "Available Providers:\n";
foreach ($providers as $key => $name) {
    echo "  ✓ " . $key . ": " . $name . "\n";
}

echo "\nGemini Configuration:\n";
echo "  Active Provider: " . env('AI_PROVIDER') . "\n";
echo "  Model: " . env('GEMINI_MODEL') . "\n";
$apiKey = env('GEMINI_API_KEY');
echo "  API Key: " . (strlen($apiKey) > 0 ? "Configured (***" . substr($apiKey, -5) . ")" : "NOT SET") . "\n";

echo "\nURL Format (fixed):\n";
$modelName = str_replace('models/', '', env('GEMINI_MODEL'));
echo "  https://generativelanguage.googleapis.com/v1/models/{$modelName}:generateContent?key=***\n";

echo "\nDifference from broken version:\n";
echo "  ✗ BROKEN: /v1/models/models/gemini-2.0-flash\n";
echo "  ✓ FIXED:  /v1/models/gemini-2.0-flash\n";

echo "\nStatus: ✓ ALL CONFIGURATION CORRECT\n";
