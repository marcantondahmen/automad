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
	Attr,
	Binding,
	Bindings,
	createGenericModal,
	createSelect,
	CSS,
	EventName,
	FieldTag,
	fire,
	getAiProviders,
	html,
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
import { AiTarget } from '@/admin/types/editor/plugins';
import { AiProvider } from '@/admin/types';
import { EditorFieldComponent } from '@/admin/components/Fields/EditorField';

/**
 * Navigate to the AI settings page.
 */
const openSettings = (): void => {
	const base = `${window.location.origin}${App.dashboardURL}/`;

	App.root.setView(new URL(`${Route.system}?section=${Section.ai}`, base));
};

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
 * Render an error modal dialog.
 *
 * @param title
 * @param content
 */
const renderErrorModal = (title: string, content: string): void => {
	const { modal, body } = createGenericModal(
		title,
		App.text('aiAssistanceOpenSettings'),
		true,
		openSettings
	);

	body.innerHTML = content;

	setTimeout(() => {
		modal.open();
	}, 0);
};

/**
 * The AI extension for EditorJS.
 */
export class AiAssistance extends BasePlugin {
	/**
	 * The name for the event that is triggered on any generational request state change.
	 *
	 * @static
	 */
	private static EVENT_NAME = 'AutomadAiAssitanceGenerateStateChange';

	/**
	 * The class name for AI assistance buttons.
	 * This class is only used for selection and not for styling.
	 *
	 * @static
	 */
	private static CLS = '__ai';

	/**
	 * The binding name for the context display in the prompt form.
	 *
	 * @static
	 */
	private static SELECTION_BINDING = 'aiSelectionBinding';

	/**
	 * Indicates a pending generation response.
	 */
	private _pending: boolean = false;

	/**
	 * The state setter.
	 */
	private set pending(state: boolean) {
		this._pending = state;
		this.component.classList.toggle(CSS.aiAssistancePending, state);

		fire(AiAssistance.EVENT_NAME, this.component);
	}

	/**
	 * The state setter.
	 */
	private get pending() {
		return this._pending;
	}

	/**
	 * The AbortController for stopping long running generations.
	 */
	private abortController: AbortController | null = null;

	/**
	 * The currently select blocks.
	 */
	private selectedBlocks: EditorJS['blockSelection']['selectedBlocks'] = [];

	/**
	 * The currently selected range.
	 */
	private selectedRange: Range = null;

	/**
	 * The last clicked block index.
	 */
	private lastFocusedBlockIndex: number | null = null;

	/**
	 * The actual plugin implementation.
	 */
	protected init(): void {
		if (!App.system.ai.enabled) {
			return;
		}

		// Exit early on re-renders, for example on undo or redo.
		if (query(`:scope > .${AiAssistance.CLS}`, this.component)) {
			return;
		}

		const details = create(
			'details',
			[AiAssistance.CLS, CSS.aiAssistance],
			{ name: 'am-ai-assistance' },
			this.component,
			html`
				<summary>
					<span class="${CSS.aiAssistanceToggle}">
						<i class="bi bi-robot"></i>
					</span>
				</summary>
			`
		);

		this.renderForm(details);
		this.initSelectionListener();
	}

	/**
	 * Render the prompt input dialog.
	 *
	 * @param details
	 * @async
	 */
	private async renderForm(details: HTMLDetailsElement): Promise<void> {
		const providers = (await getAiProviders())
			.filter((p: AiProvider) => p.isConfigured)
			.map((p: AiProvider) => {
				return { value: p.id, text: p.name };
			});

		if (providers.length > 0) {
			const form = create('div', [CSS.aiAssistanceForm], {}, details);

			const prompt = create<HTMLTextAreaElement>(
				'textarea',
				[CSS.aiAssistancePrompt],
				{},
				form
			);

			create(
				'div',
				[CSS.aiAssistanceSelection, CSS.textLimitRows],
				{
					[Attr.bind]: AiAssistance.SELECTION_BINDING,
				},
				form
			);

			Bindings.connectElements(this.component);

			const footer = create(
				'div',
				[CSS.aiAssistanceFormFooter],
				{},
				form
			);

			const select = createSelect(
				providers,
				App.system.ai.activeProviderId || providers[0].value,
				footer,
				null,
				null,
				null,
				[CSS.aiAssistanceSelect]
			);

			const buttons = create('div', [CSS.flex], {}, footer);

			const submit = create(
				'span',
				[CSS.aiAssistanceButton],
				{},
				buttons,
				'<i class="bi bi-arrow-up-circle-fill"></i>'
			);

			const stop = create(
				'span',
				[CSS.aiAssistanceButton, CSS.displayNone],
				{},
				buttons,
				'<i class="bi bi-stop-circle"></i>'
			);

			const close = create(
				'span',
				[CSS.aiAssistanceButton],
				{},
				buttons,
				'<i class="bi bi-x-lg"></i>'
			);

			this.component.listen(
				this.component,
				AiAssistance.EVENT_NAME,
				() => {
					submit.classList.toggle(CSS.displayNone, this.pending);
					stop.classList.toggle(CSS.displayNone, !this.pending);
					prompt.disabled = this.pending;
				}
			);

			this.component.listen(submit, 'click', async () => {
				this.abortController = new AbortController();

				const success = await this.generate(
					prompt.value,
					select.select.value,
					this.abortController
				);

				if (!success) {
					renderErrorModal(
						App.text('aiAssistanceRequestErrorTitle'),
						App.text('aiAssistanceRequestErrorBody')
					);

					return;
				}

				prompt.value = '';
			});

			const abort = async () => {
				const value = prompt.value;

				this.abortController.abort();

				setTimeout(() => {
					prompt.value = value;
				}, 0);
			};

			this.component.listen(stop, 'click', abort.bind(this));

			this.component.listen(close, 'click', async () => {
				if (!this.pending) {
					details.open = false;
				}
			});

			this.component.listen(window, 'keydown', (event: KeyboardEvent) => {
				if (details.open) {
					if (event.key === 'Escape') {
						details.open = false;
						abort();
					}
				}
			});

			this.component.listen(details, 'toggle', () => {
				setTimeout(() => {
					if (details.open) {
						prompt.focus();
					} else {
						prompt.blur();
					}
				}, 0);
			});

			this.component.listen(prompt, 'focus', () => {
				this.toggleSelectedBlockHighlighting(true);
			});
		}

		this.component.listen(details, 'toggle', () => {
			this.toggleSelectedBlockHighlighting(details.open);

			if (details.open && !providers.length) {
				renderErrorModal(
					App.text('aiAssistanceNoProviderErrorTitle'),
					App.text('aiAssistanceNoProviderErrorBody')
				);

				details.open = false;
			}
		});
	}

	/**
	 * Toggle highlighting of selected blocks.
	 *
	 * @param state
	 */
	private toggleSelectedBlockHighlighting(state: boolean): void {
		if (this.pending) {
			return;
		}

		queryAll(`.${CSS.aiAssistanceContext}`).forEach((block) => {
			block.classList.remove(CSS.aiAssistanceContext);
		});

		this.selectedBlocks.forEach((block: { holder: HTMLElement }) => {
			block.holder.classList.toggle(CSS.aiAssistanceContext, state);
		});
	}

	/**
	 * Generate text either append it to the editor or rewrite the current selection.
	 *
	 * @param prompt
	 * @param providerId
	 * @param abortController
	 * @return true on success
	 * @async
	 */
	private async generate(
		prompt: string,
		providerId: string,
		abortController: AbortController
	): Promise<boolean> {
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
			abortController
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
	 * Get the target content to be transformed for the current request.
	 *
	 * @return the current context object
	 * @async
	 */
	private async getTarget(): Promise<AiTarget> {
		const target: AiTarget = {
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

			target.blocks = saved.blocks.filter((b: BlockToolData) =>
				selectedBlockIds.includes(b.id)
			);
		}

		return target;
	}

	/**
	 * Insert new blocks at the end.
	 *
	 * @param content
	 * @async
	 */
	private async insertBlocks(content: string): Promise<void> {
		if (this.lastFocusedBlockIndex !== null) {
			this.editor.caret.setToBlock(this.lastFocusedBlockIndex);
		} else {
			this.editor.caret.setToLastBlock();
		}

		this.editor.blocks.insert();

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

	/**
	 * Init the block and text selection listener.
	 */
	private initSelectionListener(): void {
		const selectionBinding = new Binding(
			AiAssistance.SELECTION_BINDING,
			{}
		);

		this.component.listen(
			this.component,
			'mouseup',
			debounce((event: MouseEvent) => {
				if (this.pending) {
					return;
				}

				const target = event.target as HTMLElement;

				if (target.closest('[contenteditable]')) {
					const sel = window.getSelection();

					this.selectedRange = sel.rangeCount
						? sel.getRangeAt(0).cloneRange()
						: null;

					this.lastFocusedBlockIndex =
						this.editor.blocks.getCurrentBlockIndex();

					this.selectedBlocks = [];
				}

				if (!target.closest(`.${AiAssistance.CLS}`)) {
					this.selectedBlocks =
						this.editor.blockSelection.selectedBlocks;

					this.toggleSelectedBlockHighlighting(false);
				}

				if (this.selectedBlocks.length > 0) {
					this.selectedRange = null;
				}

				selectionBinding.value = this.selectedRange?.toString() || '';
			})
		);
	}
}
