/*
 * This EditorJS block is based on the original list block by CodeX and
 * is extended to support the Automad block grid layout.
 * https://github.com/editor-js/list
 *
 * Copyright (c) 2018 CodeX (team@ifmo.su)
 * Copyright (c) 2021 Marc Anton Dahmen
 * MIT License
 */

class AutomadBlockList extends NestedList {
	static get toolbox() {
		return {
			icon: '<svg width="17" height="13" viewBox="0 0 17 13" xmlns="http://www.w3.org/2000/svg"> <path d="M5.625 4.85h9.25a1.125 1.125 0 0 1 0 2.25h-9.25a1.125 1.125 0 0 1 0-2.25zm0-4.85h9.25a1.125 1.125 0 0 1 0 2.25h-9.25a1.125 1.125 0 0 1 0-2.25zm0 9.85h9.25a1.125 1.125 0 0 1 0 2.25h-9.25a1.125 1.125 0 0 1 0-2.25zm-4.5-5a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25zm0-4.85a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25zm0 9.85a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25z"/></svg>',
			title: AutomadEditorTranslation.get('list_toolbox'),
		};
	}
}
