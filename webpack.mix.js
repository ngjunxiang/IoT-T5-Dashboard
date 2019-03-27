const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.copyDirectory('resources/js', 'public/js')
   .copyDirectory('resources/css', 'public/css')
   .copyDirectory('resources/images', 'public/images')
   .copyDirectory('resources/vendor', 'public/vendor')
   .copyDirectory('resources/fonts', 'public/fonts');