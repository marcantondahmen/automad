/** @type {import('ts-jest').JestConfigWithTsJest} */
const config = {
	verbose: true,
	rootDir: './automad/src/client/admin',
	preset: 'ts-jest',
	testEnvironment: 'jest-environment-jsdom',
	testMatch: ['**/tests/**/*.test.ts'],
	setupFilesAfterEnv: ['<rootDir>/tests/setup.ts'],
	moduleNameMapper: {
		'^@/(.*)$': '<rootDir>/../$1',
	},
	modulePaths: ['<rootDir>/../../../../node_modules'],
	transform: {
		'^.+\\.ts$': 'ts-jest',
		'\\.(jpg|jpeg|png|gif|eot|otf|webp|svg|ttf|woff|woff2|mp4|webm|wav|mp3|m4a|aac|oga)$':
			'<rootDir>/tests/fileTransformer.js',
	},
	slowTestThreshold: 15,
};

module.exports = config;
