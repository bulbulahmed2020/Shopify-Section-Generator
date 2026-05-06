# Shopify Section Generator - Development Guide

## Quick Start

```bash
# 1. Install dependencies
composer install

# 2. Setup environment
cp .env.example .env
# Edit .env and add OPENAI_API_KEY

# 3. Generate key
php artisan key:generate

# 4. Run server
php artisan serve

# Visit: http://localhost:8000
```

## Architecture

### Service Layer: SectionGeneratorService
Handles all OpenAI API communication:
- Sends structured prompts to OpenAI
- Parses JSON responses
- Validates generated sections
- Provides preset templates

### Controller: SectionGeneratorController
- Routes requests to the service
- Validates user input
- Returns JSON responses
- Handles errors

### View: generator.blade.php
- Form for section description
- Preset buttons
- Output display sections
- Copy buttons
- Loading state

## Key Technologies

- **Framework**: Laravel 11
- **Templating**: Blade
- **Frontend**: Vanilla JS + Tailwind CSS
- **API**: OpenAI official PHP client
- **Validation**: Laravel validation rules

## File Locations

```
- Controller: app/Http/Controllers/SectionGeneratorController.php
- Service: app/Services/SectionGeneratorService.php
- View: resources/views/generator.blade.php
- Routes: routes/web.php
- Config: config/app.php
```

## System Prompt Engineering

The AI system prompt in SectionGeneratorService ensures:
1. Valid Shopify JSON schema structure
2. Configurable blocks and settings
3. Production-ready Liquid code
4. Optional CSS styling

Modify the `getSystemPrompt()` method to change AI behavior.

## Adding New Presets

1. Edit `getPresets()` in SectionGeneratorService
2. Add new array item with:
   - `id`: Unique identifier
   - `label`: Display name with emoji
   - `description`: Short description
   - `prompt`: Full AI prompt for this preset

## Frontend Interactivity

### Copy to Clipboard
Handles copying code blocks with visual feedback.

### Preset Buttons
Fill textarea with preset prompts.

### Loading State
Shows spinner during API call.

### Error Display
Shows user-friendly error messages.

## API Response Flow

1. User submits form
2. JavaScript sends POST to /generate
3. Controller validates input
4. Service calls OpenAI API
5. Response parsed to JSON
6. Results displayed in browser
7. User can copy sections

## Testing Manually

Use your browser's Developer Console:

```javascript
// Test API call
fetch('/generate', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  body: JSON.stringify({
    prompt: 'Create a simple banner section'
  })
})
.then(r => r.json())
.then(console.log)
```

## Customization Points

1. **System Prompt** - Change `getSystemPrompt()` in Service
2. **Presets** - Add/edit in `getPresets()` method
3. **Validation** - Modify `generate()` validation rules
4. **UI** - Edit `generator.blade.php`
5. **Error Handling** - Add custom logic in Service catch blocks

## Performance Considerations

- OpenAI API calls: ~2-5 seconds
- Response parsing: ~100ms
- Frontend rendering: <500ms
- Total UX latency: ~2-6 seconds

For faster response, consider:
- Caching common presets
- Using faster OpenAI model (gpt-3.5-turbo)
- Request timeout: 30 seconds max

## Security Notes

- ✅ CSRF protected (Laravel default)
- ✅ Input validated server-side
- ✅ API key in .env (not versioned)
- ✅ JSON response encoding
- ✅ No direct file system access

## Common Issues & Fixes

| Issue | Solution |
|-------|----------|
| 500 error | Check `.env` OPENAI_API_KEY is set |
| Timeout | Increase Laravel timeout setting |
| JSON errors | Validate response format |
| CORS errors | Check request headers |
| Validation fails | Ensure prompt is 10+ characters |

## Next Steps

- Add database to save sections
- Add user authentication
- Implement batch generation
- Add advanced prompt options
- Create CLI commands for testing
- Add unit tests
- Deploy to production server

---

Happy coding! 🚀
