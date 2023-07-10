import { FieldTag, FormDataProviders } from '@/core';

jest.mock('nanoid', () => {
	return { nanoid: () => 'xxxx' };
});

FormDataProviders.add(FieldTag.editor);
