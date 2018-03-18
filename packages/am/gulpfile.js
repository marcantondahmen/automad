var gulp = require('gulp'),
	cleanCSS = require('gulp-clean-css'),
	concat = require('gulp-concat'),
	header = require('gulp-header'),
	merge2 = require('merge2'),
	less = require('gulp-less'),
	rename = require('gulp-rename'),
	uglify = require('gulp-uglify'),
	gutil = require('gulp-util'),
	cleanCSSOptions = {
		format: { wrapAt: 500 },
		rebase: false
	};
	
	
// Error handling to prevent watch task to fail silently without restarting.
var onError = function(err) {
		gutil.log(gutil.colors.red('ERROR', err.plugin), err.message);
		gutil.beep();
		new gutil.PluginError(err.plugin, err, {showStack: true})
		this.emit('end');
	};
	

// Concat minify and prefix all required js files.
gulp.task('am-js', function() {
	
	var	uglifyOptions = { 
			compress: { 
				hoist_funs: false, 
				hoist_vars: false 
			},
			output: {
				comments: /(license|copyright)/i,
				max_line_len: 500
			}
		},
		pkgUIkit = require('../../lib/vendor/uikit/uikit/package.json');
	
	return 	merge2(
			gulp.src([
				'node_modules/jquery/dist/jquery.min.js',
				'node_modules/imagesloaded/imagesloaded.pkgd.min.js',
				'node_modules/masonry-layout/dist/masonry.pkgd.min.js'
			]),
			gulp.src([
				// Core. 
				// Order of files taken from lib/vendor/uikit/uikit/gulpfile.js
				'../../lib/vendor/uikit/uikit/src/js/core/core.js',
				'../../lib/vendor/uikit/uikit/src/js/core/touch.js',
				'../../lib/vendor/uikit/uikit/src/js/core/utility.js',
				'../../lib/vendor/uikit/uikit/src/js/core/smooth-scroll.js',
				'../../lib/vendor/uikit/uikit/src/js/core/scrollspy.js',
				'../../lib/vendor/uikit/uikit/src/js/core/toggle.js',
				'../../lib/vendor/uikit/uikit/src/js/core/alert.js',
				'../../lib/vendor/uikit/uikit/src/js/core/button.js',
				'../../lib/vendor/uikit/uikit/src/js/core/dropdown.js',
				'../../lib/vendor/uikit/uikit/src/js/core/grid.js',
				'../../lib/vendor/uikit/uikit/src/js/core/modal.js',
				'../../lib/vendor/uikit/uikit/src/js/core/nav.js',
				'../../lib/vendor/uikit/uikit/src/js/core/offcanvas.js',
				'../../lib/vendor/uikit/uikit/src/js/core/switcher.js',
				'../../lib/vendor/uikit/uikit/src/js/core/tab.js',
				'../../lib/vendor/uikit/uikit/src/js/core/cover.js',
				// Selected components.
				'../../lib/vendor/uikit/uikit/src/js/components/autocomplete.js',
				'../../lib/vendor/uikit/uikit/src/js/components/lightbox.js',
				'../../lib/vendor/uikit/uikit/src/js/components/pagination.js',
				'../../lib/vendor/uikit/uikit/src/js/components/slider.js',
				'../../lib/vendor/uikit/uikit/src/js/components/slideshow.js',
				'../../lib/vendor/uikit/uikit/src/js/components/sticky.js',
				'../../lib/vendor/uikit/uikit/src/js/components/tooltip.js'
			])
			.pipe(uglify(uglifyOptions))
			.pipe(concat('uikit.js', { newLine: '\r\n\r\n' } )) // Doesn't get saved to disk.
			.pipe(header('/*! <%= pkg.title %> <%= pkg.version %> | <%= pkg.homepage %> | (c) 2014 YOOtheme | MIT License */\n', { 'pkg' : pkgUIkit } )),
			gulp.src('js/*.js')
			.pipe(uglify(uglifyOptions))
		)
		.pipe(concat('am.min.js', { newLine: '\r\n\r\n' } ))
		.pipe(gulp.dest('dist'));
	
});	
	
// Compile, minify and prefix one.less.
gulp.task('am-one-less', function() {

	return 	gulp.src('one/less/one.less')
			.pipe(less())
			.on('error', onError)
			.pipe(cleanCSS(cleanCSSOptions))
			.pipe(rename({ prefix: 'am.', suffix: '.min' }))
			.pipe(gulp.dest('one/dist'));
	
});

// Compile, minify and prefix two.less.
gulp.task('am-two-less', function() {

	return 	gulp.src('two/less/two.less')
			.pipe(less())
			.on('error', onError)
			.pipe(cleanCSS(cleanCSSOptions))
			.pipe(rename({ prefix: 'am.', suffix: '.min' }))
			.pipe(gulp.dest('two/dist'));
	
});


// Watch task.
gulp.task('watch', function() {

	gulp.watch('one/less/*.less', ['am-one-less']);
	gulp.watch('two/less/*.less', ['am-two-less']);
	gulp.watch('js/*.js', ['am-js']);
	
});


// The default task.
gulp.task('default', ['am-js', 'am-one-less', 'am-two-less']);