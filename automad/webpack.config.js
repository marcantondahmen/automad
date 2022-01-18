const path = require('path');
const { merge } = require('webpack-merge');
const common = require('../webpack.common');

module.exports = (env, argv) =>
	merge(common(env, argv), {
		entry: {
			ui: './automad/ui/src/ui.ts',
		},
		output: {
			path: path.resolve(__dirname, 'dist'),
			filename: '[name].bundle.js',
		},
	});
