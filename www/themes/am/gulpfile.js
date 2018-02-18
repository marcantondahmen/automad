var gulp = require('gulp'),
	cleanCSS = require('gulp-clean-css'),
	concat = require('gulp-concat'),
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
		};
	
	return 	merge2(
			gulp.src([
				'../../automad/lib/jquery/jquery-2.0.3.min.js',
				'../../automad/lib/uikit/js/uikit.min.js',
				'../../automad/lib/uikit/js/components/autocomplete.min.js',
				'../../automad/lib/uikit/js/components/lightbox.min.js',
				'../../automad/lib/uikit/js/components/pagination.min.js',
				'../../automad/lib/uikit/js/components/slider.min.js',
				'../../automad/lib/uikit/js/components/slideshow.min.js',
				'../../automad/lib/uikit/js/components/sticky.min.js',
				'../../automad/lib/uikit/js/components/tooltip.min.js',
				'lib/imagesloaded/imagesloaded.pkgd.min.js',
				'lib/masonry/masonry.pkgd.min.js'
			]),
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