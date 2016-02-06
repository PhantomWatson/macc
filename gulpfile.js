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

function testNotification(status, pluginName, override) {
    var options = {
        title:   ( status == 'pass' ) ? 'Tests Passed' : 'Tests Failed',
        message: ( status == 'pass' ) ? '\n\nAll tests have passed!\n\n' : '\n\nOne or more tests failed...\n\n',
        icon:    __dirname + '/node_modules/gulp-' + pluginName +'/assets/test-' + status + '.png'
    };
    options = _.merge(options, override);
    return options;
}

gulp.task('php_unit', function() {
    gulp.src('phpunit.xml')
        .pipe(phpunit('', {notify: true}))
        .on('error', notify.onError(testNotification('fail', 'phpunit')))
        .pipe(notify(testNotification('pass', 'php_unit')));
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
        .pipe(gulp.dest('webroot/bootstrap/fonts'));
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
	
    // Vendor files
    var vendorFiles = [
        'vendor/twbs/bootstrap/dist/fonts/*',
        'vendor/twbs/bootstrap/dist/js/*'
    ];
    gulp.watch(vendorFiles, ['copy_vendor_files']);
});