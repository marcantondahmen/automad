import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vitest/config';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

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
