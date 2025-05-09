const path = require('path');
const webpack = require('webpack');
const { merge } = require('webpack-merge');
const autoprefixer = require('autoprefixer');
const TerserPlugin = require('terser-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-v3-webpack-plugin');
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
						{
							loader: 'postcss-loader',
							options: {
								postcssOptions: {
									plugins: [autoprefixer()],
								},
							},
						},
						'less-loader',
					],
				},
				{
					test: /\.svg$/,
					loader: 'html-loader',
				},
				{
					test: /\.woff2?$/i,
					type: 'asset/resource',
					generator: {
						filename: '../fonts/[name][ext][query]',
					},
				},
			],
		},
		resolve: {
			extensions: ['.ts', '.js'],
			alias: {
				'@': path.resolve(__dirname, './automad/src/client'),
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
			filerobot: {
				test: /(@scaleflex|filerobot|react)/,
				chunks: 'all',
				name: 'vendor.filerobot',
				enforce: true,
				priority: 1,
			},
			editorjs: {
				test: /(@editorjs|codex)/,
				chunks: 'all',
				name: 'vendor.editorjs',
				enforce: true,
				priority: 2,
			},
			toastui: {
				test: /@toast/,
				chunks: 'all',
				name: 'vendor.toastui',
				enforce: true,
				priority: 3,
			},
		},
	};

	if (argv.mode === 'development') {
		config.plugins.push(
			new BrowserSyncPlugin({
				host: 'localhost',
				port: 3000,
				proxy: 'http://127.0.0.1:8080/automad-development',
				files: ['**/*.php', './automad/dist/*/main.bundle.*'],
				ignore: ['config/*', 'packages/**/*.php', 'vendor/**/*.php'],
				notify: false,
				open: false,
			})
		);
	}

	return config;
};

const features = ['blocks', 'consent', 'mail', 'inpage', 'prism'].map(
	(name) => (env, argv) => {
		return merge(common(env, argv), {
			entry: {
				main: `./automad/src/client/${name}/index.ts`,
			},
			output: {
				path: path.resolve(__dirname, `./automad/dist/${name}`),
				filename: '[name].bundle.js',
			},
		});
	}
);

module.exports = [admin, ...features];
