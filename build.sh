#!/bin/bash
# Vercel build script

# Build frontend
npm run build

# Generate clean packages.php without dev dependencies
# (dev packages like pail, sail, collision are removed by Vercel's --no-dev)
cat > bootstrap/cache/packages.php << 'PHPEOF'
<?php return array (
  'barryvdh/laravel-dompdf' =>
  array (
    'aliases' =>
    array (
      'PDF' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
      'Pdf' => 'Barryvdh\\DomPDF\\Facade\\Pdf',
    ),
    'providers' =>
    array (
      0 => 'Barryvdh\\DomPDF\\ServiceProvider',
    ),
  ),
  'inertiajs/inertia-laravel' =>
  array (
    'providers' =>
    array (
      0 => 'Inertia\\ServiceProvider',
    ),
  ),
  'laravel/tinker' =>
  array (
    'providers' =>
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'nesbot/carbon' =>
  array (
    'providers' =>
    array (
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ),
  ),
);
PHPEOF

# Copy static assets to Vercel output directory
mkdir -p .vercel_static/build
cp -r public/build/* .vercel_static/build/
cp public/favicon.ico .vercel_static/ 2>/dev/null
cp public/robots.txt .vercel_static/ 2>/dev/null
true
