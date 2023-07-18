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

mix.setPublicPath('public_html');

mix.sass('resources/assets/sass/app.scss', 'public_html/admin_dashboard/css/app-23-07-18r2.css');

mix.sass('resources/assets/sass/tinymce-extras.scss', 'public_html/admin_dashboard/css/tinymce-extras.css');

/*mix.coffee([
    'resources/assets/coffee/dropzone.coffee'
], 'resources/assets/js/define/compiled_coffee.js');*/

mix.babel([
    'resources/assets/js/requirejs-config.js',
    'resources/assets/js/init.js',
    'resources/assets/js/define/*.js',
    'resources/assets/js/require/*.js'
], 'public_html/admin_dashboard/js/all-23-07-18r2.js');
