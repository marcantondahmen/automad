const pkg = require('./package.json');
const browserSync = require('browser-sync');
const { lessLoader } = require('esbuild-plugin-less');
const postcss = require('esbuild-postcss');
const esbuild = require('esbuild');
const path = require('path');
const fs = require('fs');

const isDev = process.argv.includes('--dev');
const outdir = path.join(__dirname, 'automad/dist/build');
const year = new Date().getFullYear();
const banner = `/* Automad ${pkg.version}, (c) ${year} ${pkg.author}, ${pkg.license} license */`;

const entryPoints = [
	'admin',
	'blocks',
	'consent',
	'inpage',
	'mail',
	'prism',
].map((entry) => {
	return path.join(__dirname, 'automad/src/client', entry, 'index.ts');
});

const minify = (source) =>
	source
		.replace(/\/\/\s.*$/gm, ' ')
		.replace(/\s+/g, ' ')
		.replace(/\>\s+\</g, '><')
		.replace(/(\w\>)\s/g, '$1')
		.replace(/\s(\<\/\w)/g, '$1')
		.replace(/`([^`]+)`/g, (_, s) => `\`${s.trim()}\``);

const htmlMinifier = () => {
	return {
		name: 'html-minifier',
		setup(build) {
			build.onLoad(
				{ filter: /\.ts$/, namespace: 'file' },
				async (args) => {
					const source = await fs.promises.readFile(
						args.path,
						'utf8'
					);

					return {
						contents: minify(source),
						loader: 'ts',
					};
				}
			);
		},
	};
};

const commonConfig = {
	entryPoints,
	bundle: true,
	format: 'esm',
	splitting: true,
	sourcemap: isDev,
	minify: !isDev,
	target: ['esnext'],
	assetNames: '[name]',
	chunkNames: 'chunks/[name].[hash]',
	write: true,
	outdir,
	banner: {
		js: banner,
		css: banner,
	},
	drop: isDev ? [] : ['console'],
	logLevel: 'info',
	loader: {
		'.svg': 'text',
		'.woff': 'file',
		'.woff2': 'file',
	},
	define: { DEVELOPMENT: isDev.toString() },
	plugins: [htmlMinifier(), lessLoader(), postcss()],
};

async function buildAll() {
	await esbuild.build(commonConfig);
}

async function startDev() {
	await buildAll();

	const bs = browserSync.create();

	bs.init({
		host: 'localhost',
		port: 3000,
		proxy: 'http://127.0.0.1:8080/automad-development',
		open: false,
		notify: false,
		files: ['**/*.php', '**/dist/**/*.{js,css}'],
		serveStatic: [outdir],
	});

	let ctx = await esbuild.context(commonConfig);

	await ctx.watch();
}

if (isDev) {
	startDev().catch((err) => {
		console.error(err);
		process.exit(1);
	});
} else {
	buildAll().catch((err) => {
		console.error(err);
		process.exit(1);
	});
}
