const { defineConfig } = require('vitest/config');
const path = require('node:path');

export default defineConfig({
	root: './automad/src/client/admin',
	plugins: [],
	test: {
		environment: 'jsdom',
		setupFiles: ['./tests/setup.ts'],
		include: ['**/tests/**/*.test.ts'],
		reporters: ['default', 'verbose'],
		slowTestThreshold: 15,
		globals: true,
	},
	resolve: {
		alias: {
			'@': path.resolve(__dirname, 'automad/src/client'),
		},
	},
});
