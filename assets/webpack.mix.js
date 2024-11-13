// webpack.mix.js

let mix = require('laravel-mix');

mix.ts( 'src/js/script.ts', 'build/js' )
    .ts( 'src/js/notify.ts', 'build/js' )


    .minify('build/js/script.js')
    
    .sass('src/scss/style.scss', 'build/css')
    .minify('build/css/style.css').autoload({
        jquery: ['$', 'window.jQuery', 'jQuery'],
     });
