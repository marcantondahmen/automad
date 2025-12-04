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
 *
 * Note that the common module is not build as an external file.
 */

import pkg from './package.json' with { type: 'json' };
import browserSync from 'browser-sync';
import postcss from 'esbuild-postcss';
import esbuild from 'esbuild';
import crypto from 'crypto';
import path from 'path';
import fs from 'fs';
import { lessLoader } from 'esbuild-plugin-less';
import { sassPlugin } from 'esbuild-sass-plugin';
import { fileURLToPath } from 'node:url';
import { dirname } from 'node:path';

const __dirname = dirname(fileURLToPath(import.meta.url));
const clientSrc = path.join(__dirname, 'automad/src/client');
const outdir = path.join(__dirname, 'automad/dist/build');
const isDev = process.argv.includes('--dev');
const banner = `/* Automad, (c) ${pkg.author}, ${pkg.license} license */`;
const hashPlaceholder = '@hash';

const fileHash = (buffer, length = 8) => {
	return crypto
		.createHash('sha1')
		.update(buffer)
		.digest('hex')
		.slice(0, length);
};

const findEntries = (pattern) => {
	return fs
		.globSync(`${clientSrc}/${pattern}`)
		.map((f) => f.replace(`${clientSrc}/`, '').replace('.ts', ''));
};

const makeRelative = (file) =>
	`../${path.basename(path.dirname(file))}/${path.basename(file)}`;

const write = (file, contents) => {
	fs.mkdirSync(path.dirname(file), {
		recursive: true,
	});

	fs.writeFileSync(file, contents);
	console.log(`   ${makeRelative(file)}`);
};

const pathConfig = () => {
	const mainEntries = findEntries('*/index.ts').filter(
		(f) => !f.match(/common/)
	);

	const vendorEntries = findEntries('vendor/*.ts');
	const blockEntries = findEntries('blocks/components/*.ts');

	const entryPoints = [
		...mainEntries.map((entry) => {
			return {
				in: path.join(clientSrc, entry),
				out: entry,
			};
		}),
		...blockEntries.map((entry) => {
			return {
				in: path.join(clientSrc, entry),
				out: `${entry.replace('components/', '')}-${hashPlaceholder}`,
			};
		}),
		...vendorEntries.map((entry) => {
			return {
				in: path.join(clientSrc, entry),
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

const outputHashes = new Map();

const hashImportsPlugin = () => {
	return {
		name: 'hash-imports',
		setup(build) {
			build.onEnd(async (result) => {
				const outputs = new Map();
				const hashRegex = new RegExp(
					`\\.\\.\\/[\\w\\/]*-${hashPlaceholder}\\.js`,
					'g'
				);

				result.outputFiles.forEach((out) => {
					if (out.path.match(/\.js$/)) {
						const content = new TextDecoder('utf-8').decode(
							out.contents
						);

						const pathRelative = makeRelative(out.path);
						const pathAbsolute = out.path;
						const hashedImports = content.match(hashRegex) ?? [];
						const hash = fileHash(content);

						outputs.set(pathRelative, {
							content,
							hash,
							pathRelative,
							pathAbsolute,
							hashedImports,
							newPathAbsolute: null,
						});
					} else {
						// Write all non-JS files.
						if (
							!outputHashes.get(out.path) ||
							outputHashes.get(out.path) != out.hash
						) {
							write(out.path, out.contents);
							outputHashes.set(out.path, out.hash);
						}
					}
				});

				outputs.forEach((o) => {
					o.dependents = [];

					outputs.forEach((d) => {
						if (d.hashedImports?.includes(o.pathRelative)) {
							o.dependents.push(d.pathRelative);
						}
					});
				});

				while (outputs.size > 0) {
					let nextKeys = [];

					outputs.forEach((o, key) => {
						if (
							o.hashedImports === null ||
							o.hashedImports?.length == 0
						) {
							nextKeys.push(key);
						}
					});

					nextKeys.forEach((key) => {
						const o = outputs.get(key);

						o.newPathAbsolute = o.pathAbsolute.replace(
							hashPlaceholder,
							o.hash
						);

						const newImport = o.pathRelative.replace(
							hashPlaceholder,
							o.hash
						);

						o.dependents.forEach((path) => {
							const d = outputs.get(path);

							d.content = d.content.replaceAll(
								o.pathRelative,
								newImport
							);

							d.hash = fileHash(d.content);
							d.hashedImports = d.content.match(hashRegex) ?? [];
						});

						if (
							!outputHashes.get(key) ||
							outputHashes.get(key) != o.hash
						) {
							fs.globSync(
								o.pathAbsolute.replace(
									/-[a-zA-Z0-9@]+\.js/,
									'-*.js'
								)
							).forEach((f) => {
								if (fs.existsSync(f)) {
									fs.unlinkSync(f);
								}
							});

							write(o.newPathAbsolute, o.content);
							outputHashes.set(key, o.hash);
						}

						outputs.delete(key);
					});
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
	minify: !isDev,
	target: ['es2022'],
	assetNames: '[name]',
	write: false,
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
	metafile: true,
	define: { DEVELOPMENT: isDev.toString() },
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
