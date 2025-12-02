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
const clientDir = path.join(__dirname, 'automad/src/client');
const outdir = path.join(__dirname, 'automad/dist/build');
const isDev = process.argv.includes('--dev');
const year = new Date().getFullYear();
const banner = `/* Automad, (c) ${pkg.author}, ${pkg.license} license */`;

const hash = crypto
	.createHash('sha256')
	.update(pkg.version)
	.digest('hex')
	.slice(0, 8);

const findEntries = (pattern) => {
	return fs
		.globSync(`${clientDir}/${pattern}`)
		.map((f) => f.replace(`${clientDir}/`, '').replace('.ts', ''));
};

// The main purpose of this function is to automatically create a proper config
// that handles vendor splitting based on the file system. The function returns
// a single object that contains the entryPoints, alias and external props for
// the esbuild configuration. All files matching */index.ts are considered
// main modules. All files matching vendor/*.ts are considered vendor
// modules that are split and build seperately.
// Vendor modules are imported by the main modules and should not be linked
// to the HTML document. A version hash will be automatically appended to
// filename in order to invalidate browser caches after a release.
const pathConfig = () => {
	const mainEntries = findEntries('*/index.ts');
	const vendorEntries = findEntries('vendor/*.ts');
	const blockEntries = findEntries('blocks/components/*.ts');

	const entryPoints = [
		...mainEntries.map((entry) => {
			return {
				in: path.join(clientDir, entry),
				out: entry,
			};
		}),
		...blockEntries.map((entry) => {
			// Generate out files with hashes for imported blocks.
			return {
				in: path.join(clientDir, entry),
				out: `${entry.replace('components/', '')}-${hash}`,
			};
		}),
		...vendorEntries.map((entry) => {
			// Generate out files with hashes for vendor modules.
			return {
				in: path.join(clientDir, entry),
				out: `${entry}-${hash}`,
			};
		}),
	];

	const alias = {};

	// Generate hashed aliases according to the blocks out files above.
	blockEntries.forEach((entry) => {
		const key = `@/${entry}`;

		alias[key] = `../${entry.replace('components/', '')}-${hash}.js`;
	});

	// Generate hashed aliases according to the vendor out files above.
	vendorEntries.forEach((entry) => {
		const key = `@/${entry}`;

		alias[key] = `../${entry}-${hash}.js`;
	});

	const external = [
		// Also generate a list of files marked as external based on the blocks.
		...blockEntries.map(
			(entry) => `../${entry.replace('components/', '')}-${hash}.js`
		),
		// Also generate a list of files marked as external based on the vendor modules.
		...vendorEntries.map((entry) => `../${entry}-${hash}.js`),
	];

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
	...pathConfig(),
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
