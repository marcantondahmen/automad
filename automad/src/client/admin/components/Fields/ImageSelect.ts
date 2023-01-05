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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	Binding,
	Bindings,
	create,
	CSS,
	EventName,
	fire,
	getPageURL,
	html,
	listen,
	query,
	resolveFileUrl,
} from '../../core';
import { KeyValueMap } from '../../types';
import { BaseFieldComponent } from './BaseField';

/**
 * An image selection field.
 *
 * @extends BaseFieldComponent
 */
class ImageSelectComponent extends BaseFieldComponent {
	/**
	 * The resize object.
	 */
	private resize: KeyValueMap = {};

	/**
	 * Create an input field.
	 */
	protected createInput(): void {
		const { name, id, value, placeholder } = this._data;
		const wrapper = create('span', [CSS.imageSelect], {}, this);
		const preview = create('span', [CSS.imageSelectPreview], {}, wrapper);
		const combo = create('div', [CSS.imageSelectCombo], {}, wrapper);

		const inputBindingName = `input_${id}`;
		new Binding(inputBindingName, {
			modifier: (value: string) => {
				const { width, height } = this.resize;
				const querystring =
					width && height && !value.match(/\:\/\//)
						? `?${width}x${height}`
						: '';

				return `${value}${querystring}`;
			},
			initial: value || '',
		});

		const input = create(
			'input',
			[CSS.input],
			{
				id,
				name,
				type: 'text',
				placeholder,
				[Attr.bind]: inputBindingName,
				[Attr.bindTo]: 'value',
			},
			combo
		);

		listen(input, EventName.changeByBinding, () => {
			fire('input', input);
		});

		this.createPreview(preview, input, id);
		const button = this.createModalButton(combo);

		const createModal = () => {
			this.createModal(inputBindingName);
		};

		listen(button, 'click', createModal.bind(this));
		listen(preview, 'click', createModal.bind(this));
	}

	/**
	 * Create the preview element.
	 *
	 * @param container
	 * @param input
	 * @param id
	 */
	private createPreview(
		container: HTMLElement,
		input: HTMLInputElement,
		id: string
	) {
		const previewBindingName = `preview_${id}`;

		new Binding(previewBindingName, {
			input,
			modifier: (value: string): string => {
				container.classList.remove(CSS.imageSelectPreviewError);

				return resolveFileUrl(value).split('?')[0];
			},
		});

		const img = create(
			'img',
			[],
			{ [Attr.bind]: previewBindingName, [Attr.bindTo]: 'src' },
			container
		);

		listen(img, 'error', () => {
			img.removeAttribute('src');
			container.classList.add(CSS.imageSelectPreviewError);
		});
	}

	/**
	 * Create the modal button.
	 *
	 * @param container
	 * @returns the modal button
	 */
	private createModalButton(container: HTMLElement): HTMLElement {
		const button = create('button', [CSS.button], {}, container);

		button.innerHTML = html`
			<i class="bi bi-folder"></i>
			<span>${App.text('browseFiles')}</span>
		`;

		return button;
	}

	/**
	 * Create the picker modal.
	 */
	private createModal(inputBindingName: string): void {
		const modal = create('am-modal', [], { [Attr.destroy]: '' }, this);

		modal.innerHTML = html`
			<div class="${CSS.modalDialog} ${CSS.modalDialogLarge}">
				<div class="${CSS.modalHeader}">
					<span>${this._data.label}</span>
					<am-modal-close class="${CSS.modalClose}"></am-modal-close>
				</div>
				<div class="${CSS.modalBody}">
					<span class="${CSS.formGroup}">
						<input
							type="text"
							class="${CSS.input} ${CSS.formGroupItem}"
							placeholder="${App.text('url')}"
						/>
						<button class="${CSS.button} ${CSS.formGroupItem}">
							${App.text('ok')}
						</button>
					</span>
					<hr />
					<div class="${CSS.flex} ${CSS.flexGap}">
						<div class="${CSS.flexItemGrow}">
							<div class="${CSS.field}">
								<label class="${CSS.fieldLabel}">
									${App.text('resizeWidthTitle')}
								</label>
								<input
									type="number"
									class="${CSS.input}"
									name="width"
								/>
							</div>
						</div>
						<div class="${CSS.flexItemGrow}">
							<div class="${CSS.field} ${CSS.flexItemGrow}">
								<label class="${CSS.fieldLabel}">
									${App.text('resizeHeightTitle')}
								</label>
								<input
									type="number"
									class="${CSS.input}"
									name="height"
								/>
							</div>
						</div>
					</div>
					<am-image-picker
						${Attr.page}="${getPageURL()}"
						${Attr.label}="${App.text('pageImages')}"
						${Attr.binding}="${inputBindingName}"
					></am-image-picker>
					<am-image-picker
						${Attr.label}="${App.text('sharedImages')}"
						${Attr.binding}="${inputBindingName}"
					></am-image-picker>
				</div>
			</div>
		`;

		const button = query('button', modal);
		const inputUrl = query('input', modal) as HTMLInputElement;
		const inputWidth = query('[name="width"]') as HTMLInputElement;
		const inputHeight = query('[name="height"]') as HTMLInputElement;

		listen(button, 'click', () => {
			const binding = Bindings.get(inputBindingName);
			binding.value = inputUrl.value;
			modal.close();
		});

		this.resize = { width: '', height: '' };

		listen(inputWidth, 'change', () => {
			this.resize.width = inputWidth.value;
		});

		listen(inputHeight, 'change', () => {
			this.resize.height = inputHeight.value;
		});

		setTimeout(() => {
			modal.open();
		}, 0);
	}
}

customElements.define('am-image-select', ImageSelectComponent);
