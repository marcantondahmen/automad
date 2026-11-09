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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { debounce } from '@/common';
import { App } from '@/admin/core';
import { EditorJSComponent } from '@/admin/components/EditorJS';
import { BasePlugin } from './BasePlugin';
import { AiRuntime } from '@/admin/editor/ai';
import type { AiRuntimeState } from '../types';

/**
 * The AI extension for EditorJS.
 */
export class AiAssistance extends BasePlugin {
	/**
	 * The actual plugin implementation.
	 */
	protected init(): void {
		if (!App.system.aiAssistance.enabled) {
			return;
		}

		this.component.listen(
			this.component,
			'mouseup',
			debounce((event: MouseEvent) => {
				if (AiRuntime.get().pending) {
					return;
				}

				const target = event.target as HTMLElement;

				if (
					target.closest(EditorJSComponent.TAG_NAME) &&
					target.closest(EditorJSComponent.TAG_NAME) ===
						this.component
				) {
					const selectedRange = this.getEditableRange();

					const runtimeState: AiRuntimeState = {
						plugin: this,
						component: this.component,
						selectedBlocks:
							this.editor.blockSelection.selectedBlocks,
						selectedRange,
						selectionDisplay: selectedRange?.toString() || '',
						lastFocusedBlockIndex: target.closest('.ce-block')
							? this.editor.blocks.getCurrentBlockIndex()
							: -1,
					};

					if (runtimeState.selectedBlocks.length > 0) {
						const count = runtimeState.selectedBlocks.length;

						runtimeState.selectionDisplay = `↪ ${count} ${count > 1 ? App.text('blocks') : App.text('block')}`;
						runtimeState.selectedRange = null;
					}

					AiRuntime.get().setState(runtimeState);
				}
			})
		);
	}

	/**
	 * Get the current selection range, but only in case it is located
	 * within a single editable element of a block of this editor.
	 * Ranges that extend to other UI elements such as field labels are ignored.
	 *
	 * @return the range or null
	 */
	private getEditableRange(): Range | null {
		const sel = window.getSelection();

		if (!sel.rangeCount) {
			return null;
		}

		const range = sel.getRangeAt(0);

		const getEditable = (node: Node): HTMLElement | null => {
			const element =
				node instanceof HTMLElement ? node : node.parentElement;

			return element?.closest('[contenteditable="true"]') ?? null;
		};

		const editable = getEditable(range.startContainer);

		if (
			!editable ||
			editable !== getEditable(range.endContainer) ||
			!editable.closest('.ce-block') ||
			editable.closest(EditorJSComponent.TAG_NAME) !== this.component
		) {
			return null;
		}

		return range.cloneRange();
	}
}
