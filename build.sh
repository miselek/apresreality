#!/bin/bash
# Vercel build script

# Clear cached package manifest (dev packages not available on Vercel)
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php

# Build frontend
npm run build

# Copy static assets to Vercel output directory
mkdir -p .vercel_static/build
cp -r public/build/* .vercel_static/build/
cp public/favicon.ico .vercel_static/ 2>/dev/null
cp public/robots.txt .vercel_static/ 2>/dev/null
true
