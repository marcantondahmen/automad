const headerTemplate = `/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 * 
 *
 * AUTOMAD
 * 
 * version <%= pkg.version %>
 *
 * Copyright (c) 2014-<%= new Date().getFullYear() %> by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */
`;

const gulp = require('gulp');
const autoprefixer = require('gulp-autoprefixer');
const beep = require('beepbeep');
const browserSync = require('browser-sync').create();
const cleanCSS = require('gulp-clean-css');
const concat = require('gulp-concat');
const header = require('gulp-header');
const merge2 = require('merge2');
const less = require('gulp-less');
const log = require('fancy-log');
const rename = require('gulp-rename');
const replace = require('gulp-replace');
const sort = require('gulp-sort');
const uglify = require('gulp-uglify-es').default;
const pkg = require('./package.json');
const dist = 'dist';
const cleanCSSOptions = {
	format: { wrapAt: 500 },
	rebase: false,
};

// UIkit prefix.
// The prefix can not contain 'uk-' since selectors like [class*="uk-icon-"]
// would also match prefixed classes like am-uk-icon-*.
const prefix = 'am-u-';
const customize = {
	cls: {
		search: /uk-([a-z\d\-]+)/g,
		replace: prefix + '$1',
	},
	da: {
		search: /data-uk-/g,
		replace: 'data-' + prefix,
	},
};

// Error handling to prevent watch task to fail silently without restarting.
const onError = function (err) {
	log.error(err);
	beep();
	this.emit('end');
};

// Concat and minify the blocks js.
gulp.task('blocks-js', function () {
	var uglifyOptions = {
		compress: {
			hoist_funs: false,
			hoist_vars: false,
		},
		output: {
			max_line_len: 500,
		},
	};

	return gulp
		.src(['blocks/js/*.js'])
		.pipe(sort())
		.pipe(concat('blocks.min.js'))
		.pipe(uglify(uglifyOptions))
		.pipe(header(headerTemplate, { pkg: pkg }))
		.pipe(gulp.dest(dist));
});

// Concat, minify and prefix the UI js.
gulp.task('automad-js', function () {
	var uglifyOptions = {
		compress: {
			hoist_funs: false,
			hoist_vars: false,
		},
		output: {
			max_line_len: 500,
		},
	};

	return (
		merge2(
			gulp.src(['ui/js/*.js']).pipe(sort()),
			gulp.src(['ui/js/*/*.js']).pipe(sort()),
			gulp.src(['ui/js/*/*/*.js']).pipe(sort())
		)
			.pipe(concat('automad.min.js'))
			.pipe(uglify(uglifyOptions))
			.pipe(header(headerTemplate, { pkg: pkg }))
			// Rename custom scrollbars.
			// This has to be done accordingly within the libs-js task.
			.pipe(replace('.mCustomScrollbar(', '.am_mCustomScrollbar('))
			// Prefix all UIkit items.
			.pipe(replace(customize.cls.search, customize.cls.replace))
			.pipe(replace(customize.da.search, customize.da.replace))
			.pipe(gulp.dest(dist))
	);
});

// Concat minify and prefix all required js libraries.
gulp.task('libs-js', function () {
	var uglifyOptions = {
			output: {
				comments: /(license|copyright)/i,
			},
		},
		pkgUIkit = require('../lib/vendor/uikit/uikit/package.json');

	return (
		merge2(
			// jQuery first.
			gulp.src(['node_modules/jquery/dist/jquery.min.js']),
			// Sortable.
			gulp.src(['node_modules/sortablejs/Sortable.min.js']),
			// Editor.js.
			gulp.src([
				'node_modules/@editorjs/editorjs/dist/editor.js.LICENSE.txt',
				'node_modules/@editorjs/editorjs/dist/editor.js',
				'node_modules/@editorjs/nested-list/dist/nested-list.js',
				'node_modules/@editorjs/quote/dist/bundle.js',
				'node_modules/@editorjs/table/dist/table.js',
				'node_modules/editorjs-drag-drop/dist/bundle.js',
				'node_modules/editorjs-style/dist/index.js',
			]),
			// CodeMirror. To be minified.
			gulp
				.src([
					'node_modules/codemirror/lib/codemirror.js',
					'node_modules/codemirror/mode/markdown/markdown.js',
					'node_modules/codemirror/addon/display/placeholder.js',
					'node_modules/codemirror/addon/edit/closebrackets.js',
					'node_modules/codemirror/addon/edit/matchbrackets.js',
					'node_modules/codemirror/addon/edit/continuelist.js',
					'node_modules/codemirror/addon/mode/overlay.js',
					'node_modules/codemirror/addon/selection/mark-selection.js',
					'node_modules/codemirror/mode/xml/xml.js',
					'node_modules/codemirror/mode/gfm/gfm.js',
				])
				.pipe(uglify(uglifyOptions)),
			// Marked (for HTML editor).
			gulp.src(['node_modules/marked/marked.min.js']),
			// Scrollbars.
			gulp
				.src([
					'node_modules/jquery-mousewheel/jquery.mousewheel.js',
					'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js',
				])
				// Rename plugin namespace to avoid conflicts.
				// This has to be done accordingly for the automad.min.js file within the automad-js task.
				.pipe(
					replace(
						'pluginNS="mCustomScrollbar"',
						'pluginNS="am_mCustomScrollbar"'
					)
				)
				.pipe(replace('pluginPfx="mCS"', 'pluginPfx="am_mCS"'))
				.pipe(
					replace(
						'defaultSelector=".mCustomScrollbar"',
						'defaultSelector=".am_mCustomScrollbar"'
					)
				)
				.pipe(uglify(uglifyOptions)),
			// UIkit core and components.
			gulp
				.src([
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
					'../lib/vendor/uikit/uikit/src/js/core/tab.js',
					'../lib/vendor/uikit/uikit/src/js/core/cover.js',
					// Selected components.
					'../lib/vendor/uikit/uikit/src/js/components/accordion.js',
					'../lib/vendor/uikit/uikit/src/js/components/autocomplete.js',
					'../lib/vendor/uikit/uikit/src/js/components/datepicker.js',
					'../lib/vendor/uikit/uikit/src/js/components/form-select.js',
					'../lib/vendor/uikit/uikit/src/js/components/notify.js',
					'../lib/vendor/uikit/uikit/src/js/components/timepicker.js',
					'../lib/vendor/uikit/uikit/src/js/components/tooltip.js',
				])
				.pipe(uglify(uglifyOptions))
				.pipe(concat('uikit.js', { newLine: '\r\n\r\n' })) // Doesn't get saved to disk.
				.pipe(
					header(
						'/*! <%= pkg.title %> <%= pkg.version %> | <%= pkg.homepage %> | (c) 2014 YOOtheme | MIT License */\n',
						{ pkg: pkgUIkit }
					)
				),
			// File upload. To be minified.
			gulp
				.src([
					'node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
					'node_modules/blueimp-file-upload/js/jquery.fileupload.js',
					'node_modules/blueimp-file-upload/js/jquery.iframe-transport.js',
				])
				.pipe(uglify(uglifyOptions)),
			// Draggabilly.
			gulp
				.src('node_modules/draggabilly/dist/draggabilly.pkgd.js')
				.pipe(uglify(uglifyOptions)),
			// Taggle. To be minified.
			gulp
				.src('node_modules/taggle/src/taggle.js')
				.pipe(uglify(uglifyOptions))
		)
			.pipe(concat('libs.min.js', { newLine: '\r\n\r\n' }))
			// Prefix all UIkit items.
			.pipe(replace(customize.cls.search, customize.cls.replace))
			.pipe(replace(customize.da.search, customize.da.replace))
			.pipe(gulp.dest(dist))
	);
});

// Compile and minify blocks.less.
gulp.task('blocks-less', function () {
	return gulp
		.src('blocks/less/blocks.less')
		.pipe(less())
		.on('error', onError)
		.pipe(autoprefixer({ grid: false }))
		.pipe(cleanCSS(cleanCSSOptions))
		.pipe(header(headerTemplate, { pkg: pkg }))
		.pipe(rename({ suffix: '.min' }))
		.pipe(gulp.dest(dist));
});

// Compile, minify and prefix automad.less.
gulp.task('automad-less', function () {
	return (
		gulp
			.src('ui/less/automad.less')
			.pipe(less())
			.on('error', onError)
			.pipe(autoprefixer({ grid: false }))
			.pipe(cleanCSS(cleanCSSOptions))
			.pipe(header(headerTemplate, { pkg: pkg }))
			.pipe(rename({ suffix: '.min' }))
			// Prefix all UIkit items.
			.pipe(replace(customize.cls.search, customize.cls.replace))
			.pipe(replace(customize.da.search, customize.da.replace))
			.pipe(gulp.dest(dist))
	);
});

// Concat all css files used by npm dependencies.
gulp.task('libs-css', function () {
	return gulp
		.src([
			'node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css',
			'node_modules/codemirror/lib/codemirror.css',
		])
		.pipe(cleanCSS(cleanCSSOptions))
		.pipe(concat('libs.min.css', { newLine: '\r\n\r\n' }))
		.pipe(gulp.dest(dist));
});

// Browser sync.

gulp.task('reload', function (done) {
	browserSync.reload();
	done();
});

// Watch task.
gulp.task('watch', function () {
	browserSync.init({
		proxy: 'localhost:8080/automad-development',
	});

	gulp.watch('blocks/js/*.js', gulp.series('blocks-js', 'reload'));
	gulp.watch('blocks/less/*.less', gulp.series('blocks-less', 'reload'));
	gulp.watch('ui/js/**/*.js', gulp.series('automad-js', 'reload'));
	gulp.watch('ui/less/**/*.less', gulp.series('automad-less', 'reload'));
	gulp.watch('src/**/*.php', gulp.series('reload'));
});

// The default task.
gulp.task(
	'default',
	gulp.series(
		'blocks-js',
		'blocks-less',
		'automad-js',
		'libs-js',
		'automad-less',
		'libs-css'
	)
);
