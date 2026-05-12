# Shopify Section Generator 🛍️

A minimal but clean MVP web app built with **Laravel 11** and **Blade templates** that generates Shopify sections using AI.

## Features

✨ **Multi-AI Provider Support**: Choose from OpenAI, Google Gemini, OpenRouter, or Grok  
🤖 **AI-Powered Generation**: Generates Shopify sections based on user descriptions  
📋 **Complete Output**: Generates section name, schema, Liquid code, and CSS  
⚡ **Quick Presets**: 5 pre-built preset templates (FAQ, Banner, Product, Testimonials, CTA)  
📋 **Easy Copy**: One-click copy buttons for all generated code  
🎨 **Clean UI**: Modern, responsive interface with Tailwind CSS  
✅ **Input Validation**: Ensures quality prompts with client and server validation  
🚨 **Error Handling**: Graceful error messages for API failures  

## Prerequisites

- **PHP 8.1+**
- **Laravel 11.x**
- **Composer**
- **Node.js & npm** (optional, for frontend)
- **At least one AI Provider API Key** (OpenAI, Gemini, OpenRouter, or Grok)

## Installation

### 1. Clone/Download the Project

```bash
cd shopify-generator
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

Copy `.env` and configure it:

```bash
cp .env.example .env
```

Edit `.env` and configure your preferred AI provider:

```env
# Choose default provider: openai, gemini, openrouter, or grok
AI_PROVIDER=openai

# OpenAI (GPT)
OPENAI_API_KEY=sk-your-api-key-here
OPENAI_MODEL=gpt-4o

# Google Gemini (alternative)
GEMINI_API_KEY=your-gemini-api-key-here
GEMINI_MODEL=gemini-2.5-flash-lite

# OpenRouter (alternative - multi-provider access)
OPENROUTER_API_KEY=your-openrouter-api-key-here
OPENROUTER_MODEL=deepseek/deepseek-chat

# Grok (alternative - xAI)
GROK_API_KEY=your-grok-api-key-here
GROK_MODEL=grok-2-latest

APP_KEY=base64:your-key-here
```

### 4. Generate App Key

```bash
php artisan key:generate
```

### 5. Run Development Server

```bash
php artisan serve
```

The app will be available at: **http://localhost:8000**

## Project Structure

```
shopify-generator/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   └── SectionGeneratorController.php
│   │   └── Middleware/
│   └── Services/
│       ├── AIProviderFactory.php
│       ├── SectionGeneratorService.php
│       └── Providers/
│           ├── AIProviderInterface.php
│           ├── OpenAIProvider.php
│           ├── GeminiProvider.php
│           ├── OpenRouterProvider.php
│           └── GrokProvider.php
├── config/
│   ├── ai.php
│   └── app.php
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── views/
│       └── generator.blade.php
├── routes/
│   ├── web.php
│   └── console.php
├── bootstrap/
│   └── app.php
├── public/
│   ├── index.php
│   └── build/
├── storage/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
├── database/
├── vite.config.js
├── tailwind.config.js
├── postcss.config.js
├── composer.json
├── package.json
├── .env.example
└── README.md
```

## How It Works

### 1. **AIProviderFactory** (`app/Services/AIProviderFactory.php`)

- Factory pattern for creating AI provider instances
- Dynamically selects provider based on `AI_PROVIDER` configuration
- Supports OpenAI, Google Gemini, OpenRouter, and Grok
- Validates provider configuration availability

**Supported Providers:**
- **OpenAI**: GPT-4o, GPT-4 Turbo, GPT-3.5 Turbo
- **Google Gemini**: Gemini 2.0 Flash, Gemini 2.5 Flash
- **OpenRouter**: Multi-provider access (Claude, Llama, etc.)
- **Grok**: Grok 2.0 via X.AI

### 2. **Provider Interface** (`app/Services/Providers/AIProviderInterface.php`)

- Standardized interface for all AI providers
- Methods: `generateSection()`, `getName()`, `isConfigured()`, `getModels()`, `setModel()`
- Ensures consistent behavior across different AI services

### 3. **SectionGeneratorService** (`app/Services/SectionGeneratorService.php`)

- Uses AIProviderFactory to get the configured provider
- Sends carefully crafted system prompt to generate sections
- Parses AI response into structured JSON
- Validates generated Shopify sections

**Key Features:**
- System prompt ensures Shopify-compatible output
- Handles API errors gracefully
- Includes 5 preset templates
- Provider-agnostic implementation

### 4. **SectionGeneratorController** (`app/Http/Controllers/SectionGeneratorController.php`)

- `index()` - Displays form with presets and available providers
- `generate()` - Processes user input and returns generated section
- Validates input (required, 10-2000 characters)
- Returns JSON response with error handling

### 5. **Blade View** (`resources/views/generator.blade.php`)

- Modern, responsive interface with Tailwind CSS
- Real-time character counter with 2000 char limit
- 5 quick preset buttons for instant templates
- Provider selector dropdown
- Loading state during generation
- Copy buttons for each output section
- Error message display with retry option
- Input preservation

## API Endpoints

### GET `/`
Shows the section generator form and presets.

**Response:** HTML page with Blade template

### POST `/generate`
Generates a Shopify section based on user input.

**Request:**
```json
{
  "prompt": "Create a testimonials section with a carousel showing customer reviews with star ratings..."
}
```

**Success Response (200):**
```json
{
  "section_name": "Testimonials Carousel",
  "schema": { ... },
  "liquid": "...",
  "css": "...",
  "input": "..."
}
```

**Error Response (400/422):**
```json
{
  "error": "Failed to generate section: API error message",
  "input": "..."
}
```

## Using Presets

The app includes 5 quick-start presets:

1. **❓ FAQ Section** - Collapsible FAQ items
2. **📍 Hero Banner** - Full-width hero with CTA
3. **🛍️ Featured Product** - Product display with add-to-cart
4. **⭐ Testimonials** - Customer review carousel
5. **🎯 Call-to-Action** - Simple CTA section

Click any preset to fill the textarea and generate that section type.

## Generated Output Format

The AI generates sections in this JSON format:

```json
{
  "section_name": "Section Name",
  "schema": {
    "name": "Section Name",
    "settings": [
      {
        "type": "text",
        "id": "heading",
        "label": "Heading"
      }
    ],
    "blocks": [
      {
        "type": "item",
        "name": "Item",
# Application
APP_NAME=Shopify Section Generator
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# AI Configuration
AI_PROVIDER=openai                           # Default: openai, gemini, openrouter, or grok
OPENAI_API_KEY=sk-your-key
OPENAI_MODEL=gpt-4o
GEMINI_API_KEY=your-gemini-key
GEMINI_MODEL=gemini-2.5-flash-lite
OPENROUTER_API_KEY=your-openrouter-key
OPENROUTER_MODEL=deepseek/deepseek-chat
GROK_API_KEY=your-grok-key
GROK_MODEL=grok-2-latest

# Database (SQLite by default)
DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
```

### Customizing AI Generation

Edit the `getSystemPrompt()` method in `SectionGeneratorService.php` to customize how the AI generates sections. The system prompt is provider-agnostic and works across all supported AI service
  "css": "section { ... }"
}
```

## Configuration

### `.env` Variables

```env
APP_NAME=Shopify Section Generator
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

OPENAI_API_KEY=sk-your-key
OPENAI_MODEL=gpt-4o
```

### Customizing the System Prompt

Edit the `getSystemPrompt()` method in `SectionGeneratorService.php` to customize how the AI generates sections.

## Frontend Features

- **Real-time Character Counter**: Shows character count with 2000 char limit
- **Loading Spinner**: Beautiful animated loader during generation
- **One-Click Copy**: Copy buttons with feedback animation
- **Preset Buttons**: Jump-start generation with templates
- **Error Display**: Clear error messages with retry option
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Syntax Highlighting**: Code blocks with dark theme

## Error Handling

The app gracefully handles:
- Missing OpenAI API key
- API rate limits
- Invalid API responses
- Network errors
- Validation errors
- Malformed JSON responses

## Development

### Running Tests

```bash
php artisan test
```

### Checking Code

```bash
php artisan tinker
```

### Debugging

Enable debug mode in `.env`:

```env
APP_DEBUG=true
```

## Deployment

### Prepare for Production

1. Set `.env` to production:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. Generate a new APP_KEY:
   ```bash
   php artisan key:generate
   ```

3. Cache configuration:
   ```bash
   php artisan config:cache
   php artisan route:cache
   ```

### Deploy to Server

- Upload files to your server
- Run `composer install --no-dev`
- Configure `.env` with production settings
- Set proper file permissions
- Use a production web server (nginx/Apache)

## Troubleshooting

### AI Provider Selection

The app automatically detects which providers are configured. To change providers:

1. **Set your preferred default provider** in `.env`:
   ```env
   AI_PROVIDER=openai
   ```

2. **Available Provider Options:**
   - `openai` - Most reliable, highest quality output (requires credits)
   - `gemini` - Fast and cost-effective, Google's latest models
   - `openrouter` - Multi-model access, flexible options
   - `grok` - Latest open-source based models

3. **Switching providers:** Simply update `AI_PROVIDER` in `.env` and restart the server.

### Common Issues

**"Provider not configured"**
- Ensure you've added the API key for your chosen provider in `.env`
- Check that `AI_PROVIDER` is set to a configured provider
- Restart the development server: `php artisan serve`

**"PHP is not recognized"**
- Install PHP or add it to your system PATH
- Download from: https://www.php.net/downloads

**"Composer not found"**
- Install Composer from: https://getcomposer.org/download/

**"API key not working"**
- Verify your API key is correct in `.env`
- Check that your account has credits/active subscription
- For OpenAI: https://platform.openai.com/account/usage/overview
- For Gemini: https://aistudio.google.com
- For OpenRouter: https://openrouter.ai/account/usage
- For Grok: https://console.x.ai

**"Cannot connect to AI service"**
- Check your internet connection
- Verify API endpoint is reachable
- Check firewall/proxy settings
- Try a different provider as backup

## Customization Ideas

- 🔄 Add provider switcher in UI (allow users to choose AI provider per request)
- 🎨 Add CSS framework integration (Tailwind presets, Bootstrap themes)
- 💾 Save generated sections to database
- 📤 Export sections to file (JSON, ZIP, Liquid files)
- 🔐 Add user authentication and section history
- 🌙 Add dark/light theme toggle
- 📊 Add usage analytics and API cost tracking
- 🎯 Add advanced prompt options (style, complexity, section type)
- 🧪 Add section preview/live testing before export
- 🔗 Add section versioning and comparison
- 📚 Add documentation generation for sections

## Performance Tips

- OpenAI API calls take 2-5 seconds
- Consider implementing request timeout (30s)
- Add rate limiting for production
- Cache presets for instant access

## Security

- The app validates all user input
- CSRF protection enabled (Laravel default)
- API key stored in `.env` (not committed to git)
- No user data is stored
- JSON responses are properly encoded

## License

MIT License - Feel free to use and modify

## Support

For issues with:
- **Laravel**: https://laravel.com/docs
- **Blade**: https://laravel.com/docs/blade
- **OpenAI API**: https://platform.openai.com/docs

---

**Built with ❤️ Using Laravel 11 and Blade**
