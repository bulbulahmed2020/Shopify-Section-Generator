const form = document.getElementById('generatorForm');

const prompt = document.getElementById('prompt');

const generateBtn = document.getElementById('generateBtn');

const loadingState = document.getElementById('loadingState');

const outputContainer = document.getElementById('outputContainer');

const errorMessage = document.getElementById('errorMessage');

form.addEventListener('submit', async function (e)
{
    e.preventDefault();

    if (!prompt.value.trim()) {

        showError('Please enter a prompt.');

        return;
    }

    showLoading(true);

    try {

        const response = await fetch('/generate', {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'Accept': 'application/json',

                'X-CSRF-TOKEN':
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content
            },

            body: JSON.stringify({

                prompt: prompt.value,

                provider:
                    document.getElementById('provider')?.value,

                model:
                    document.getElementById('model')?.value || null,
            })
        });

        const data = await response.json();

        console.log(data);

        if (!response.ok) {

            showError(
                data.error || 'Failed to generate section.'
            );

            showLoading(false);

            return;
        }

        displayResults(data);

    } catch (error) {

        console.error(error);

        showError(error.message);

    } finally {

        showLoading(false);
    }
});

function showLoading(show)
{
    loadingState.classList.toggle('hidden', !show);

    if (show) {
        outputContainer.classList.add('hidden');
    }

    generateBtn.disabled = show;

    generateBtn.style.opacity = show ? '0.6' : '1';

    generateBtn.textContent = show
        ? '⏳ Generating...'
        : '✨ Generate Section';
}

function showError(message)
{
    errorMessage.textContent = message;

    errorMessage.classList.remove('hidden');
}

function displayResults(data)
{
    errorMessage.classList.add('hidden');

    const output =
        document.getElementById('liquidOutput');

    const input =
        document.getElementById('originalInput');

    // VERY IMPORTANT
    // NEVER use innerHTML here
    output.textContent =
        data.liquid_code || '';

    input.textContent =
        data.input || '';

    outputContainer.classList.remove('hidden');

    setTimeout(() => {

        outputContainer.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

    }, 100);
}

function copyToClipboard(id, button)
{
    const element =
        document.getElementById(id);

    navigator.clipboard.writeText(
        element.textContent
    );

    const original =
        button.innerHTML;

    button.innerHTML = '✅ Copied';

    setTimeout(() => {

        button.innerHTML = original;

    }, 2000);
}

function fillPreset(promptText)
{
    prompt.value = promptText;

    updateCharCount();
}

function updateCharCount()
{
    const count =
        document.getElementById('charCount');

    count.textContent =
        prompt.value.length + ' / 2000 characters';
}

function updateModels()
{
    const providerSelect =
        document.getElementById('provider');

    const modelSelect =
        document.getElementById('model');

    if (!providerSelect || !modelSelect) return;

    const selectedProvider = providerSelect.value;

    // Get models for this provider from window.__MODELS
    const providerModels = window.__MODELS?.[selectedProvider] || {};

    // Clear current options
    modelSelect.innerHTML = '<option value="">Default Model</option>';

    // Add new options
    Object.keys(providerModels).forEach(function(modelKey) {
        const option = document.createElement('option');
        option.value = modelKey;
        option.textContent = providerModels[modelKey];
        modelSelect.appendChild(option);
    });
}

// Initialize models when page loads
document.addEventListener('DOMContentLoaded', function()
{
    updateModels();
});

prompt.addEventListener(
    'input',
    updateCharCount
);