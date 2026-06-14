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
import { AiRuntimeState } from '@/admin/types/editor/plugins';
import { EditorJSComponent } from '@/admin/components/EditorJS';
import { BasePlugin } from './BasePlugin';
import { AiRuntime } from '@/admin/editor/ai';

/**
 * The AI extension for EditorJS.
 */
export class AiAssistance extends BasePlugin {
	/**
	 * The actual plugin implementation.
	 */
	protected init(): void {
		if (!App.system.ai.enabled) {
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
					const sel = window.getSelection();
					const selectedRange = sel.rangeCount
						? sel.getRangeAt(0).cloneRange()
						: null;

					const runtimeState: AiRuntimeState = {
						plugin: this,
						component: this.component,
						selectedBlocks:
							this.editor.blockSelection.selectedBlocks,
						selectedRange,
						selectionDisplay: selectedRange?.toString() || '',
						lastFocusedBlockIndex:
							this.editor.blocks.getCurrentBlockIndex(),
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
}
