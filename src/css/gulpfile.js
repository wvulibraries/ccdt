var elixir = require('laravel-elixir');

// Create a version for each scss change and link it at css/app.css 
 elixir(function(mix) {
     mix.sass('app.scss')
        .version('css/app.css');
 });
