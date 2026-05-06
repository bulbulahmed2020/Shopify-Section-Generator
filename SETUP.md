# 🚀 Quick Setup Guide - Shopify Section Generator

## What Was Built

A complete Laravel MVP web app that uses AI to generate production-ready Shopify sections!

**Key Files Created:**

```
shopify-generator/
├── app/
│   ├── Http/Controllers/SectionGeneratorController.php    ✅ Routes & Logic
│   └── Services/SectionGeneratorService.php               ✅ OpenAI Integration
├── resources/
│   └── views/generator.blade.php                          ✅ Beautiful UI
├── routes/web.php                                         ✅ Route definitions
├── config/app.php                                         ✅ Configuration
├── public/index.php                                       ✅ Entry point
├── bootstrap/app.php                                      ✅ App bootstrap
├── composer.json                                          ✅ Dependencies
├── .env                                                   ⚙️ Environment setup
├── .env.example                                           📋 Template
├── README.md                                              📚 Full documentation
└── DEVELOPMENT.md                                         🛠️ Dev guide
```

## Features Included

✨ **AI-Powered Generation**
- Uses OpenAI API (GPT-4o)
- Returns schema, Liquid code, and CSS

📋 **Complete Output**
- Section Name
- Shopify Schema (JSON)
- Liquid Code
- CSS Styling (optional)

⚡ **5 Quick Presets**
1. FAQ Section
2. Hero Banner
3. Featured Product
4. Testimonials Carousel
5. Call-to-Action

🎨 **Beautiful UI**
- Modern responsive design
- Tailwind CSS styling
- One-click copy buttons
- Loading animations
- Error handling

✅ **Full Validation**
- Client-side validation
- Server-side validation
- 10-2000 character requirements
- Error messages

## Installation Steps

### 1️⃣ Install Composer Dependencies

```bash
cd c:\Vibe-Coding\shopify-generator
composer install
```

> If composer not installed, download from https://getcomposer.org/download/

### 2️⃣ Configure Environment

```bash
# Copy template
copy .env.example .env
```

Edit `.env` and update:
```env
OPENAI_API_KEY=sk-your-actual-api-key-here
OPENAI_MODEL=gpt-4o
```

Get your API key from: https://platform.openai.com/api-keys

### 3️⃣ Generate Application Key

```bash
php artisan key:generate
```

### 4️⃣ Start Development Server

```bash
php artisan serve
```

Output will show:
```
Laravel development server started: http://127.0.0.1:8000
```

### 5️⃣ Open in Browser

Visit: **http://localhost:8000**

## How to Use

### Via Form Input

1. Enter a description (10+ characters)
   - Example: "Create a testimonials section with carousel and star ratings"
2. Click "✨ Generate Section"
3. Wait for AI to generate (2-5 seconds)
4. Copy any section using the 📋 buttons

### Via Presets

1. Click any preset button (FAQ, Banner, etc)
2. Textarea fills with that preset's prompt
3. Click "✨ Generate Section"
4. Get instant results

## What Each Generated Item Does

**Section Name**
- The recommended name for your Shopify section
- Use this when creating `section-name.liquid`

**Schema (JSON)**
- Shopify theme editor configuration
- Defines settings, blocks, and presets
- Copy into your section's `{% schema %}...{% endschema %}`

**Liquid Code**
- The HTML/Liquid template
- Copy into the main section file
- Use with `{{ section.settings.xxx }}` for dynamic content

**CSS (Optional)**
- Styling for the section
- Copy into within `<style>` tags in the section
- Or move to your theme's assets

## Customization

### Change System Prompt

Edit `SectionGeneratorService.php`, method `getSystemPrompt()`:

```php
private function getSystemPrompt(): string
{
    return <<<'PROMPT'
    // Your custom system prompt here
    PROMPT;
}
```

### Add More Presets

Edit `getPresets()` method in `SectionGeneratorService.php`:

```php
[
    'id' => 'my-preset',
    'label' => '🎨 My Custom Preset',
    'description' => 'My description',
    'prompt' => 'Your AI prompt here',
]
```

### Modify UI

Edit `resources/views/generator.blade.php`:
- Change colors (Tailwind classes)
- Add/remove form fields
- Customize output display

## Troubleshooting

### "php: command not found"
**Fix:** Install PHP from https://www.php.net/downloads

### "Composer not found"
**Fix:** Install Composer from https://getcomposer.org/download/

### "OPENAI_API_KEY not set"
**Fix:** Add your OpenAI key to `.env`:
```env
OPENAI_API_KEY=sk-xxxxxxxxxxxxxxxx
```

### "Connection timeout"
**Fix:** OpenAI API might be slow. Try:
- Use `gpt-3.5-turbo` instead of `gpt-4o` (faster/cheaper)
- Check internet connection
- Increase timeout in Laravel config

### "Invalid JSON response"
**Fix:** The AI response was malformed. Try:
- Reword your prompt
- Be more specific in description
- Use English for best results

## File Locations Reference

| File | Purpose |
|------|---------|
| `app/Services/SectionGeneratorService.php` | OpenAI API calls & parsing |
| `app/Http/Controllers/SectionGeneratorController.php` | Request handling & validation |
| `resources/views/generator.blade.php` | Frontend UI & JavaScript |
| `routes/web.php` | Route definitions (GET `/`, POST `/generate`) |
| `config/app.php` | App configuration & env vars |
| `.env` | Environment variables (keep secret!) |

## Development Inside VS Code

Open VS Code at the project folder:

```bash
code c:\Vibe-Coding\shopify-generator
```

Then start the server in integrated terminal:

```bash
php artisan serve
```

Visit http://localhost:8000 in your browser!

## Environment Variables Explained

```env
APP_NAME              # App name shown in browser
APP_ENV               # local = dev, production = live
APP_DEBUG             # true = show errors, false = hide
APP_URL               # Where app is hosted
OPENAI_API_KEY        # Your OpenAI API key (SECRET!)
OPENAI_MODEL          # AI model: gpt-4o, gpt-4-turbo, gpt-3.5-turbo
```

## API Endpoints

```
GET  /                    → Show form
POST /generate            → Generate section (requires JSON)
```

### Example POST Request

```bash
curl -X POST http://localhost:8000/generate \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: token-here" \
  -d '{"prompt": "Create a hero banner section"}'
```

## Performance Expected

- **Generation Time**: 2-5 seconds (depends on API)
- **UI Response**: Instant (<100ms)
- **Copy to Clipboard**: <50ms
- **Total UX**: ~2-6 seconds

## Next Steps

After setup, you can:

1. **Test Generation**
   - Enter various descriptions
   - Try the preset buttons
   - Copy and review generated code

2. **Customize**
   - Modify system prompt for better outputs
   - Add more presets
   - Change UI styling

3. **Deploy**
   - Upload to web server
   - Point domain to `public/` folder
   - Set `.env` to production mode

4. **Extend**
   - Add database to save sections
   - Add user authentication
   - Add section preview
   - Add batch generation

## Support & Resources

- **Laravel Docs**: https://laravel.com/docs
- **Blade Syntax**: https://laravel.com/docs/blade
- **OpenAI API**: https://platform.openai.com/docs
- **Shopify Schema**: https://shopify.dev/themes/architecture

## What's Next?

Your app is ready to use! Here are ideas to enhance it:

- 📱 Add mobile preview
- 💾 Save generated sections to database
- 🔄 Regenerate with different settings
- 📤 Export as ZIP/JSON
- 🌙 Dark mode toggle
- 🚀 Deploy to hosting platform

---

**🎉 You now have a working Shopify Section Generator!**

Start by visiting: http://localhost:8000

Enjoy! 🚀
