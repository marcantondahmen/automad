/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { BlockAPI } from '@/vendor/editorjs';
import { EditorJSComponent } from '@/admin/components/EditorJS';
import { query } from '@/admin/core';
import { insertBlock } from '../utils';
import { BasePlugin } from './BasePlugin';

/**
 * Drag and drop extension for EditorJS.
 * This extensions allows for moving blocks around within an editor
 * instance as well as between different editors.
 */
export class DragDrop extends BasePlugin {
	/**
	 * The currently dragged block and its index.
	 *
	 * @static
	 */
	private static CURRENT: {
		block: BlockAPI;
		index: number;
	};

	/**
	 * The drop target.
	 *
	 * @static
	 */
	private static TARGET: HTMLElement;

	/**
	 * The actual plugin implementation.
	 */
	protected init(): void {
		const handle = query(
			':scope > .codex-editor > .ce-toolbar .ce-toolbar__settings-btn',
			this.component
		);

		if (handle) {
			handle.setAttribute('draggable', 'true');

			this.component.listen(handle, 'dragstart', () => {
				const block = this.getCurrentBlock();

				DragDrop.CURRENT = {
					block,
					index: DragDrop.getBlockIndex(block.holder),
				};
			});

			this.component.listen(handle, 'drag', () => {
				this.editor.toolbar.close();
			});
		}

		this.component.listen(
			this.component,
			'dragover',
			(event: DragEvent) => {
				event.stopImmediatePropagation();

				const target = event.target as HTMLElement;
				const targetBlock = target.closest<HTMLElement>('.ce-block');

				if (targetBlock) {
					DragDrop.TARGET = targetBlock;
					DragDrop.setSelection(DragDrop.TARGET);

					return;
				}

				const targetEditor = target.closest<EditorJSComponent>(
					EditorJSComponent.TAG_NAME
				);

				if (!targetEditor) {
					return;
				}

				const firstBlock = query<HTMLElement>('.ce-block');

				if (!firstBlock) {
					return;
				}

				DragDrop.TARGET = firstBlock;
				DragDrop.setSelection(DragDrop.TARGET);
			}
		);

		this.component.listen(
			this.component,
			'drop',
			async (event: DragEvent) => {
				event.stopImmediatePropagation();

				await DragDrop.move();
			}
		);
	}

	/**
	 * Move a block. Either within a single editor or between two different editors.
	 */
	private static async move(): Promise<void> {
		if (DragDrop.CURRENT?.block.holder == DragDrop.TARGET) {
			return;
		}

		if (DragDrop.CURRENT?.block.holder.contains(DragDrop.TARGET)) {
			return;
		}

		if (!DragDrop.TARGET || !DragDrop.CURRENT) {
			return;
		}

		const sourceComponent = DragDrop.getParentEditorComponent(
			DragDrop.CURRENT.block.holder
		);

		const sourceIndex = DragDrop.CURRENT.index;

		const targetBlock = DragDrop.TARGET;
		const targetComponent = DragDrop.getParentEditorComponent(targetBlock);
		const targetIndex = DragDrop.getBlockIndex(targetBlock);

		const scrolled = window.scrollY;

		if (sourceComponent === targetComponent) {
			sourceComponent.editor.blocks.move(targetIndex, sourceIndex);

			await DragDrop.refresh(sourceComponent.editor);

			sourceComponent.editor.caret.setToBlock(targetIndex, 'end');
		} else {
			await insertBlock(
				DragDrop.CURRENT.block,
				targetComponent.editor,
				targetIndex
			);

			sourceComponent.editor.blocks.delete(sourceIndex);
			targetComponent.editor.caret.setToBlock(targetIndex, 'end');
		}

		DragDrop.CURRENT = null;
		DragDrop.TARGET = null;

		setTimeout(() => {
			window.scrollTo(0, scrolled);
		}, 0);
	}
}
