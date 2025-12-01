import pkg from './package.json' with { type: 'json' };
import browserSync from 'browser-sync';
import { lessLoader } from 'esbuild-plugin-less';
import { sassPlugin } from 'esbuild-sass-plugin';
import postcss from 'esbuild-postcss';
import esbuild from 'esbuild';
import { fileURLToPath } from 'node:url';
import { dirname } from 'node:path';
import path from 'path';
import fs from 'fs';
import crypto from 'crypto';

const __dirname = dirname(fileURLToPath(import.meta.url));
const isDev = process.argv.includes('--dev');
const outdir = path.join(__dirname, 'automad/dist/build');
const year = new Date().getFullYear();
const banner = `/* Automad, (c) ${pkg.author}, ${pkg.license} license */`;

const items = [
	'admin/index',
	'admin/vendor/editorjs',
	'admin/vendor/filerobot',
	'admin/vendor/toastui',
	'blocks/index',
	'consent/index',
	'inpage/index',
	'mail/index',
	'prism/index',
];

const pathConfig = (items) => {
	const hash = crypto
		.createHash('sha256')
		.update(pkg.version)
		.digest('hex')
		.slice(0, 8);

	const entryPoints = items.map((entry) => {
		return {
			in: path.join(__dirname, 'automad/src/client', entry),
			out: entry.replace(/vendor\/(.*)$/, `vendor/$1-${hash}`),
		};
	});

	const vendorItems = items.filter((item) => item.includes('vendor'));
	const alias = {};

	vendorItems.forEach((item) => {
		const basename = item.replace('admin/vendor/', '');
		const key = `@/${item}`;

		alias[key] = `./vendor/${basename}-${hash}.js`;
	});

	const external = vendorItems.map((item) => {
		const basename = item.replace('admin/vendor/', '');

		return `./vendor/${basename}*`;
	});

	return { alias, entryPoints, external };
};

const minify = (source) =>
	source
		// Remove all single line comments in order to safely remove newlines.
		.replace(/\/\/\s.*$/gm, ' ')
		// Once replace multiple whitespace chars including newlines to a single space.
		.replace(/\s+/g, ' ')
		// From here on, on single \s needs to be matched.
		.replace(/\>\s\</g, '><')
		.replace(/(\w\>)\s/g, '$1')
		.replace(/\s(\<\/\w)/g, '$1')
		// Also trim template strings.
		.replace(/`([^`]+)`/g, (_, s) => `\`${s.trim()}\``);

const tsMinifier = () => {
	return {
		name: 'ts-minifier',
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
	...pathConfig(items),
	bundle: true,
	format: 'esm',
	sourcemap: isDev,
	minify: !isDev,
	target: ['es2022'],
	assetNames: '[name]',
	write: true,
	outdir,
	banner: {
		js: banner,
		css: banner,
	},
	legalComments: 'inline',
	drop: isDev ? [] : ['console'],
	logLevel: 'info',
	loader: {
		'.svg': 'text',
		'.woff': 'file',
		'.woff2': 'file',
	},
	define: { DEVELOPMENT: isDev.toString() },
	plugins: [
		lessLoader(),
		sassPlugin({
			quietDeps: true,
			filter: /\.scss/,
		}),
		postcss(),
		...(isDev ? [] : [tsMinifier()]),
	],
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
		files: ['**/src/**/*.php', '**/dist/**/*.{js,css}'],
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
