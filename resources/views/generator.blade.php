<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>{{ config('app.name') }}</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">

<div class="container mx-auto px-4 py-12 max-w-7xl">

    <!-- Header -->
    <div class="text-center mb-12">

        <h1 class="text-5xl font-bold text-gray-900 mb-4">
            🛍️ Shopify Section Generator
        </h1>

        <p class="text-xl text-gray-600">
            AI-powered Shopify section builder
        </p>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Sidebar -->
        <div class="lg:col-span-1">

            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6">

                <h2 class="text-2xl font-bold mb-6">
                    Create Section
                </h2>

                <!-- FORM -->
                <form id="generatorForm" class="space-y-5">

                    @csrf

                    <!-- Prompt -->
                    <div>

                        <label class="block font-semibold mb-2">
                            Describe Your Section
                        </label>

                        <textarea
                            id="prompt"
                            name="prompt"
                            rows="8"
                            class="w-full border-2 border-gray-300 rounded-lg p-4 focus:border-blue-500 focus:outline-none resize-none"
                            placeholder="Create a FAQ section with accordion items..."
                        ></textarea>

                        <div
                            id="charCount"
                            class="text-xs text-gray-500 mt-2"
                        >
                            0 / 2000 characters
                        </div>

                    </div>

                    <!-- Provider -->
                    @if(count($providers) > 1)

                    <div>

                        <label class="block font-semibold mb-2">
                            AI Provider
                        </label>

                        <select
                            id="provider"
                            name="provider"
                            class="w-full border-2 border-gray-300 rounded-lg p-3 focus:border-blue-500 focus:outline-none bg-white"
                        >

                            @foreach($providers as $key => $name)

                            <option
                                value="{{ $key }}"
                                {{ $currentProvider['name'] === $name ? 'selected' : '' }}
                            >
                                {{ $name }}
                            </option>

                            @endforeach

                        </select>

                    </div>

                    @endif

                    <!-- BUTTON -->
                    <button
                        type="submit"
                        id="generateBtn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg py-4 px-6 rounded-xl transition-all duration-200 shadow-md"
                    >
                        ✨ Generate Section
                    </button>

                    <!-- Error -->
                    <div
                        id="errorMessage"
                        class="hidden bg-red-50 border border-red-300 text-red-700 p-4 rounded-lg"
                    ></div>

                </form>

                <!-- PRESETS -->
                <div class="mt-10 pt-8 border-t border-gray-200">

                    <h3 class="text-lg font-bold mb-4">
                        Quick Presets
                    </h3>

                    <div class="space-y-3">

                        @foreach($presets as $preset)

                        <button
                            type="button"
                            class="preset-btn w-full text-left p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl hover:from-blue-100 hover:to-indigo-100 transition-all"
                            onclick="fillPreset(`{{ $preset['prompt'] }}`)"
                        >

                            <div class="font-semibold text-gray-900">
                                {{ $preset['label'] }}
                            </div>

                            <div class="text-sm text-gray-600 mt-1">
                                {{ $preset['description'] }}
                            </div>

                        </button>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

        <!-- OUTPUT -->
        <div class="lg:col-span-2">

            <!-- Loading -->
            <div
                id="loadingState"
                class="hidden bg-white rounded-xl shadow-lg p-12 text-center"
            >

                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mx-auto mb-6"></div>

                <p class="text-lg font-semibold text-gray-700">
                    Generating your Shopify section...
                </p>

            </div>

            <!-- Output -->
            <div
                id="outputContainer"
                class="hidden space-y-6"
            >

                <!-- Generated Section -->
                <div class="bg-white rounded-xl shadow-lg p-6">

                    <div class="flex justify-between items-center mb-5">

                        <div>

                            <h2 class="text-2xl font-bold text-gray-900">
                                Shopify Section File
                            </h2>

                            <p class="text-gray-500 text-sm mt-1">
                                Copy & paste directly into Shopify
                                sections/*.liquid
                            </p>

                        </div>

                        <button
                            onclick="copyToClipboard('liquidOutput', this)"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold"
                        >
                            📋 Copy Code
                        </button>

                    </div>

                    <pre class="bg-gray-900 text-green-400 rounded-xl p-6 overflow-auto text-sm max-h-[800px]">
                        <code id="liquidOutput"></code>
                    </pre>

                </div>

                <!-- Prompt -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-5 rounded-lg">

                    <h3 class="font-bold text-blue-900 mb-2">
                        Your Prompt
                    </h3>

                    <p
                        id="originalInput"
                        class="text-gray-700"
                    ></p>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="{{ asset('js/generator.js') }}"></script>

</body>
</html>