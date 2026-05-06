# Shopify Section Generator - Build Setup Guide

## Frontend Setup with Vite, Tailwind CSS, and npm

This project now uses a modern build toolchain for asset management:

### Technologies
- **Vite** - Lightning-fast build tool and dev server
- **Tailwind CSS** - Utility-first CSS framework
- **PostCSS** - CSS processing with autoprefixer
- **npm** - Package manager

## Installation

### 1. Install Dependencies

```bash
npm install
```

This installs:
- `vite` - Build tool and dev server
- `tailwindcss` - CSS framework
- `postcss` & `autoprefixer` - CSS processing
- `laravel-vite-plugin` - Laravel integration
- `axios` - HTTP client (optional, for AJAX)

## Development

### Development Server
Run both Laravel and Vite dev servers:

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite dev server (hot module reload)
npm run dev
```

The Vite dev server provides:
- ⚡ **Hot Module Reloading (HMR)** - Instant CSS/JS updates
- 🚀 **Fast refresh** - See changes instantly without full page reload
- 📦 **Optimized dev builds** - Fast compilation

### Production Build

```bash
npm run build
```

This generates optimized, minified assets in `public/build/`:
- `assets/app-[hash].css` - Compiled CSS with Tailwind
- `assets/app-[hash].js` - Compiled JavaScript
- `manifest.json` - Asset manifest for Laravel Vite plugin

## Project Structure

```
shopify-generator/
├── resources/
│   ├── css/
│   │   └── app.css              # Tailwind directives & custom CSS
│   ├── js/
│   │   └── app.js               # JavaScript entry point
│   └── views/
│       └── generator.blade.php   # Blade template with @vite()
├── public/
│   └── build/                   # Built assets (production)
├── vite.config.js              # Vite configuration
├── tailwind.config.js          # Tailwind configuration
├── postcss.config.js           # PostCSS configuration
├── package.json                # npm dependencies
└── package-lock.json           # Locked versions

```

## Asset Compilation

### What Vite Does

**CSS Processing:**
1. Takes `resources/css/app.css`
2. Processes Tailwind directives (`@tailwind base`, `@components`, `@utilities`)
3. PostCSS adds vendor prefixes (autoprefixer)
4. Minifies for production

**JavaScript:**
1. Takes `resources/js/app.js`
2. Bundles dependencies
3. Minifies for production

### Using Assets in Blade

The `@vite()` directive in `resources/views/generator.blade.php`:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

This:
- Includes CSS and JS in the page
- In development: Uses HMR for instant updates
- In production: Points to hashed, minified files

## Tailwind CSS Customization

### Tailwind Configuration

Edit `tailwind.config.js` to:

```javascript
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        // Add custom colors
        shopify: '#96be24',
      },
      fontFamily: {
        // Add custom fonts
        sans: ['Poppins', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
```

### Custom CSS

Add custom styles to `resources/css/app.css`:

```css
@layer components {
  .btn-primary {
    @apply px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700;
  }
}
```

## CSS & JavaScript Files

### resources/css/app.css
- Tailwind base directives
- Component layer definitions
- Utility layer overrides
- Custom animations and styles

### resources/js/app.js
- Entry point for JavaScript
- Global event listeners
- Reusable functions exposed to window
- Can import other modules

## npm Scripts

```json
{
  "dev": "vite",           // Start dev server with HMR
  "build": "vite build",   // Build for production
  "preview": "vite preview" // Preview production build locally
}
```

## Performance Tips

### Development
- Vite provides instant HMR during development
- Changes to CSS are reflected without page reload
- JS changes trigger fast refresh

### Production
- Minified CSS (~4.3 KB gzipped)
- Minified JS (~0.4 KB gzipped)
- Asset hashing prevents caching issues
- Manifest file for Laravel integration

## Troubleshooting

### Tailwind CSS not updating
1. Ensure `content` paths in `tailwind.config.js` are correct
2. Run `npm run build` to rebuild
3. Clear browser cache

### HMR not working in dev
1. Ensure `npm run dev` is running in a separate terminal
2. Laravel should be served on the correct host/port
3. Check `APP_URL` in `.env`

### Build fails
Run `npm audit fix --force` to update vulnerable packages

## Adding New npm Packages

```bash
# Install a new package
npm install package-name

# Install as dev dependency
npm install --save-dev package-name

# Rebuild assets
npm run build
```

## GitIgnore

The following are already in `.gitignore`:
- `/node_modules/` - npm packages (don't commit)
- `/public/build` - Built assets (regenerate on deploy)
- `/.env` - Environment variables

Build assets regenerated on each deploy via `npm run build`.
