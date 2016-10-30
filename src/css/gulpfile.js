const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

// Link for the javascript
elixir(mix => {
    mix.sass('app.scss')
       .webpack('app.js');
});

// Link for the bootstrap scss
elixir(function(mix) {
  mix.sass('app.scss')
    .version('css/app.css');
});
