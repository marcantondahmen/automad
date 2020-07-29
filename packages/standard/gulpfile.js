/*
 *	Standard
 *
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *  MIT license
 */


var gulp = require('gulp'),
	autoprefixer = require('gulp-autoprefixer'),
	cleanCSS = require('gulp-clean-css'),
	concat = require('gulp-concat'),
	header = require('gulp-header'),
	merge2 = require('merge2'),
	less = require('gulp-less'),
	rename = require('gulp-rename'),
	uglify = require('gulp-uglify-es').default,
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
gulp.task('standard-js', function() {
	
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
				'node_modules/imagesloaded/imagesloaded.pkgd.min.js'
			]),
			gulp.src([
				// Core. 
				// Order of files taken from lib/vendor/uikit/uikit/gulpfile.js
				'../../lib/vendor/uikit/uikit/src/js/core/core.js',
				'../../lib/vendor/uikit/uikit/src/js/core/touch.js',
				'../../lib/vendor/uikit/uikit/src/js/core/utility.js',
				'../../lib/vendor/uikit/uikit/src/js/core/dropdown.js',
				'../../lib/vendor/uikit/uikit/src/js/core/grid.js',
				'../../lib/vendor/uikit/uikit/src/js/core/modal.js',
				'../../lib/vendor/uikit/uikit/src/js/core/nav.js',
				// Selected components.
				'../../lib/vendor/uikit/uikit/src/js/components/autocomplete.js',
				'../../lib/vendor/uikit/uikit/src/js/components/lightbox.js',
				'../../lib/vendor/uikit/uikit/src/js/components/pagination.js',
				'../../lib/vendor/uikit/uikit/src/js/components/slider.js',
				'../../lib/vendor/uikit/uikit/src/js/components/slideshow.js',
				'../../lib/vendor/uikit/uikit/src/js/components/tooltip.js'
			])
			.pipe(uglify(uglifyOptions))
			.pipe(concat('uikit.js', { newLine: '\r\n\r\n' } )) // Doesn't get saved to disk.
			.pipe(header('/*! <%= pkg.title %> <%= pkg.version %> | <%= pkg.homepage %> | (c) 2014 YOOtheme | MIT License */\n', { 'pkg' : pkgUIkit } )),
			// Sticky sidebar.
			gulp.src([
				'node_modules/css-element-queries/src/ResizeSensor.js'
			])
			.pipe(uglify(uglifyOptions)),
			gulp.src([
				'node_modules/sticky-sidebar/dist/sticky-sidebar.min.js'
			]),
			gulp.src([
				'../../automad/gui/js/scroll_position.js',
				'js/*.js'
			])
			.pipe(uglify(uglifyOptions))
		)
		.pipe(concat('standard.min.js', { newLine: '\r\n\r\n' } ))
		.pipe(gulp.dest('dist'));
	
});	
	

// Compile, minify and prefix standard.less.
gulp.task('standard-less', function() {

	return 	gulp.src('less/standard.less')
			.pipe(less())
			.on('error', onError)
			.pipe(autoprefixer({ grid: false }))
			.pipe(cleanCSS(cleanCSSOptions))
			.pipe(rename({ suffix: '.min' }))
			.pipe(gulp.dest('dist'));
	
});


// Watch task.
gulp.task('watch', function() {

	gulp.watch('less/*.less', gulp.series('standard-less'));
	gulp.watch('js/*.js', gulp.series('standard-js'));
	
});


// The default task.
gulp.task('default', gulp.series('standard-js', 'standard-less'));