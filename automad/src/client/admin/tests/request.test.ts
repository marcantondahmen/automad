import { setNodeByPath } from '../core';

test('setNodebyPath', () => {
	const tree = {};

	setNodeByPath(tree, 'simple', 'value');
	setNodeByPath(tree, 'object[test]', 'hello');
	setNodeByPath(tree, 'object[nested][key1]', 'hello');
	setNodeByPath(tree, 'object[nested][key2]', 'world');
	setNodeByPath(tree, 'object[array][][key1]', 'value1');
	setNodeByPath(tree, 'object[array][][key2]', 'value2');
	setNodeByPath(tree, 'object[array][][key3]', 'value3');
	setNodeByPath(tree, 'object[array2][]', 'value1');
	setNodeByPath(tree, 'object[array2][]', 'value2');
	setNodeByPath(tree, 'object[array2][]', 'value3');

	expect(tree).toEqual({
		simple: 'value',
		object: {
			test: 'hello',
			nested: {
				key1: 'hello',
				key2: 'world',
			},
			array: {
				0: { key1: 'value1' },
				1: { key2: 'value2' },
				2: { key3: 'value3' },
			},
			array2: {
				0: 'value1',
				1: 'value2',
				2: 'value3',
			},
		},
	});
});
