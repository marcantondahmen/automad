/**
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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 *
 * ------------------------------
 *
 * Esbuild config for Automad
 *
 * This script handles automatic splitting of vendor modules and block classes in order
 * to be able to import those modules asynchronously. Splitting can be controlled entirely
 * by the file structure of the entry points. There are three main catergories:
 *
 * 1. Main "index" Files
 *
 * Index files are the main entry points that are loaded by the actual PHP pages.
 * They have to named "index.ts".
 *
 * 2. Block Classes
 *
 * Block classes are imported dynamically whenever a related web component is connected.
 * They have to match the pattern "blocks/components/*.ts".
 * All class modules are marked as external for esbuild and split into separate files
 * with a hashed filename.
 *
 * 3. Vendor Modules
 *
 * Large vendor modules are also split into separate files and also have hashed
 * filenames. Like blocks, they are also imported by other modules and therefore
 * are not loaded by PHP pages.
 * They have to match the pattern "vendor/*.ts".
 */

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
const hashPlaceholder = '@HASH';

const fileHash = (buffer, length = 8) => {
	return crypto
		.createHash('sha1')
		.update(buffer)
		.digest('hex')
		.slice(0, length);
};

const findEntries = (pattern) => {
	return fs
		.globSync(`${clientDir}/${pattern}`)
		.map((f) => f.replace(`${clientDir}/`, '').replace('.ts', ''));
};

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
			return {
				in: path.join(clientDir, entry),
				out: `${entry.replace('components/', '')}-${hashPlaceholder}`,
			};
		}),
		...vendorEntries.map((entry) => {
			return {
				in: path.join(clientDir, entry),
				out: `${entry}-${hashPlaceholder}`,
			};
		}),
	];

	const alias = {};

	blockEntries.forEach((entry) => {
		const key = `@/${entry}`;

		alias[key] =
			`../${entry.replace('components/', '')}-${hashPlaceholder}.js`;
	});

	vendorEntries.forEach((entry) => {
		const key = `@/${entry}`;

		alias[key] = `../${entry}-${hashPlaceholder}.js`;
	});

	const external = [
		...blockEntries.map(
			(entry) =>
				`../${entry.replace('components/', '')}-${hashPlaceholder}.js`
		),
		...vendorEntries.map((entry) => `../${entry}-${hashPlaceholder}.js`),
	];

	return { alias, entryPoints, external };
};

const hashImportsPlugin = () => {
	return {
		name: 'hash-imports',
		setup(build) {
			build.onEnd(async (result) => {
				const toBeHashed = fs.globSync(
					`${outdir}/**/*-${hashPlaceholder}.js`
				);

				const renameMap = new Map();
				const relative = (p) =>
					`../${path.basename(path.dirname(p))}/${path.basename(p)}`;

				toBeHashed.forEach((filePath) => {
					const buffer = fs.readFileSync(filePath);
					const hash = fileHash(buffer);
					const newPath = filePath.replace(hashPlaceholder, hash);

					fs.renameSync(filePath, newPath);

					renameMap.set(relative(filePath), relative(newPath));
				});

				const outputs = fs.globSync(`${outdir}/**/*.js`);

				outputs.forEach((filePath) => {
					let content = fs.readFileSync(filePath, 'utf8');

					for (const [oldName, newName] of renameMap) {
						content = content.replaceAll(oldName, newName);
					}

					fs.writeFileSync(filePath, content);
				});

				if (!isDev) {
					console.log('\nHashed imports:', renameMap);
				}
			});
		},
	};
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

const tsMinifierPlugin = () => {
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
	metafile: true,
	plugins: [
		lessLoader(),
		sassPlugin({
			quietDeps: true,
			filter: /\.scss/,
		}),
		postcss(),
		...(isDev ? [] : [tsMinifierPlugin()]),
		hashImportsPlugin(),
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
