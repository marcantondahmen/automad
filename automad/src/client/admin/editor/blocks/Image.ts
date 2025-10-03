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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { ImgComponent } from '@/admin/components/Img';
import {
	App,
	Attr,
	collectFieldData,
	create,
	createField,
	createGenericModal,
	createImagePickerModal,
	CSS,
	debounce,
	EventName,
	FieldTag,
	fire,
	getPageURL,
	html,
	ImageController,
	notifyError,
	requestAPI,
	resolveFileUrl,
	uniqueId,
} from '@/admin/core';
import { ImageBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export class ImageBlock extends BaseBlock<ImageBlockData> {
	/**
	 * Sanitizer settings.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			url: true,
			link: false,
			alt: true,
			openInNewTab: false,
			caption: {},
		};
	}

	/**
	 * Paste configuration
	 *
	 * @static
	 */
	static get pasteConfig() {
		return {
			patterns: {
				image: new RegExp(
					`(https?:\\/\\/)?\\S+\\.(${App.fileTypesImage.join('|')})$`,
					'i'
				),
			},
			files: {
				extensions: App.fileTypesImage,
			},
		};
	}

	/**
	 * Toolbox settings.
	 *
	 * @static
	 */
	static get toolbox() {
		return {
			title: App.text('imageTool'),
			icon: '<i class="bi bi-image"></i>',
		};
	}

	/**
	 * The image element.
	 */
	private img: ImgComponent;

	/**
	 * The caption element.
	 */
	private caption: HTMLDivElement;

	/**
	 * Prepare block data.
	 *
	 * @param data
	 * @param data.url
	 * @param data.alt
	 * @param data.link
	 * @param data.openInNewTab
	 * @param data.caption
	 * @return the image block data
	 */
	protected prepareData(data: ImageBlockData): ImageBlockData {
		return {
			url: data.url || '',
			alt: data.alt || '',
			link: data.link || '',
			openInNewTab: data.openInNewTab || false,
			caption: data.caption || '',
		};
	}

	/**
	 * Set the url property and update the preview.
	 *
	 * @param url
	 */
	private setImage(url: string): void {
		this.data.url = url;
		this.img.src = resolveFileUrl(url);
	}

	/**
	 * Render the main block element.
	 *
	 * @return the rendered block
	 */
	render(): HTMLElement {
		this.wrapper.classList.add(CSS.editorBlockImage);
		this.img = create(
			ImgComponent.TAG_NAME,
			[],
			{},
			this.wrapper
		) as ImgComponent;

		this.setImage(this.data.url);

		if (!this.readOnly) {
			const buttons = create(
				'div',
				[CSS.editorBlockImageButtons, CSS.formGroup],
				{},
				this.wrapper
			);

			const select = create(
				'button',
				[CSS.button, CSS.buttonIcon, CSS.formGroupItem],
				{ [Attr.tooltip]: App.text('selectImage') },
				buttons,
				'<i class="bi bi-images"></i>'
			);

			const alt = create(
				'button',
				[CSS.button, CSS.buttonIcon, CSS.formGroupItem],
				{ [Attr.tooltip]: App.text('altAttr') },
				buttons,
				'<i class="bi bi-tag-fill"></i>'
			);

			const link = create(
				'button',
				[CSS.button, CSS.buttonIcon, CSS.formGroupItem],
				{ [Attr.tooltip]: App.text('link') },
				buttons,
				'<i class="bi bi-link"></i>'
			);

			this.listen(select, 'click', this.pickImage.bind(this));
			this.listen(alt, 'click', this.createAltModal.bind(this));
			this.listen(link, 'click', this.createLinkModal.bind(this));
		}

		this.caption = create(
			'div',
			['cdx-block', 'ce-paragraph'],
			{
				contenteditable: this.readOnly ? 'false' : 'true',
				placeholder: App.text('caption'),
			},
			this.wrapper,
			html`${this.data.caption}`
		);

		this.listen(this.caption, 'input', () => {
			fire('change', this.caption);
		});

		return this.wrapper;
	}

	/**
	 * Called when block is added.
	 */
	appendCallback(): void {
		this.pickImage();
	}

	/**
	 * Add image when pasting a valid image url.
	 */
	onPaste(event: CustomEvent) {
		if (event.type == 'pattern') {
			this.setImage(event.detail.data);
		}

		if (event.type == 'file') {
			const file = event.detail.file;
			const reader = new FileReader();
			const extension = file.type.split('/')[1];
			const name = `image-${Date.now()}`;

			reader.onload = async (loadEvent) => {
				if (!extension) {
					return;
				}

				this.wrapper.innerHTML = '<am-spinner></am-spinner>';

				const { error } = await requestAPI(
					ImageController.save,
					{
						url: getPageURL(),
						name,
						extension,
						imageBase64: loadEvent.target.result,
					},
					false,
					() => {
						this.wrapper.innerHTML = '';

						this.data.url = getPageURL()
							? `${name}.${extension}`
							: `/shared/${name}.${extension}`;

						this.render();

						fire(EventName.filesChangeOnServer);
					}
				);

				if (error) {
					notifyError(error);
				}
			};

			reader.readAsDataURL(file);
		}
	}

	/**
	 * Pick an image.
	 */
	private pickImage(): void {
		createImagePickerModal(
			this.setImage.bind(this),
			App.text('selectImage'),
			this.data.url
		);
	}

	/**
	 * Create the alt text modal.
	 */
	private createAltModal(): void {
		const { modal, body } = createGenericModal(App.text('altAttr'));

		createField(FieldTag.input, body, {
			value: this.data.alt,
			name: 'alt',
			key: uniqueId(),
			label: 'alt',
		});

		this.listen(
			body,
			'input',
			debounce(() => {
				const data = collectFieldData(modal);

				this.data.alt = data.alt;
				this.blockAPI.dispatchChange();
			}, 200)
		);

		setTimeout(() => {
			modal.open();
		});
	}

	/**
	 * Create the link modal.
	 */
	private createLinkModal(): void {
		const { modal, body } = createGenericModal(App.text('link'));

		createField(FieldTag.url, body, {
			value: this.data.link,
			name: 'link',
			key: uniqueId(),
			label: App.text('link'),
		});

		createField(FieldTag.toggle, body, {
			value: this.data.openInNewTab,
			name: 'newTab',
			key: uniqueId(),
			label: App.text('openInNewTab'),
		});

		this.listen(
			body,
			'input',
			debounce(() => {
				const data = collectFieldData(modal);

				this.data.link = data.link;
				this.data.openInNewTab = data.newTab ? true : false;

				this.blockAPI.dispatchChange();
			}, 200)
		);

		setTimeout(() => {
			modal.open();
		});
	}

	/**
	 * Return the section block data.
	 *
	 * @return the saved data
	 */
	save(): ImageBlockData {
		this.data.caption = this.caption.innerHTML || '';

		return this.data;
	}
}
