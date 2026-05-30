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
	App,
	createField,
	createGenericModal,
	createSelect,
	EventName,
	FieldTag,
	fire,
	getAiProviders,
	requestAPI,
} from '@/admin/core';
import {
	AiAssistanceController,
	create,
	debounce,
	query,
	queryAll,
	Route,
	Section,
} from '@/common';
import { BlockToolData, EditorJS } from '@/vendor/editorjs';
import { BasePlugin } from './BasePlugin';
import { AiContext } from '@/admin/types/editor/plugins';
import { AiProvider } from '@/admin/types';
import { EditorFieldComponent } from '@/admin/components/Fields/EditorField';

/**
 * The AI extension for EditorJS.
 */
export class AiAssistance extends BasePlugin {
	/**
	 * The class name for AI assistance buttons.
	 * This class is only used for selection and not for styling.
	 *
	 * @static
	 */
	private static BUTTON_CLASS = '__ai';

	/**
	 * The currently select blocks.
	 */
	private selectedBlocks: EditorJS['blockSelection']['selectedBlocks'] = [];

	/**
	 * The currently selected range.
	 */
	private selectedRange: Range = null;

	/**
	 * The actual plugin implementation.
	 */
	protected init(): void {
		if (!App.system.ai.enabled) {
			return;
		}

		// Exit early on re-renders, for example on undo or redo.
		if (query(`:scope > .${AiAssistance.BUTTON_CLASS}`, this.component)) {
			return;
		}

		const button = create(
			'span',
			[AiAssistance.BUTTON_CLASS],
			{},
			this.component,
			'Ai Assistance <i class="bi bi-stars"></i>'
		);

		this.component.listen(
			button,
			'mousedown selectionchange',
			(event: Event) => {
				event.preventDefault();

				this.renderModal();
			}
		);

		this.component.listen(
			this.component,
			'mouseup',
			debounce(() => {
				const sel = window.getSelection();

				this.selectedRange = sel.rangeCount
					? sel.getRangeAt(0).cloneRange()
					: null;

				this.selectedBlocks = this.editor.blockSelection.selectedBlocks;
			})
		);
	}

	/**
	 * Navigate to the AI settings page.
	 */
	private openSettings(): void {
		const base = `${window.location.origin}${App.dashboardURL}/`;

		App.root.setView(
			new URL(`${Route.system}?section=${Section.ai}`, base)
		);
	}

	/**
	 * Render an error modal dialog.
	 *
	 * @param title
	 * @param content
	 */
	private renderErrorModal(title: string, content: string): void {
		const { modal, body } = createGenericModal(
			title,
			App.text('aiAssistanceOpenSettings'),
			true,
			this.openSettings.bind(this)
		);

		body.innerHTML = content;

		setTimeout(() => {
			modal.open();
		}, 0);
	}

	/**
	 * Render the prompt input dialog.
	 *
	 * @async
	 */
	private async renderModal(): Promise<void> {
		const providers = (await getAiProviders())
			.filter((p: AiProvider) => p.isConfigured)
			.map((p: AiProvider) => {
				return { value: p.id, text: p.name };
			});

		if (!providers.length) {
			this.renderErrorModal(
				App.text('aiAssistanceNoProviderErrorTitle'),
				App.text('aiAssistanceNoProviderErrorBody')
			);

			return;
		}

		const select = createSelect(
			providers,
			App.system.ai.activeProviderId || providers[0].value
		);

		const prompt = createField(FieldTag.textarea, null, {
			key: 'am-ai-prompt',
			label: 'Prompt',
			name: 'prompt',
			value: '',
		});

		const { modal, body } = createGenericModal(
			'Prompt',
			'Ok',
			true,
			async () => {
				const success = await this.generate(
					prompt.query(),
					select.select.value
				);

				modal.close();

				if (!success) {
					this.renderErrorModal(
						App.text('aiAssistanceRequestErrorTitle'),
						App.text('aiAssistanceRequestErrorBody')
					);
				}
			}
		);

		body.appendChild(select);
		body.appendChild(prompt);

		setTimeout(() => {
			modal.open();
		}, 0);
	}

	/**
	 * Generate text either append it to the editor or rewrite the current selection.
	 *
	 * @param prompt
	 * @param providerId
	 * @return true on success
	 * @async
	 */
	private async generate(
		prompt: string,
		providerId: string
	): Promise<boolean> {
		const { data, error } = await requestAPI(AiAssistanceController.text, {
			providerId,
			prompt,
			context: await this.getContext(),
		});

		if (error) {
			return false;
		}

		const output = data?.output || null;

		if (!output) {
			return true;
		}

		fire(EventName.appStateRequireUpdate);

		if (this.selectedBlocks.length) {
			this.replaceBlocks(output);

			return true;
		}

		if (
			this.selectedRange !== null &&
			this.selectedRange.startOffset != this.selectedRange.endOffset
		) {
			this.replaceRange(output);

			return true;
		}

		this.insertBlocks(output);

		return true;
	}

	/**
	 * Get the context for the current request.
	 *
	 * @return the current context object
	 * @async
	 */
	private async getContext(): Promise<AiContext> {
		const context: AiContext = {
			text: this.selectedRange?.toString() || '',
			blocks: [],
		};

		if (this.selectedBlocks.length > 0) {
			const saved = await this.editor.save();

			const selectedBlockIds: string[] = this.selectedBlocks.reduce(
				(ids: string[], block: { id: string }) => {
					ids.push(block.id);

					return ids;
				},
				[]
			);

			context.blocks = saved.blocks.filter((b: BlockToolData) =>
				selectedBlockIds.includes(b.id)
			);
		}

		if (!context.text && !context.blocks?.length) {
			context.blocks = await this.getAllPageBlocks();
		}

		return context;
	}

	/**
	 * Get all page blocks.
	 *
	 * @return all blocks of the currentr page
	 * @async
	 */
	private async getAllPageBlocks(): Promise<BlockToolData[]> {
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
	}

	/**
	 * Insert new blocks at the end.
	 *
	 * @param content
	 * @async
	 */
	private async insertBlocks(content: string): Promise<void> {
		this.editor.caret.setToLastBlock();
		this.editor.blocks.insert();
		this.editor.caret.setToLastBlock();

		await this.editor.paste.processText(content, true);

		fire('input', this.component);
	}

	/**
	 * Replace selected block with generated content.
	 *
	 * @param content
	 * @async
	 */
	private async replaceBlocks(content: string): Promise<void> {
		if (!this.selectedBlocks?.length) {
			return;
		}

		const api = this.editor.blocks;
		const blocks = this.selectedBlocks;
		const firstBlockIndex = api.getBlockIndex(blocks[0].id);

		blocks.forEach((block: BlockToolData) => {
			api.delete(api.getBlockIndex(block.id));
		});

		api.insert(undefined, undefined, undefined, firstBlockIndex);

		this.editor.caret.setToBlock(firstBlockIndex);

		await this.editor.paste.processText(content, true);

		fire('input', this.component);
	}

	/**
	 * Replace selected text with generated content.
	 *
	 * @param content
	 * @async
	 */
	private replaceRange(content: string): void {
		const div = create('div');

		div.innerHTML = content;

		const text = div.textContent || '';
		const range = this.selectedRange;

		div.remove();

		range.deleteContents();

		const node = document.createTextNode(text);

		range.insertNode(node);

		range.setStartAfter(node);
		range.collapse(true);

		const sel = window.getSelection();

		sel.removeAllRanges();
		sel.addRange(range);

		this.selectedRange = sel.getRangeAt(0).cloneRange();

		fire('input', this.component);
	}
}
