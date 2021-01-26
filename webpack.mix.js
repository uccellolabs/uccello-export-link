const mix = require('laravel-mix')

const autoload = {
   jquery: [ '$', 'jQuery', 'jquery']
}
mix.autoload(autoload)

mix.setPublicPath('public')

mix.js('./resources/js/script.js', 'public/js')
   .version()
//    .after(stats => {
//        console.log(stats)

//     });

mix.after(webpackStats => {
    // Copy all compiled files into main project (auto publishing)
    // mix.copyDirectory('public', 'public/vendor/uccello/url-export');
    mix.copyDirectory('public', 'tata');
});
