const path = require('path');
const webpack = require('webpack');
const { merge } = require('webpack-merge');
const TerserPlugin = require('terser-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const pkg = require('./package.json');

const optimizeTemplate = (html) => {
	return html
		.replace(/\$\{"([^"]+)"(\s\/\*\s[\w\.\-]+\s\*\/)?\}/g, '$1')
		.replace(/\s+/g, ' ')
		.replace(/\<\s+/g, '<')
		.replace(/\s+\>/g, '>')
		.replace(/\>\s+\</g, '><')
		.replace(/(^`\s|\s`$)/g, '`');
};

class SystemBellPlugin {
	pluginName = 'SystemBellPlugin';

	apply(compiler) {
		compiler.hooks.done.tap(this.pluginName, (stats) => {
			if (stats.compilation.errors.length > 0) {
				process.stdout.write('\x07');
			}
		});
	}
}

const common = (env, argv) => {
	const devMode = argv.mode === 'development';
	const config = {
		module: {
			rules: [
				{
					test: /\.ts$/,
					use: [
						{
							loader: 'string-replace-loader',
							options: {
								search: /(`[^`]+`)/g,
								replace(match, p1, offset, string) {
									return optimizeTemplate(p1);
								},
							},
						},
						{
							loader: 'ts-loader',
						},
					],
					exclude: /node_modules/,
				},
				{
					test: /\.(less|css)$/i,
					use: [
						MiniCssExtractPlugin.loader,
						'css-loader',
						'less-loader',
					],
				},
				{
					test: /\.svg$/,
					loader: 'html-loader',
				},
				// Fix file name of inter font.
				{
					test: /\.woff2?$/i,
					type: 'asset/resource',
					generator: {
						filename: (pathData) => {
							const name = path.basename(
								pathData.module.resourceResolveData.relativePath
							);

							return `../fonts/${name
								.replace('.var', '-var')
								.toLowerCase()}`;
						},
					},
				},
				// Ignore Bootstrap icons legacy woff file.
				{
					test: /bootstrap-icons\.css$/i,
					loader: 'string-replace-loader',
					options: {
						search: /,\s*url\("[^"]+\/bootstrap-icons\.woff\?\w+"\)\s*format\("woff"\)/g,
						replace: '',
					},
				},
			],
		},
		optimization: {
			minimizer: [
				new CssMinimizerPlugin(),
				new TerserPlugin({
					extractComments: false,
				}),
				'...',
			],
		},
		plugins: [
			new MiniCssExtractPlugin({
				filename: '[name].bundle.css',
			}),
			new SystemBellPlugin(),
			new webpack.BannerPlugin({
				banner: () => {
					const year = new Date().getFullYear();

					return `Automad ${pkg.version}, (c) ${year} ${pkg.author}, ${pkg.license} license`;
				},
				exclude: /vendor/,
			}),
			new webpack.DefinePlugin({
				DEVELOPMENT: JSON.stringify(devMode),
			}),
		],
	};

	if (devMode) {
		config.watch = true;
		config.devtool = 'source-map';
	}

	return config;
};

const admin = (env, argv) => {
	const config = merge(common(env, argv), {
		entry: {
			main: './automad/src/client/admin/index.ts',
		},
		resolve: {
			extensions: ['.ts', '.js'],
			alias: {
				'@': path.resolve(__dirname, './automad/src/client/admin'),
				common: path.resolve(__dirname, './automad/src/client/common'),
				// Add this alias to make FileRobot imports work.
				// React is only used as dependency of FileRobot but will be installed in two locations:
				// 1. node_modules/react
				// 2. node_modules/filerobot-image-editor/node_modules/react
				//
				// It is important to make sure that react is only imported once during bundling and therefore
				// the alias has to be added here.
				//
				// https://github.com/scaleflex/filerobot-image-editor/issues/107#issuecomment-886589896
				// https://github.com/facebook/react/issues/13991#issuecomment-983316545
				react: path.resolve(__dirname, './node_modules/react'),
			},
		},
		output: {
			path: path.resolve(__dirname, './automad/dist/admin'),
			filename: '[name].bundle.js',
		},
	});

	config.optimization.splitChunks = {
		cacheGroups: {
			vendor: {
				test: /node_modules/,
				chunks: 'all',
				name: 'vendor',
				enforce: true,
			},
		},
	};

	if (argv.mode === 'development') {
		config.plugins.push(
			new BrowserSyncPlugin({
				host: 'localhost',
				port: 3000,
				proxy: 'http://127.0.0.1:8080/automad-development',
				files: [
					'**/*.php',
					'./automad/src/client/admin/mockup/**/*.html',
					'./automad/dist/blocks/main.bundle.*',
				],
				ignore: ['config/*', 'packages/**/*.php', 'vendor/**/*.php'],
				notify: false,
			})
		);
	}

	return config;
};

const blocks = (env, argv) =>
	merge(common(env, argv), {
		entry: {
			main: './automad/src/client/blocks/index.ts',
		},
		resolve: {
			extensions: ['.ts', '.js'],
			alias: {
				'~': path.resolve(__dirname, './automad/src/client/blocks'),
				common: path.resolve(__dirname, './automad/src/client/common'),
			},
		},
		output: {
			path: path.resolve(__dirname, './automad/dist/blocks'),
			filename: '[name].bundle.js',
		},
	});

module.exports = [admin, blocks];
