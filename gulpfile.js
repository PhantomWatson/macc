var gulp = require('gulp');
var less = require('gulp-less');
var watchLess = require('gulp-watch-less');
var plumber = require('gulp-plumber');
var notify = require("gulp-notify");
var LessPluginCleanCSS = require('less-plugin-clean-css');
var uglify = require('gulp-uglify');
var rename = require("gulp-rename");
var jshint = require('gulp-jshint');
var stylish = require('jshint-stylish');
var phpcs = require('gulp-phpcs');
var phpunit = require('gulp-phpunit');
var _ = require('lodash');
var runSequence = require('run-sequence');

gulp.task('default', ['less', 'js', 'php', 'watch']);



/**************
 *    PHP     *
 **************/

gulp.task('php', function(callback) {
	return runSequence('php_cs', 'php_unit', callback);
});

gulp.task('php_cs', function() {
	return gulp.src(['src/**/*.php'])
		// Validate files using PHP Code Sniffer
		.pipe(phpcs({
			bin: 'C:/xampp/htdocs/macc/vendor/bin/phpcs.bat',
			standard: 'PSR2',
			warningSeverity: 0
		}))
		// Log all problems that was found
		.pipe(phpcs.reporter('log'));
});

gulp.task('php_unit', function() {
    gulp.src('phpunit.xml')
        .pipe(phpunit('', {notify: true}));
});

/**************
 * Javascript *
 **************/
var srcJsFiles = [ 
    'webroot/js/script.js'
];

gulp.task('js', function(callback) {
	return runSequence('js_lint', callback);
});

gulp.task('js_lint', function () {
	return gulp.src(srcJsFiles)
    	.pipe(jshint())
        .pipe(jshint.reporter(stylish))
        .pipe(notify('JS linted'));
});



/****************
 * VENDOR FILES *
 ****************/
gulp.task('copy_vendor_files', function () {
    gulp.src('vendor/twbs/bootstrap/dist/fonts/*')
        .pipe(gulp.dest('webroot/fonts'));
    gulp.src('vendor/twbs/bootstrap/dist/js/*')
    .pipe(gulp.dest('webroot/bootstrap/js'));
});



/**************
 *    LESS    *
 **************/
gulp.task('less', function () {
	var cleanCSSPlugin = new LessPluginCleanCSS({advanced: true});
	gulp.src('webroot/css/style.less')
		.pipe(less({plugins: [cleanCSSPlugin]}))
        .pipe(gulp.dest('webroot/css'))
        .pipe(notify('LESS compiled'));
});



/**************
 *  Watching  *
 **************/

gulp.task('watch', function() {
    // LESS
	var cleanCSSPlugin = new LessPluginCleanCSS({advanced: true});
	watchLess('webroot/css/style.less', ['less'])
    	.pipe(less({plugins: [cleanCSSPlugin]}))
        .pipe(gulp.dest('webroot/css'))
        .pipe(notify('LESS compiled'));

	// PHP
	gulp.watch('src/**/*.php', ['php_cs', 'php_unit']);
	gulp.watch('src/**/*.ctp', ['php_unit']);
	
	// JavaScript
    gulp.watch(srcJsFiles, ['js']);
	
    // Vendor files
    var vendorFiles = [
        'vendor/twbs/bootstrap/dist/fonts/*',
        'vendor/twbs/bootstrap/dist/js/*'
    ];
    gulp.watch(vendorFiles, ['copy_vendor_files']);
});
