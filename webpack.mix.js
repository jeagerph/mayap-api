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

mix.sass('resources/scss/documents/default/default.scss', 'public/css/documents');

mix.sass('resources/scss/identifications/default/default.scss', 'public/css/identifications');

mix.sass('resources/scss/reports/default.scss', 'public/css/reports');