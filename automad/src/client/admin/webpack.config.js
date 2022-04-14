const path = require('path');
const { merge } = require('webpack-merge');
const common = require('../../../../webpack.common');

module.exports = (env, argv) =>
	merge(common(env, argv), {
		entry: {
			main: './automad/src/client/admin/index.ts',
		},
		output: {
			path: path.resolve(__dirname, '../../../dist/admin'),
			filename: '[name].bundle.js',
		},
	});
