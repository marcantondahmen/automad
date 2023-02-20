/** @type {import('jest').Config} */
const config = {
	verbose: true,
	preset: 'ts-jest',
	testEnvironment: 'jest-environment-jsdom',
	testMatch: ['**/tests/**/*.test.ts'],
	setupFilesAfterEnv: ['<rootDir>/automad/src/client/admin/tests/setup.ts'],
	watchPathIgnorePatterns: [
		'<rootDir>/automad/dist/',
		'<rootDir>/node_modules/',
	],
};

module.exports = config;
