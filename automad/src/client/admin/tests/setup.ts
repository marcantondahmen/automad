import { FieldTag, FormDataProviders } from '@/admin/core';

jest.mock('prismjs', () => {
	return {
		default: { languages: { extend: () => {} } },
	};
});

jest.mock('nanoid', () => {
	return { nanoid: () => 'xxxx' };
});

jest.mock('@editorjs/embed', () => {
	return {
		default: jest.fn().mockImplementation(() => {
			return {};
		}),
	};
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
