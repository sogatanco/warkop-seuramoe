const mix = require('laravel-mix');

// Konfigurasi React
mix.react('react-app/src/index.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');