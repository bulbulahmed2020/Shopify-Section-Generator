# Shopify Section Generator 🛍️

A minimal but clean MVP web app built with **Laravel** and **Blade templates** that generates Shopify sections using AI.

## Features

✨ **AI-Powered Generation**: Uses OpenAI to generate Shopify sections based on user descriptions  
📋 **Complete Output**: Generates section name, schema, Liquid code, and CSS  
⚡ **Quick Presets**: 5 pre-built preset templates (FAQ, Banner, Product, Testimonials, CTA)  
📋 **Easy Copy**: One-click copy buttons for all generated code  
🎨 **Clean UI**: Modern, responsive interface with Tailwind CSS  
✅ **Input Validation**: Ensures quality prompts with client and server validation  
🚨 **Error Handling**: Graceful error messages for API failures  

## Prerequisites

- **PHP 8.1+**
- **Composer**
- **Node.js & npm** (optional, for frontend)
- **OpenAI API Key**

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

Edit `.env` and add your OpenAI API key:

```env
OPENAI_API_KEY=sk-your-api-key-here
OPENAI_MODEL=gpt-4o
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
│   │   └── Controllers/
│   │       └── SectionGeneratorController.php
│   └── Services/
│       └── SectionGeneratorService.php
├── config/
│   └── app.php
├── resources/
│   └── views/
│       └── generator.blade.php
├── routes/
│   └── web.php
├── public/
│   └── index.php
├── bootstrap/
│   └── app.php
├── composer.json
└── .env
```

## How It Works

### 1. **SectionGeneratorService** (`app/Services/SectionGeneratorService.php`)

- Communicates with OpenAI API
- Sends a carefully crafted system prompt
- Parses the AI response into structured JSON
- Validates the generated section

**Key Features:**
- System prompt ensures Shopify-compatible output
- Handles API errors gracefully
- Includes 5 preset templates

### 2. **SectionGeneratorController** (`app/Http/Controllers/SectionGeneratorController.php`)

- `index()` - Displays the form with presets
- `generate()` - Processes user input and returns generated section
- Validates input (required, 10-2000 characters)
- Returns JSON response

### 3. **Blade View** (`resources/views/generator.blade.php`)

- Modern, responsive interface
- Real-time character counter
- 5 quick preset buttons
- Loading state during generation
- Copy buttons for each output section
- Error message display
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
        "settings": [...]
      }
    ],
    "presets": [
      {
        "name": "Default",
        "settings": {},
        "blocks": []
      }
    ]
  },
  "liquid": "{% section 'section-name' %}\n...",
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

### "PHP is not recognized"
- Install PHP or add it to your system PATH
- Download from: https://www.php.net/downloads

### "Composer not found"
- Install Composer from: https://getcomposer.org/download/

### "OpenAI API key not working"
- Verify your API key in `.env`
- Check your OpenAI account has credits
- Use correct model name (gpt-4o, gpt-4-turbo, gpt-3.5-turbo)

### "Cannot connect to OpenAI"
- Check your internet connection
- Verify OpenAI API endpoint is reachable
- Check firewall/proxy settings

## Customization Ideas

-🎨 Add CSS framework integration (Tailwind, Bootstrap)
- 🔄 Add section regeneration options
- 💾 Save generated sections to database
- 📤 Export sections to file (JSON, ZIP)
- 🔐 Add user authentication
- 🌙 Add dark/light theme toggle
- 📊 Add usage analytics
- 🎯 Add advanced prompt options (style, complexity)

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

**Built with ❤️ Using Laravel and Blade**
