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

import {
	AiAssistanceController,
	App,
	create,
	CSS,
	EventName,
	FieldTag,
	fire,
	getLogger,
	query,
	queryAll,
	requestAPI,
} from '@/admin/core';
import { AiRuntimeState, AiTarget } from '@/admin/types/editor/plugins';
import { BlockToolData } from 'automad-editorjs';
import { EditorFieldComponent } from '@/admin/components/Fields/EditorField';
import { EditorAiAssistanceComponent } from '@/admin/components/EditorAiAssistance';

/**
 * Get all page blocks.
 *
 * @return all blocks of the currentr page
 * @async
 */
const getAllPageBlocks = async (): Promise<BlockToolData[]> => {
	const editorFields = queryAll<EditorFieldComponent>(FieldTag.editor);

	const savedEditors = await Promise.all(
		editorFields.map(async (field) => {
			const editor = field.editorJS.editor;

			return await editor.save();
		})
	);

	const blocks = savedEditors.reduce((acc, data) => {
		acc = [...acc, ...data.blocks];

		return acc;
	}, []);

	return blocks;
};

/**
 * The AI Runtime class serves as the link between all editors and the AI assistant UI.
 */
export class AiRuntime {
	/**
	 * The singelton runtime instance.
	 *
	 * @static
	 */
	private static instance: AiRuntime = null;

	/**
	 * The runtime state.
	 */
	private state: AiRuntimeState = null;

	/**
	 * Indicates a pending generation response.
	 */
	private _pending: boolean = false;

	/**
	 * The pending state setter.
	 */
	private set pending(state: boolean) {
		this._pending = state;

		fire(EventName.aiRuntimeStateChange);
	}

	/**
	 * The pending state getter.
	 */
	get pending() {
		return this._pending;
	}

	/**
	 * The AbortController for stopping long running generations.
	 */
	private abortController: AbortController | null = null;

	/**
	 * The constructor.
	 */
	constructor() {
		getLogger().log('New AI runtime instance created');
	}

	/**
	 * The static getter that returns the singelton instance.
	 *
	 * @return the instance
	 * @static
	 */
	static get(): AiRuntime {
		if (!App.system.ai.enabled) {
			return;
		}

		if (!AiRuntime.instance) {
			AiRuntime.instance = new AiRuntime();
		}

		return AiRuntime.instance;
	}

	/**
	 * Generate text either append it to the editor or rewrite the current selection.
	 *
	 * @param prompt
	 * @param providerId
	 * @return true on success
	 * @async
	 */
	async generate(prompt: string, providerId: string): Promise<boolean> {
		this.abortController = new AbortController();
		this.pending = true;

		const lockId = App.addNavigationLock();

		const { data, error } = await requestAPI(
			AiAssistanceController.text,
			{
				providerId,
				prompt,
				context: await getAllPageBlocks(),
				target: await this.getTarget(),
			},
			true,
			null,
			true,
			this.abortController
		);

		App.removeNavigationLock(lockId);

		this.pending = false;

		if (error) {
			return false;
		}

		const output = data?.output || null;

		if (!output) {
			return true;
		}

		fire(EventName.appStateRequireUpdate);

		const contentManager = new AiContentManager(this.state);

		if (this.state.selectedBlocks.length) {
			contentManager.replaceBlocks(output);

			return true;
		}

		if (
			this.state.selectedRange !== null &&
			this.state.selectedRange.startOffset !=
				this.state.selectedRange.endOffset
		) {
			contentManager.replaceRange(output);

			return true;
		}

		contentManager.insertBlocks(output);

		return true;
	}

	/**
	 * Abort a pending generation request.
	 */
	abortRequest(): void {
		this.abortController?.abort();
	}

	/**
	 * Destroy the runtime instance.
	 */
	destroy(): void {
		AiRuntime.instance = null;

		getLogger().log('AI runtime destoyed');
	}

	/**
	 * Expose the selection display.
	 *
	 * @return the current selection display
	 */
	getSelectionDisplay(): string {
		return this.state?.selectionDisplay ?? '';
	}

	/**
	 * Set the runtime state.
	 *
	 * @param state
	 */
	setState(state: AiRuntimeState): void {
		this.state = state;
		this.toggleSelectionHighlighting(true);

		fire(EventName.aiRuntimeStateChange);
		getLogger().log(this.state);
	}

	/**
	 * Toggle highlighting of selected editor and blocks.
	 *
	 * @param active
	 */
	toggleSelectionHighlighting(active: boolean = true): void {
		const details = query<HTMLDetailsElement>(
			`${EditorAiAssistanceComponent.TAG_NAME} > details`
		);

		setTimeout(() => {
			queryAll(`.${CSS.editorAiAssistanceSelectedEditor}`).forEach(
				(editor) => {
					editor.classList.remove(
						CSS.editorAiAssistanceSelectedEditor
					);
				}
			);

			queryAll(`.${CSS.editorAiAssistanceSelectedBlock}`).forEach(
				(block) => {
					block.classList.remove(CSS.editorAiAssistanceSelectedBlock);
				}
			);

			if (active && details.open) {
				this.state?.component.classList.add(
					CSS.editorAiAssistanceSelectedEditor
				);

				this.state?.selectedBlocks.forEach(
					(block: { holder: HTMLElement }) => {
						block.holder.classList.add(
							CSS.editorAiAssistanceSelectedBlock
						);
					}
				);
			}
		}, 0);
	}

	/**
	 * Get the target content to be transformed for the current request.
	 *
	 * @return the current context object
	 * @async
	 */
	private async getTarget(): Promise<AiTarget> {
		const target: AiTarget = {
			text: this.state.selectedRange?.toString() || '',
			blocks: [],
		};

		if (this.state.selectedBlocks.length > 0) {
			const saved = await this.state.component.editor.save();

			const selectedBlockIds: string[] = this.state.selectedBlocks.reduce(
				(ids: string[], block: { id: string }) => {
					ids.push(block.id);

					return ids;
				},
				[]
			);

			target.blocks = saved.blocks.filter((b: BlockToolData) =>
				selectedBlockIds.includes(b.id)
			);
		}

		return target;
	}
}

class AiContentManager {
	/**
	 * The runtime state.
	 */
	private state: AiRuntimeState;

	/**
	 * The constructor.
	 */
	constructor(state: AiRuntimeState) {
		this.state = state;
	}

	/**
	 * Insert new blocks at the end.
	 *
	 * @param content
	 * @async
	 */
	async insertBlocks(content: string): Promise<void> {
		if (this.state.lastFocusedBlockIndex !== null) {
			this.state.component.editor.caret.setToBlock(
				this.state.lastFocusedBlockIndex
			);
		} else {
			this.state.component.editor.caret.setToLastBlock();
		}

		this.state.component.editor.blocks.insert();

		await this.state.component.editor.paste.processText(content, true);

		fire('input', this.state.component);
	}

	/**
	 * Replace selected block with generated content.
	 *
	 * @param content
	 * @async
	 */
	async replaceBlocks(content: string): Promise<void> {
		if (!this.state.selectedBlocks?.length) {
			return;
		}

		const api = this.state.component.editor.blocks;
		const blocks = this.state.selectedBlocks;
		const firstBlockIndex = api.getBlockIndex(blocks[0].id);

		blocks.forEach((block: BlockToolData) => {
			api.delete(api.getBlockIndex(block.id));
		});

		api.insert(undefined, undefined, undefined, firstBlockIndex);

		this.state.component.editor.caret.setToBlock(firstBlockIndex);

		await this.state.component.editor.paste.processText(content, true);

		fire('input', this.state.component);
	}

	/**
	 * Replace selected text with generated content.
	 *
	 * @param content
	 * @async
	 */
	replaceRange(content: string): void {
		const div = create('div');

		div.innerHTML = content;

		const text = div.textContent || '';
		const range = this.state.selectedRange;

		div.remove();

		range.deleteContents();

		const node = document.createTextNode(text);

		range.insertNode(node);

		range.setStartAfter(node);
		range.collapse(true);

		const sel = window.getSelection();

		sel.removeAllRanges();
		sel.addRange(range);

		this.state.selectedRange = sel.getRangeAt(0).cloneRange();

		fire('input', this.state.component);
	}
}
