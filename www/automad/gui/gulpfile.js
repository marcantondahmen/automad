var 	gulp = require('gulp'),
	bump = require('gulp-bump'),
	cleanCSS = require('gulp-clean-css'),
	concat = require('gulp-concat'),
	header = require('gulp-header'),
	gulpIf = require('gulp-if'),
	less = require('gulp-less'),
	rename = require('gulp-rename'),
	uglify = require('gulp-uglify'),
	fs = require('fs'),
	pkg = require('./package.json'),
	destination = 'dist';


// Set version in package.json to the current date (YY.MM.DD).
// Note that this version format should be different from the Automad version (AM_VERSION), since the Automad version gets bumped on commits
// and therefore it is difficult (and not needed) to keep both versions in sync automatically.
gulp.task('bump', function() {

	var	date = new Date(),
		y = date.getFullYear().toString().substr(-2),
		m = ('0' + (date.getMonth() + 1).toString()).substr(-2),
		d = ('0' + date.getDate().toString()).substr(-2);
	
	// Set pkg.version to be used in the header template without reloading package.json.
	pkg.version = y + '.' + m + '.' + d;
	
	return	gulp.src('./package.json')
		.pipe(bump( { version: pkg.version } ))
		.pipe(gulp.dest('./'));
	
});


// Concat and minify the GUI js.
gulp.task('gui-js', ['bump'], function() {
	
	return 	gulp.src('js/*.js')
		.pipe(concat('gui.min.js'))
		.pipe(uglify({ compress: { hoist_funs: false, hoist_vars: false } }))
		.pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
		.pipe(gulp.dest(destination));
	
});


// Concat and minify all required js libraries.
gulp.task('lib-js', ['bump'], function() {
	
	return	gulp.src([
			'../lib/jquery/jquery-2.0.3.min.js', 
			'../lib/bootstrap/js/bootstrap.min.js',
			'../lib/malihu-custom-scrollbar/jquery.mCustomScrollbar.concat.min.js',
			'../lib/jquery-file-upload/jquery.ui.widget.js',
			'../lib/jquery-file-upload/jquery.fileupload.js',
			'../lib/jquery-file-upload/jquery.iframe-transport.js'
		])
		.pipe(gulpIf(['*.js','!*.min.js'], uglify({ preserveComments: 'license' })))
		.pipe(concat('libs.min.js', { newLine: '\r\n' } ))
		.pipe(gulp.dest(destination));
	
});


// Compile and minify automad.less.
gulp.task('less', ['bump'], function() {

	return 	gulp.src('less/automad.less')
		.pipe(less())
		.pipe(cleanCSS())
		.pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
		.pipe(rename({ suffix: '.min' }))
		.pipe(gulp.dest(destination));
	
});

// Watch task.
gulp.task('watch', function() {

	gulp.watch('js/*.js', ['gui-js']);
	gulp.watch('less/*.less', ['less']);
	
});


// The default task.
gulp.task('default', ['gui-js', 'lib-js', 'less']);