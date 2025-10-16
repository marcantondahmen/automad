import { vi } from 'vitest';
import { FieldTag, FormDataProviders } from '@/admin/core';

vi.mock('prismjs', () => {
	return {
		default: { languages: { extend: () => {} } },
	};
});

vi.mock('@editorjs/embed', () => {
	return {
		default: vi.fn().mockImplementation(() => {
			return {};
		}),
	};
});

vi.mock('@editorjs/nested-list', () => {
	return {
		default: vi.fn().mockImplementation(() => {
			return {};
		}),
	};
});

vi.mock('@editorjs/table', () => {
	return {
		default: vi.fn().mockImplementation(() => {
			return {};
		}),
	};
});

FormDataProviders.add(FieldTag.editor);
