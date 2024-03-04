import { collectFieldData, create, FieldTag, query } from '@/admin/core';

test('collectFieldData (nested)', () => {
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

test('collectFieldData (checkbox)', () => {
	const element = create(
		'div',
		[],
		{},
		null,
		`
			<input name="input" value="string" />
			<input type="checkbox" name="checkbox1" value="1" checked />
			<input type="checkbox" name="checkbox2" value="1" />
		`
	);

	const data = collectFieldData(element);

	expect(data).toEqual({
		input: 'string',
		checkbox1: true,
	});
});

test('collectFieldData (radio)', () => {
	const element = create(
		'div',
		[],
		{},
		null,
		`
			<input name="input" value="string" />
			<input type="radio" name="radio" value="1" />
			<input type="radio" name="radio" value="2" checked />
			<input type="radio" name="radio" value="3" />
		`
	);

	const data = collectFieldData(element);

	expect(data).toEqual({
		input: 'string',
		radio: '2',
	});
});
