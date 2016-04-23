var elixir = require('laravel-elixir');
require('laravel-elixir-livereload');
/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    //mix.sass('app.scss');
    //mix.less('app.less', 'public/css/app.css');
    mix.less('*.less', 'public/css/app.css');
		//mix.scriptsIn('public/js/pws', 'public/js/pwsapp.js');
		mix.scripts([
				'pwsnamespace.js', 
				'pwscore.js', 
				'pwsrender.js',
				'pwsworker.js',
				'pwsworkercollection.js'
		], 'public/js/pwsapp.js');
		mix.livereload();

});



