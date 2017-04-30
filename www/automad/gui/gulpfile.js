var 	gulp = require('gulp'),
	bump = require('gulp-bump'),
	cleanCSS = require('gulp-clean-css'),
	concat = require('gulp-concat'),
	googleFonts = require('gulp-google-webfonts'),
	header = require('gulp-header'),
	merge2 = require('merge2'),
	less = require('gulp-less'),
	rename = require('gulp-rename'),
	replace = require('gulp-replace'),
	sequence = require('gulp-sequence'),
	uglify = require('gulp-uglify'),
	gutil = require('gulp-util'),
	fs = require('fs'),
	pkg = require('./package.json'),
	destination = 'dist',
	prefix = 'am';


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


// Concat, minify and prefix the GUI js.
gulp.task('automad-js', ['bump'], function() {
	
	var	uglifyOptions = { 
			compress: { 
				hoist_funs: false, 
				hoist_vars: false 
			},
			output: {
				max_line_len: 2000
			}
		};
	
	return 	gulp.src('js/*.js')
		.pipe(concat('automad.min.js'))
		.pipe(uglify(uglifyOptions))
		.pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
		// Prefix all UIkit items.
		.pipe(replace(/(uk-[a-z\d\-]+)/g, prefix + '-$1'))
		.pipe(replace(/data-uk-/g, 'data-' + prefix + '-uk-'))
		.pipe(gulp.dest(destination));
	
});


// Concat minify and prefix all required js libraries.
gulp.task('libs-js', ['bump'], function() {
	
	var	uglifyOptions = { 
			preserveComments: 'license', 
			output: { 
				max_line_len: 2000
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
			// Marked (for HTML editor).
			gulp.src([
				'../lib/marked/marked.min.js'
			]),
			// UIkit core and components.
			gulp.src([
				'../lib/uikit/js/uikit.min.js',
				'../lib/uikit/js/components/accordion.min.js',
				'../lib/uikit/js/components/autocomplete.min.js',
				'../lib/uikit/js/components/datepicker.min.js',
				'../lib/uikit/js/components/grid.min.js',
				'../lib/uikit/js/components/htmleditor.min.js',
				'../lib/uikit/js/components/notify.min.js',
				'../lib/uikit/js/components/sticky.min.js',
				'../lib/uikit/js/components/tooltip.min.js'
			]),
			// File upload. To be minified.
			gulp.src([
				'../lib/jquery-file-upload/jquery.ui.widget.js',
				'../lib/jquery-file-upload/jquery.fileupload.js',
				'../lib/jquery-file-upload/jquery.iframe-transport.js'	
			])
			.pipe(uglify(uglifyOptions)),
			// Draggabilly.
			gulp.src('../lib/draggabilly/draggabilly.pkgd.js')
			.pipe(uglify(uglifyOptions))
		)
		.pipe(concat('libs.min.js', { newLine: '\r\n\r\n' } ))
		// Prefix all UIkit items.
		.pipe(replace(/(uk-[a-z\d\-]+)/g, prefix + '-$1'))
		.pipe(replace(/data-uk-/g, 'data-' + prefix + '-uk-'))
		.pipe(gulp.dest(destination));
	
});


// Compile, minify and prefix automad.less.
gulp.task('automad-less', ['bump'], function() {

	return 	gulp.src('less/automad.less')
		.pipe(less())
		.on('error', onError)
		.pipe(cleanCSS({ format: 'keep-breaks', rebase: false }))
		.pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
		.pipe(rename({ suffix: '.min' }))
		// Prefix all UIkit items.
		.pipe(replace(/(uk-[a-z\d\-]+)/g, prefix + '-$1'))
		.pipe(replace(/data-uk-/g, 'data-' + prefix + '-uk-'))
		.pipe(gulp.dest(destination));
	
});


// Watch task.
gulp.task('watch', function() {

	gulp.watch('js/*.js', ['automad-js']);
	gulp.watch('less/*.less', ['automad-less']);
	
});


// Download fonts from Google.
gulp.task('google-fonts-download', function() {

	var	libDir = '../../lib/google/fonts/', // Note: the path is relative to gulp.dest
		fontsList = './fonts.list',
		options = {
			fontsDir: libDir,
			cssDir: libDir,
			cssFilename: 'fonts.css'
		},
		woff = Object.assign({}, options, { format: 'woff' }),
		woff2 = Object.assign({}, options, { format: 'woff2' }),
		ttf = Object.assign({}, options, { format: 'ttf' });

	return	merge2(
			gulp.src(fontsList)
			.pipe(googleFonts(woff)),
			gulp.src(fontsList)
			.pipe(googleFonts(woff2)),
			gulp.src(fontsList)
			.pipe(googleFonts(ttf))
		)
		.pipe(gulp.dest(destination));	
		
});


// Add all formats to the fonts.css file. 
// The gulp-google-webfonts plugin can only create a .css file for one format.
// Therefore that file will be processed in a second step.
gulp.task('google-fonts-css', function() {

	var	rgx = /(src\: url\(([^\)]+?)\.ttf\) format\(\'truetype\'\);)/g,
		rpl = 	"src: url($2.woff2) format('woff2');" +
			"\n\tsrc: url($2.woff) format('woff');" + 
			"\n\t$1";

	return	gulp.src('../lib/google/fonts/fonts.css')
		.pipe(replace(rgx, rpl))
		.pipe(gulp.dest('../lib/google/fonts'))
	
});


// Run both google font tasks as a sequence.
gulp.task('google-fonts', sequence('google-fonts-download', 'google-fonts-css'));


// The default task.
gulp.task('default', ['automad-js', 'libs-js', 'automad-less']);