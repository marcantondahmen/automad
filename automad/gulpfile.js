var gulp = require('gulp'),
	autoprefixer = require('gulp-autoprefixer'),
	cleanCSS = require('gulp-clean-css'),
	concat = require('gulp-concat'),
	googleFonts = require('gulp-google-webfonts'),
	header = require('gulp-header'),
	merge2 = require('merge2'),
	less = require('gulp-less'),
	rename = require('gulp-rename'),
	replace = require('gulp-replace'),
	uglify = require('gulp-uglify-es').default,
	gutil = require('gulp-util'),
	fs = require('fs'),
	pkg = require('./package.json'),
	distDashboard = 'gui/dist',
	distBlocks = 'blocks/dist',
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


// Concat and minify the blocks js.
gulp.task('blocks-js', function () {

	var uglifyOptions = {
		compress: {
			hoist_funs: false,
			hoist_vars: false
		},
		output: {
			max_line_len: 500
		}
	};

	return gulp.src(['blocks/js/*.js'])
		   .pipe(concat('blocks.min.js'))
		   .pipe(uglify(uglifyOptions))
		   .pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
		   .pipe(gulp.dest(distBlocks));

});


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
	
	return 	gulp.src([
				'gui/js/*.js',
				'gui/js/*/*.js'
			])
			.pipe(concat('automad.min.js'))
			.pipe(uglify(uglifyOptions))
			.pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
			// Rename custom scrollbars.
			// This has to be done accordingly within the libs-js task.
			.pipe(replace('.mCustomScrollbar(', '.am_mCustomScrollbar('))
			// Prefix all UIkit items.
			.pipe(replace(customize.cls.search, customize.cls.replace))
			.pipe(replace(customize.da.search, customize.da.replace))
			.pipe(gulp.dest(distDashboard));
	
});


// Concat minify and prefix all required js libraries.
gulp.task('libs-js', function() {
	
	var	uglifyOptions = { 
			output: {
				comments: /(license|copyright)/i
			} 
		},
		pkgUIkit = require('../lib/vendor/uikit/uikit/package.json');
	
	return 	merge2(
			// jQuery first.
			gulp.src([
				'node_modules/jquery/dist/jquery.min.js'
			]),
			// Editor.js.
			gulp.src([
				'node_modules/@editorjs/editorjs/dist/editor.js.LICENSE.txt',
				'node_modules/@editorjs/editorjs/dist/editor.js',
				'node_modules/@editorjs/embed/dist/bundle.js',
				'node_modules/@editorjs/header/dist/bundle.js',
				'node_modules/@editorjs/inline-code/dist/bundle.js',
				'node_modules/@editorjs/list/dist/bundle.js',
				'node_modules/@editorjs/marker/dist/bundle.js',
				'node_modules/@editorjs/quote/dist/bundle.js',
				'node_modules/@editorjs/raw/dist/bundle.js',
				'node_modules/@editorjs/table/dist/bundle.js',
				'node_modules/@editorjs/underline/dist/bundle.js',
				'node_modules/editorjs-drag-drop/dist/bundle.js',
				'node_modules/editorjs-inspector/dist/index.js',
				'node_modules/editorjs-style/dist/index.js',
				'node_modules/editorjs-undo/dist/bundle.js'
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
				'node_modules/codemirror/addon/selection/mark-selection.js',
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
				'node_modules/jquery-mousewheel/jquery.mousewheel.js',
				'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js'
			])
			// Rename plugin namespace to avoid conflicts.
			// This has to be done accordingly for the automad.min.js file within the automad-js task.
			.pipe(replace('pluginNS="mCustomScrollbar"', 'pluginNS="am_mCustomScrollbar"'))
			.pipe(replace('pluginPfx="mCS"', 'pluginPfx="am_mCS"'))
			.pipe(replace('defaultSelector=".mCustomScrollbar"', 'defaultSelector=".am_mCustomScrollbar"'))
			.pipe(uglify(uglifyOptions)),
			// UIkit core and components.
			gulp.src([
				// Core. 
				// Order of files taken from lib/vendor/uikit/uikit/gulpfile.js
				'../lib/vendor/uikit/uikit/src/js/core/core.js',
				'../lib/vendor/uikit/uikit/src/js/core/touch.js',
				'../lib/vendor/uikit/uikit/src/js/core/utility.js',
				'../lib/vendor/uikit/uikit/src/js/core/smooth-scroll.js',
				'../lib/vendor/uikit/uikit/src/js/core/scrollspy.js',
				'../lib/vendor/uikit/uikit/src/js/core/toggle.js',
				'../lib/vendor/uikit/uikit/src/js/core/alert.js',
				'../lib/vendor/uikit/uikit/src/js/core/button.js',
				'../lib/vendor/uikit/uikit/src/js/core/dropdown.js',
				'../lib/vendor/uikit/uikit/src/js/core/grid.js',
				'../lib/vendor/uikit/uikit/src/js/core/modal.js',
				'../lib/vendor/uikit/uikit/src/js/core/nav.js',
				'../lib/vendor/uikit/uikit/src/js/core/offcanvas.js',
				'../lib/vendor/uikit/uikit/src/js/core/switcher.js',
				'../lib/vendor/uikit/uikit/src/js/core/tab.js',
				'../lib/vendor/uikit/uikit/src/js/core/cover.js',
				// Selected components.
				'../lib/vendor/uikit/uikit/src/js/components/accordion.js',
				'../lib/vendor/uikit/uikit/src/js/components/autocomplete.js',
				'../lib/vendor/uikit/uikit/src/js/components/datepicker.js',
				'../lib/vendor/uikit/uikit/src/js/components/form-select.js',
				'../lib/vendor/uikit/uikit/src/js/components/notify.js',
				'../lib/vendor/uikit/uikit/src/js/components/timepicker.js',
				'../lib/vendor/uikit/uikit/src/js/components/tooltip.js'
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
		.pipe(gulp.dest(distDashboard));
	
});


// Compile and minify blocks.less.
gulp.task('blocks-less', function() {

	return gulp.src('blocks/less/blocks.less')
		   .pipe(less())
		   .on('error', onError)
		   .pipe(autoprefixer({ grid: false }))
		   .pipe(cleanCSS(cleanCSSOptions))
		   .pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
		   .pipe(rename({ suffix: '.min' }))
		   .pipe(gulp.dest(distBlocks));	

});


// Compile, minify and prefix automad.less.
gulp.task('automad-less', function() {

	return 	gulp.src('gui/less/automad.less')
			.pipe(less())
			.on('error', onError)
			.pipe(autoprefixer({ grid: false }))
			.pipe(cleanCSS(cleanCSSOptions))
			.pipe(header(fs.readFileSync('header.txt', 'utf8'), { pkg: pkg }))
			.pipe(rename({ suffix: '.min' }))
			// Prefix all UIkit items.
			.pipe(replace(customize.cls.search, customize.cls.replace))
			.pipe(replace(customize.da.search, customize.da.replace))
			.pipe(gulp.dest(distDashboard));
	
});


// Concat all css files used by npm dependencies.
gulp.task('libs-css', function() {
	
	return 	gulp.src([
				'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css',
				'node_modules/codemirror/lib/codemirror.css'
			])
			.pipe(cleanCSS(cleanCSSOptions))
			.pipe(concat('libs.min.css', { newLine: '\r\n\r\n' } ))
			.pipe(gulp.dest(distDashboard));
	
});


// Watch task.
gulp.task('watch', function() {

	gulp.watch('blocks/js/*.js', gulp.series('blocks-js'));
	gulp.watch('blocks/less/*.less', gulp.series('blocks-less'));
	gulp.watch('gui/js/*.js', gulp.series('automad-js'));
	gulp.watch('gui/js/*/*.js', gulp.series('automad-js'));
	gulp.watch('gui/less/*.less', gulp.series('automad-less'));
	gulp.watch('gui/less/*/*.less', gulp.series('automad-less'));
	
});


// The default task.
gulp.task('default', gulp.series('blocks-js', 'blocks-less', 'automad-js', 'libs-js', 'automad-less', 'libs-css'));