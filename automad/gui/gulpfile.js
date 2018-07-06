var gulp = require('gulp'),
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
	cleanCSSOptions = {
		format: { wrapAt: 500 },
		rebase: false
	},
	// UIkit prefix. 
	// The prefix can not contain 'uk-' since selectors like [class*="uk-icon-"]
	// would also match prefixed classes like am-uk-icon-*.
	prefix = 'am-u-',
	customize = {
		cls: {
			search: /uk-([a-z\d\-]+)/g,
			replace: prefix + '$1'
		},
		da: {
			search: /data-uk-/g,
			replace: 'data-' + prefix
		} 
	};


// Error handling to prevent watch task to fail silently without restarting.
var onError = function(err) {
		gutil.log(gutil.colors.red('ERROR', err.plugin), err.message);
		gutil.beep();
		new gutil.PluginError(err.plugin, err, {showStack: true})
		this.emit('end');
	};


// Concat, minify and prefix the GUI js.
gulp.task('automad-js', function() {
	
	var	uglifyOptions = { 
			compress: { 
				hoist_funs: false, 
				hoist_vars: false 
			},
			output: {
				max_line_len: 500
			}
		};
	
	return 	gulp.src('js/*.js')
			.pipe(concat('automad.min.js'))
			.pipe(uglify(uglifyOptions))
			.pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
			// Prefix all UIkit items.
			.pipe(replace(customize.cls.search, customize.cls.replace))
			.pipe(replace(customize.da.search, customize.da.replace))
			.pipe(gulp.dest(destination));
	
});


// Concat minify and prefix all required js libraries.
gulp.task('libs-js', function() {
	
	var	uglifyOptions = { 
			output: {
				comments: /(license|copyright)/i
			} 
		},
		pkgUIkit = require('../../lib/vendor/uikit/uikit/package.json');
	
	return 	merge2(
			// jQuery first.
			gulp.src([
				'node_modules/jquery/dist/jquery.min.js'
			]),
			// CodeMirror. To be minified.
			gulp.src([
				'node_modules/codemirror/lib/codemirror.js',
				'node_modules/codemirror/mode/markdown/markdown.js',
				'node_modules/codemirror/addon/display/placeholder.js',
				'node_modules/codemirror/addon/edit/closebrackets.js',
				'node_modules/codemirror/addon/edit/matchbrackets.js',
				'node_modules/codemirror/addon/edit/continuelist.js',
				'node_modules/codemirror/addon/mode/overlay.js',
				'node_modules/codemirror/mode/xml/xml.js',
				'node_modules/codemirror/mode/gfm/gfm.js'
			])
			.pipe(uglify(uglifyOptions)),
			// Marked (for HTML editor).
			gulp.src([
				'node_modules/marked/marked.min.js'
			]),
			// Scrollbars.
			gulp.src([
				'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js'
			]),
			// UIkit core and components.
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
				'../../lib/vendor/uikit/uikit/src/js/components/accordion.js',
				'../../lib/vendor/uikit/uikit/src/js/components/autocomplete.js',
				'../../lib/vendor/uikit/uikit/src/js/components/datepicker.js',
				'../../lib/vendor/uikit/uikit/src/js/components/form-select.js',
				'../../lib/vendor/uikit/uikit/src/js/components/htmleditor.js',
				'../../lib/vendor/uikit/uikit/src/js/components/notify.js',
				'../../lib/vendor/uikit/uikit/src/js/components/sticky.js',
				'../../lib/vendor/uikit/uikit/src/js/components/timepicker.js',
				'../../lib/vendor/uikit/uikit/src/js/components/tooltip.js'
			])
			.pipe(uglify(uglifyOptions))
			.pipe(concat('uikit.js', { newLine: '\r\n\r\n' } )) // Doesn't get saved to disk.
			.pipe(header('/*! <%= pkg.title %> <%= pkg.version %> | <%= pkg.homepage %> | (c) 2014 YOOtheme | MIT License */\n', { 'pkg' : pkgUIkit } )),
			// File upload. To be minified.
			gulp.src([
				'node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
				'node_modules/blueimp-file-upload/js/jquery.fileupload.js',
				'node_modules/blueimp-file-upload/js/jquery.iframe-transport.js'	
			])
			.pipe(uglify(uglifyOptions)),
			// Draggabilly.
			gulp.src('node_modules/draggabilly/dist/draggabilly.pkgd.js')
			.pipe(uglify(uglifyOptions)),
			// Taggle. To be minified.
			gulp.src('node_modules/taggle/src/taggle.js')
			.pipe(uglify(uglifyOptions))
		)
		.pipe(concat('libs.min.js', { newLine: '\r\n\r\n' } ))
		// Prefix all UIkit items.
		.pipe(replace(customize.cls.search, customize.cls.replace))
		.pipe(replace(customize.da.search, customize.da.replace))
		.pipe(gulp.dest(destination));
	
});


// Compile, minify and prefix automad.less.
gulp.task('automad-less', function() {

	return 	gulp.src('less/automad.less')
			.pipe(less())
			.on('error', onError)
			.pipe(cleanCSS(cleanCSSOptions))
			.pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
			.pipe(rename({ suffix: '.min' }))
			// Prefix all UIkit items.
			.pipe(replace(customize.cls.search, customize.cls.replace))
			.pipe(replace(customize.da.search, customize.da.replace))
			.pipe(gulp.dest(destination));
	
});


// Concat all css files used by npm dependencies.
gulp.task('libs-css', function() {
	
	return 	gulp.src([
				'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css',
				'node_modules/codemirror/lib/codemirror.css'
			])
			.pipe(cleanCSS(cleanCSSOptions))
			.pipe(concat('libs.min.css', { newLine: '\r\n\r\n' } ))
			.pipe(gulp.dest(destination));
	
});


// Watch task.
gulp.task('watch', function() {

	gulp.watch('js/*.js', ['automad-js']);
	gulp.watch('less/*.less', ['automad-less']);
	
});


// Download fonts from Google.
gulp.task('google-fonts-download', function() {

	var	libDir = '../../../lib/fonts/google', // Note: the path is relative to gulp.dest
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

	return	gulp.src('../../lib/fonts/google/fonts.css')
			.pipe(replace(rgx, rpl))
			.pipe(gulp.dest('../../lib/fonts/google'))
	
});


// Run both google font tasks as a sequence.
gulp.task('google-fonts', sequence('google-fonts-download', 'google-fonts-css'));


// The default task.
gulp.task('default', ['automad-js', 'libs-js', 'automad-less', 'libs-css']);