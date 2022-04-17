const path = require('path');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

const minifyHTML = (html) => {
	return html
		.replace(/\s+/g, ' ')
		.replace(/\<\s+/g, '<')
		.replace(/\s+\>/g, '>')
		.replace(/\>\s+\</g, '><')
		.replace(/(^`\s|\s`$)/g, '`');
};

module.exports = (env, argv) => {
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
									return minifyHTML(p1);
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
		resolve: {
			extensions: ['.ts', '.js'],
			alias: {
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
				react: path.resolve(__dirname, 'node_modules/react'),
			},
		},
		optimization: {
			minimizer: [new CssMinimizerPlugin(), '...'],
			splitChunks: {
				cacheGroups: {
					vendor: {
						test: /node_modules/,
						chunks: 'all',
						name: 'vendor',
						enforce: true,
					},
				},
			},
		},
		plugins: [
			new MiniCssExtractPlugin({
				filename: '[name].bundle.css',
			}),
		],
	};

	if (argv.mode === 'development') {
		console.log('Development Mode');

		config.plugins.push(
			new BrowserSyncPlugin({
				host: 'localhost',
				port: 3000,
				proxy: 'http://localhost:8080/automad-development',
				files: ['**/*.php'],
				ignore: ['config/*', 'packages/**/*.php', 'vendor/**/*.php'],
			})
		);

		config.watch = true;
		config.devtool = 'source-map';
	} else {
		console.log('Production Mode');
	}

	return config;
};
