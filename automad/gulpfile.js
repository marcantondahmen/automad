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

});


gulp.task('font-inter', function () {
	return merge2(
		gulp
			.src('node_modules/typeface-inter/Inter Web/Inter-roman.var.woff2')
			.pipe(rename('inter-roman-var.woff2')),
		gulp
			.src('node_modules/typeface-inter/Inter Web/Inter-italic.var.woff2')
			.pipe(rename('inter-italic-var.woff2')),
		gulp.src([
			'node_modules/typeface-inter/LICENSE.txt',
			'node_modules/typeface-inter/README.md',
		])
	).pipe(gulp.dest(`${dist}/fonts/inter`));
});

gulp.task('font-bootstrap-icons', function () {
	return gulp
		.src([
			'node_modules/bootstrap-icons/font/fonts/bootstrap-icons.woff2',
			'node_modules/bootstrap-icons/LICENSE.md',
			'node_modules/bootstrap-icons/README.md',
		])
		.pipe(gulp.dest(`${dist}/fonts/bootstrap-icons`));
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
	gulp.watch('src/**/*.php', gulp.series('reload'));
});

// The default task.
gulp.task('default', gulp.series('blocks-js', 'blocks-less'));
