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
import esbuild from 'esbuild';
import postcss from 'esbuild-postcss';
import browserSync from 'browser-sync';
import { lessLoader } from 'esbuild-plugin-less';
import { sassPlugin } from 'esbuild-sass-plugin';
import { fileURLToPath } from 'node:url';
import crypto from 'node:crypto';
import path from 'node:path';
import fs from 'node:fs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
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

	// Prepare the entryPoints by adding a hash placeholder to all
	// non-index files.
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

	// The items in the alias list are modified by adding the hash placeholder
	// accordingly  to match the entryPoints config.
	blockEntries.forEach((entry) => {
		const key = `@/${entry}`;

		alias[key] =
			`../${entry.replace('components/', '')}-${hashPlaceholder}.js`;
	});

	vendorEntries.forEach((entry) => {
		const key = `@/${entry}`;

		alias[key] = `../${entry}-${hashPlaceholder}.js`;
	});

	// Now mark all non-index files with their hashed filenames as external.
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
				const placeholderRegex = new RegExp(
					`\\.\\.\\/[\\w\\/]*-${hashPlaceholder}\\.js`,
					'g'
				);

				// First the esbuild output files array is converted
				// to a custom output map with additional data.
				result.outputFiles.forEach((outputFile) => {
					if (outputFile.path.match(/\.js$/)) {
						const content = new TextDecoder('utf-8').decode(
							outputFile.contents
						);

						const pathRelative = makeRelative(outputFile.path);
						const pathAbsolute = outputFile.path;

						// Placeholder imports are imports that contain
						// the @hash placeholder in their path.
						const placeholderImports =
							content.match(placeholderRegex) ?? [];

						outputs.set(pathRelative, {
							content,
							pathRelative,
							pathAbsolute,
							placeholderImports,
							newPathAbsolute: null,
						});
					} else {
						// Write all non-JS assets directly and without hashing
						// since they are not async imported.
						if (
							!outputHashes.get(outputFile.path) ||
							outputHashes.get(outputFile.path) != outputFile.hash
						) {
							write(outputFile.path, outputFile.contents);
							outputHashes.set(outputFile.path, outputFile.hash);
						}
					}
				});

				// Now iterate all output files once and store all files
				// that import it inside the dependents array.
				outputs.forEach((out) => {
					out.dependents = [];

					outputs.forEach((dep) => {
						if (
							dep.placeholderImports?.includes(out.pathRelative)
						) {
							out.dependents.push(dep.pathRelative);
						}
					});
				});

				// In order to resolve all paths correctly and hash their content
				// after the contained import placeholders are also resolved
				// and hashed as well, the output map is processed in a
				// while loop as longs as it contains elements.
				while (outputs.size > 0) {
					let finalizedFiles = [];

					// During each iteration, all files that do not contain any import
					// placeholders, are considered clean and finalized" and will be
					// added to the array of files that can be hashed and processed.
					outputs.forEach((out, key) => {
						if (out.placeholderImports.length == 0) {
							finalizedFiles.push(key);
						}
					});

					// That hash is used to update the file path by replacing
					// the placeholder with it. Then all references in its dependents are updated as well.
					finalizedFiles.forEach((key) => {
						const out = outputs.get(key);

						// When processing the clean and finalized files, first,
						// a hash of the file content is generated.
						const hash = fileHash(out.content);

						// That hash is used to update the file path by replacing
						// the placeholder with it.
						out.newPathAbsolute = out.pathAbsolute.replace(
							hashPlaceholder,
							hash
						);

						const newImport = out.pathRelative.replace(
							hashPlaceholder,
							hash
						);

						out.dependents.forEach((path) => {
							const dep = outputs.get(path);

							// Then all file references in its dependents are updated as well.
							dep.content = dep.content.replaceAll(
								out.pathRelative,
								newImport
							);

							// Now the finalized and hashed imports are removed from the
							// dependent's list of placeholder imports.
							dep.placeholderImports =
								dep.content.match(placeholderRegex) ?? [];
						});

						if (
							!outputHashes.get(key) ||
							outputHashes.get(key) != hash
						) {
							// In case the hash has changed since the last build or it is
							// the first build, remove old versions of the file first.
							fs.globSync(
								out.pathAbsolute.replace(
									/-[a-zA-Z0-9@]+\.js/,
									'-*.js'
								)
							).forEach((f) => {
								if (fs.existsSync(f)) {
									fs.unlinkSync(f);
								}
							});

							// Then write the content to the file with the new hashed filename.
							write(out.newPathAbsolute, out.content);

							// And store the hash in the cross-build map.
							outputHashes.set(key, hash);
						}

						// Finally remove the file form the outputs map.
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
	pure: isDev ? [] : ['console.warn'],
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
