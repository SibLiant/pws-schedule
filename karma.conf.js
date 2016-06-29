// Karma configuration
// Generated on Sun Mar 13 2016 22:37:55 GMT-0400 (EDT)

module.exports = function(config) {

  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',


    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['jasmine'],


    // list of files / patterns to load in the browser
    files: [
		'public/js/jquery-2.2.0.min.js',
		'public/js/moment.2.11.1.js',
		'public/js/underscore_1.8.3.js',
		'public/js/jquery-ui-1.11.4.custom/jquery-ui.js',

		//'public/js/pwsapp.js',


		'resources/assets/js/pwsnamespace.js',
		'resources/assets/js/pwscore.js',
		'resources/assets/js/pwsworker.js',
		'resources/assets/js/pwsrender.js',
		'spec/javascripts/jasmine-jquery.js',
		//'spec/javascripts/jasmine-fixture.js',


		'public/js/tests/jasmine/core.js'
    ],


    // list of files to exclude
    exclude: [
    ],

    // preprocess matching files before serving them to the browser
    // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
    preprocessors: {
		//'public/js/pwsapp.js': 'coverage'

		'resources/assets/js/pwsnamespace.js': 'coverage',
		'resources/assets/js/pwscore.js': 'coverage',
		'resources/assets/js/pwsworker.js': 'coverage',
		'resources/assets/js/pwsrender.js': 'coverage'
    },


    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['progress', 'coverage'],


    // web server port
    port: 9876,


    // enable / disable colors in the output (reporters and logs)
    colors: true,


    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_INFO,


    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,


    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    //browsers: ['Chrome', 'Chrome_without_security'],
    browsers: ['Chrome'],
 
    // you can define custom flags 
    customLaunchers: {
      Chrome_without_security: {
        base: 'Chrome',
        flags: ['--allow-file-access-from-files']
      }
    },


    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false,

    // Concurrency level
    // how many browser should be started simultaneous
    concurrency: Infinity
  })
}
