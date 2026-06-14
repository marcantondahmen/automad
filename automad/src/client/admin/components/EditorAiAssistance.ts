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
	fire,
	html,
} from '@/admin/core';
import { create, Route, Section } from '@/common';
import { EditorJSComponent } from '@/admin/components/EditorJS';
import { AiProvider, SelectComponentOption } from '@/admin/types';
import { AiRuntime } from '@/admin/editor/ai';
import { BaseComponent } from './Base';

/**
 * Navigate to the AI settings page.
 */
const openSettings = (): void => {
	const base = `${window.location.origin}${App.dashboardURL}/`;

	App.root.setView(new URL(`${Route.system}?section=${Section.ai}`, base));
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

export class EditorAiAssistanceComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-editor-ai-assistance';

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(
			CSS.editorAiAssistance,
			CSS.editorAiAssistanceHidden,
			CSS.displaySmallNone
		);

		const providers: SelectComponentOption[] = App.system.ai.providers
			.filter((p: AiProvider) => p.isConfigured)
			.map((p: AiProvider) => {
				return { value: p.id, text: p.name };
			});

		const details = create(
			'details',
			[],
			{ name: 'am-ai-assistance' },
			this,
			html`
				<summary>
					<span class="${CSS.editorAiAssistanceToggle}">
						<small>${App.text('systemAi')}</small>
						<i class="bi bi-robot"></i>
					</span>
				</summary>
			`
		);

		this.listen(details, 'toggle', () => {
			AiRuntime.get().toggleSelectionHighlighting(details.open);

			if (details.open && !providers.length) {
				renderErrorModal(
					App.text('aiAssistanceNoProviderErrorTitle'),
					App.text('aiAssistanceNoProviderErrorBody')
				);

				details.open = false;
			}
		});

		this.listen(window, 'mouseup', (event: Event) => {
			const target = event.target as HTMLElement;

			this.toggleUi(
				!(
					target.closest(EditorJSComponent.TAG_NAME) ||
					this.contains(target)
				)
			);
		});

		this.render(details, providers);
	}

	/**
	 * Render the main UI.
	 *
	 * @param details
	 * @param providers
	 */
	private render(
		details: HTMLDetailsElement,
		providers: SelectComponentOption[]
	): void {
		if (providers.length == 0) {
			return;
		}

		const selectionBindingName = 'AiRuntimeSelection';
		const selectionBinding = new Binding(selectionBindingName);

		const form = create('div', [CSS.editorAiAssistanceForm], {}, details);

		const prompt = create<HTMLTextAreaElement>(
			'textarea',
			[CSS.editorAiAssistancePrompt],
			{},
			form
		);

		create(
			'div',
			[CSS.editorAiAssistanceSelection, CSS.textLimitRows],
			{
				[Attr.bind]: selectionBindingName,
			},
			form
		);

		const footer = create(
			'div',
			[CSS.editorAiAssistanceFormFooter],
			{},
			form
		);

		const select = createSelect(
			providers,
			App.system.ai.activeProviderId || `${providers[0].value}`,
			footer,
			null,
			null,
			null,
			[CSS.editorAiAssistanceSelect]
		);

		const buttons = create('div', [CSS.flex], {}, footer);

		const settings = create(
			'span',
			[CSS.editorAiAssistanceButton, CSS.textMuted],
			{
				[Attr.tooltip]: App.text('aiAssistanceOpenSettings'),
				[Attr.tooltipOptions]: 'placement:top',
			},
			buttons,
			'<i class="bi bi-sliders"></i>'
		);

		const submit = create(
			'span',
			[CSS.editorAiAssistanceButton],
			{
				[Attr.tooltip]: `${App.text('submit')}<br>⇧ + Enter`,
				[Attr.tooltipOptions]: 'placement:top',
			},
			buttons,
			'<i class="bi bi-arrow-up-circle-fill"></i>'
		);

		const stop = create(
			'span',
			[CSS.editorAiAssistanceButton, CSS.displayNone],
			{},
			buttons,
			'<i class="bi bi-stop-circle"></i>'
		);

		const close = create(
			'span',
			[CSS.editorAiAssistanceButton],
			{},
			buttons,
			'<i class="bi bi-x-lg"></i>'
		);

		const generate = async () => {
			const success = await AiRuntime.get().generate(
				prompt.value,
				select.select.value
			);

			if (!success) {
				renderErrorModal(
					App.text('aiAssistanceRequestErrorTitle'),
					App.text('aiAssistanceRequestErrorBody')
				);

				return;
			}

			prompt.value = '';
			fire('input', prompt);
		};

		const abort = async () => {
			const value = prompt.value;

			AiRuntime.get().abortRequest();

			setTimeout(() => {
				prompt.value = value;
			}, 0);
		};

		this.listen(window, 'keydown', (event: KeyboardEvent) => {
			if (details.open) {
				if (event.key === 'Escape') {
					details.open = false;
					abort();
				}
			}
		});

		this.listen(window, EventName.aiRuntimeStateChange, () => {
			submit.classList.toggle(CSS.displayNone, AiRuntime.get().pending);
			stop.classList.toggle(CSS.displayNone, !AiRuntime.get().pending);
			prompt.disabled = AiRuntime.get().pending;

			selectionBinding.value = AiRuntime.get()
				.getSelectionDisplay()
				.trim();

			this.classList.toggle(
				CSS.editorAiAssistancePending,
				AiRuntime.get().pending
			);
		});

		this.listen(details, 'toggle', () => {
			setTimeout(() => {
				if (details.open) {
					prompt.focus();
				} else {
					prompt.blur();
				}
			}, 0);
		});

		this.listen(settings, 'click', openSettings);

		this.listen(submit, 'click', generate);

		this.listen(prompt, 'keydown', (event: KeyboardEvent) => {
			if (event.key === 'Enter' && event.shiftKey) {
				event.preventDefault();

				if (!!prompt.value) {
					generate();
				}
			}
		});

		this.listen(prompt, 'input', () => {
			submit.classList.toggle(
				CSS.editorAiAssistanceButtonDisabled,
				!prompt.value
			);
		});

		fire('input', prompt);

		this.listen(prompt, 'focus', () => {
			AiRuntime.get().toggleSelectionHighlighting(true);
		});

		this.listen(prompt, 'blur', () => {
			AiRuntime.get().toggleSelectionHighlighting(false);
		});

		this.listen(stop, 'click', abort.bind(this));

		this.listen(close, 'click', async () => {
			if (!AiRuntime.get().pending) {
				details.open = false;
			}
		});

		Bindings.connectElements(this);
	}

	/**
	 * Toggle the main ui.
	 *
	 * @param state
	 */
	private toggleUi(state: boolean): void {
		AiRuntime.get().toggleSelectionHighlighting(!state);

		this.classList.toggle(CSS.editorAiAssistanceHidden, state);
	}

	/**
	 * Remove all window event listeners when disconnecting.
	 */
	disconnectedCallback(): void {
		AiRuntime.get().destroy();

		super.disconnectedCallback();
	}
}

customElements.define(
	EditorAiAssistanceComponent.TAG_NAME,
	EditorAiAssistanceComponent
);
