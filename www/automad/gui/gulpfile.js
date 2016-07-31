var 	gulp = require('gulp'),
	bump = require('gulp-bump'),
	cleanCSS = require('gulp-clean-css'),
	concat = require('gulp-concat'),
	header = require('gulp-header'),
	merge2 = require('merge2'),
	less = require('gulp-less'),
	rename = require('gulp-rename'),
	uglify = require('gulp-uglify'),
	gutil = require('gulp-util'),
	fs = require('fs'),
	pkg = require('./package.json'),
	destination = 'dist';


// Error handling to prevent watch task to fail silently without restarting.
var 	onError = function(err) {
			gutil.log(gutil.colors.red('ERROR', err.plugin), err.message);
			gutil.beep();
			new gutil.PluginError(err.plugin, err, {showStack: true})
			this.emit('end');
		};


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
	
	var	uglifyOptions = { 
			compress: { 
				hoist_funs: false, 
				hoist_vars: false 
			},
			output: {
				max_line_len: 1000
			}
		};
	
	return 	gulp.src('js/*.js')
		.pipe(concat('gui.min.js'))
		.pipe(uglify(uglifyOptions))
		.pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
		.pipe(gulp.dest(destination));
	
});


// Concat and minify all required js libraries.
gulp.task('lib-js', ['bump'], function() {
	
	var	uglifyOptions = { 
			preserveComments: 'license', 
			output: { 
				max_line_len: 1000
			} 
		};
	
	return 	merge2(
			// jQuery first.
			gulp.src([
				'../lib/jquery/jquery-2.0.3.min.js'
			]),
			// CodeMirror. To be minified.
			gulp.src([
				'../lib/codemirror/lib/codemirror.js',
				'../lib/codemirror/mode/markdown/markdown.js',
				'../lib/codemirror/addon/mode/overlay.js',
				'../lib/codemirror/mode/xml/xml.js',
				'../lib/codemirror/mode/gfm/gfm.js'
			])
			.pipe(uglify(uglifyOptions)),
			// Scrollbars.
			gulp.src([
				'../lib/malihu-custom-scrollbar/jquery.mCustomScrollbar.concat.min.js'
			]),
			// Marked (for HTML editor).
			gulp.src([
				'../lib/marked/marked.min.js'
			]),
			// UIkit core and components.
			gulp.src([
				'../lib/uikit/js/uikit.min.js',
				'../lib/uikit/js/components/autocomplete.min.js',
				'../lib/uikit/js/components/datepicker.min.js',
				'../lib/uikit/js/components/grid.min.js',
				'../lib/uikit/js/components/htmleditor.min.js',
				'../lib/uikit/js/components/notify.min.js',
				'../lib/uikit/js/components/sticky.min.js',
			]),
			// File upload. To be minified.
			gulp.src([
				'../lib/jquery-file-upload/jquery.ui.widget.js',
				'../lib/jquery-file-upload/jquery.fileupload.js',
				'../lib/jquery-file-upload/jquery.iframe-transport.js'	
			])
			.pipe(uglify(uglifyOptions))
		)
		.pipe(concat('libs.min.js', { newLine: '\r\n\r\n' } ))
		.pipe(gulp.dest(destination));
	
});


// Compile and minify automad.less.
gulp.task('less', ['bump'], function() {

	return 	gulp.src('less/automad.less')
		.pipe(less())
		.on('error', onError)
		.pipe(cleanCSS({ keepBreaks: true }))
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