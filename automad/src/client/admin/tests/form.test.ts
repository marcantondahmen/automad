import { collectFieldData, create, FieldTag, query } from '@/core';

test('collectFieldData', () => {
	const tag = FieldTag.editor;
	const element = create(
		'div',
		[],
		{},
		null,
		`
			<div class="main">
				<input name="field1" value="value1" />
				<input name="field2" value="value2" />
				<${tag} class="editor">
					<input name="field3" value="value3" />
					<input name="field4" value="value4" />
					<${tag} class="nested">
						<input name="field5" value="value5" />
						<input name="field6" value="value6" />
					</${tag}>
				</${tag}>
			</div>
		`
	);

	const main = query('.main', element);
	const editor = query('.editor', element);
	const nested = query('.nested', element);

	const mainData = collectFieldData(main);
	const editorData = collectFieldData(editor);
	const nestedData = collectFieldData(nested);

	expect(mainData).toEqual({ field1: 'value1', field2: 'value2' });
	expect(editorData).toEqual({ field3: 'value3', field4: 'value4' });
	expect(nestedData).toEqual({ field5: 'value5', field6: 'value6' });
});
