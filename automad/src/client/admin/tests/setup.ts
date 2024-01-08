import { FieldTag, FormDataProviders } from '@/core';

jest.mock('nanoid', () => {
	return { nanoid: () => 'xxxx' };
});

jest.mock('@editorjs/nested-list', () => {
	return {
		default: jest.fn().mockImplementation(() => {
			return {};
		}),
	};
});

jest.mock('@editorjs/table', () => {
	return {
		default: jest.fn().mockImplementation(() => {
			return {};
		}),
	};
});

FormDataProviders.add(FieldTag.editor);
