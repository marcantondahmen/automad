const path = require('path');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

module.exports = (env, argv) => {
	const config = {
		module: {
			rules: [
				{
					test: /\.ts$/,
					use: 'ts-loader',
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
					test: /\.woff2?$/i,
					type: 'asset/resource',
					generator: {
						filename: (pathData) => {
							const name = path.basename(
								pathData.module.resourceResolveData.relativePath
							);

							return `./fonts/${name
								.replace('.var', '-var')
								.toLowerCase()}`;
						},
					},
				},
			],
		},
		resolve: {
			extensions: ['.ts', '.js'],
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
			})
		);

		config.watch = true;
		config.devtool = 'source-map';
	} else {
		console.log('Production Mode');
	}

	return config;
};
