const path = require('path');
const { merge } = require('webpack-merge');
const common = require('../../../../webpack.common');

module.exports = (env, argv) =>
	merge(common(env, argv), {
		entry: {
			main: './automad/src/client/admin/index.ts',
		},
		resolve: {
			extensions: ['.ts', '.js'],
			alias: {
				'@': path.resolve(__dirname, './'),
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
				react: path.resolve(
					__dirname,
					'../../../../node_modules/react'
				),
			},
		},
		output: {
			path: path.resolve(__dirname, '../../../dist/admin'),
			filename: '[name].bundle.js',
		},
	});
