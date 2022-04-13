/*
 *	Baker
 *
 *	Copyright (c) 2017-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	MIT license
 *
 *	based on UIkit 2
 */

var gulp = require('gulp'),
	autoprefixer = require('gulp-autoprefixer'),
	cleanCSS = require('gulp-clean-css'),
	concat = require('gulp-concat'),
	header = require('gulp-header'),
	merge2 = require('merge2'),
	less = require('gulp-less'),
	remoteSrc = require('gulp-remote-src'),
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

// Concat all css files used by npm dependencies.
gulp.task('libs-css', function() {
	
	return 	gulp.src([
				'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css'
			])
			.pipe(cleanCSS(cleanCSSOptions))
			.pipe(concat('libs.min.css', { newLine: '\r\n\r\n' } ))
			.pipe(gulp.dest('dist'));
	
});

// Compile and minify baker.min.css.
gulp.task('baker-less', function() {
	
	return 	gulp.src('less/baker.less')
			.pipe(less())
			.on('error', onError)
			.pipe(autoprefixer({ grid: false }))
			.pipe(cleanCSS(cleanCSSOptions))
			.pipe(rename({ suffix: '.min' }))
			.pipe(gulp.dest('dist'));

});

// Concat and minify libs.min.js.
gulp.task('libs-js', function() {
	
	var	uglifyOptions = { 
			output: {
				comments: /(license|copyright)/i
			} 
		},
		pkgUIkit = require('../../../lib/vendor/uikit/uikit/package.json');
	
	return 	merge2(
				// jQuery first.
				gulp.src([
					'node_modules/jquery/dist/jquery.min.js',
					'node_modules/imagesloaded/imagesloaded.pkgd.min.js'
				]),
				// Scrollbars.
				gulp.src([
					'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js'
				]),
				gulp.src([
					// Core. 
					// Order of files taken from lib/vendor/uikit/uikit/gulpfile.js
					'../../../lib/vendor/uikit/uikit/src/js/core/core.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/touch.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/utility.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/smooth-scroll.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/scrollspy.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/toggle.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/alert.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/button.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/dropdown.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/grid.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/modal.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/nav.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/offcanvas.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/switcher.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/tab.js',
					'../../../lib/vendor/uikit/uikit/src/js/core/cover.js',
					// Selected components.
					'../../../lib/vendor/uikit/uikit/src/js/components/autocomplete.js',
					'../../../lib/vendor/uikit/uikit/src/js/components/pagination.js',
					'../../../lib/vendor/uikit/uikit/src/js/components/sticky.js'
				])
				.pipe(uglify(uglifyOptions))
				.pipe(concat('uikit.js', { newLine: '\r\n\r\n' } )) // Doesn't get saved to disk.
				.pipe(header('/*! <%= pkg.title %> <%= pkg.version %> | <%= pkg.homepage %> | (c) 2014 YOOtheme | MIT License */\n', { 'pkg' : pkgUIkit } )),
				remoteSrc([
					'packages/standard/js/masonry.js'
				], {
					base: 'https://raw.githubusercontent.com/marcantondahmen/automad/1.6.4/'
				})
			.pipe(uglify(uglifyOptions))
			)
			.pipe(concat('libs.min.js', { newLine: '\r\n\r\n' } ))
			.pipe(gulp.dest('dist'));
		
});	
	
// Concat and minify baker.min.js.
gulp.task('baker-js', function() {
	
	var	uglifyOptions = { 
			compress: { 
				hoist_funs: false, 
				hoist_vars: false 
			},
			output: {
				max_line_len: 500
			}
		};
	
	return 	gulp.src([
				'../../../automad/gui/js/textarea.js',
				'../../../automad/gui/js/toggle.js',
				'../../../automad/gui/js/util.js',
				'js/*.js'
			])
			.pipe(uglify(uglifyOptions))
			.pipe(concat('baker.min.js', { newLine: '\r\n\r\n' } ))
			.pipe(gulp.dest('dist'));
	
});


// Watch task.
gulp.task('watch', function() {

	gulp.watch('less/*.less', gulp.series('baker-less'));
	gulp.watch('js/*.js', gulp.series('baker-js'));
	
});


// The default task.
gulp.task('default', gulp.series('libs-js', 'libs-css', 'baker-js', 'baker-less'));
