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
	query,
	requestAPI,
	resolveFileUrl,
	uniqueId,
} from '@/admin/core';
import { ImageBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';
import { ResponsiveImageSettingsComponent } from '@/admin/components/ResponsiveImageSettings';
import { TunesMenuConfig } from 'automad-editorjs/types/tools';
import { DropdownComponent } from '@/admin/components/Dropdown';
import { filterEmptyData } from '../utils';

/**
 * The image block.
 *
 * @extends BaseBlock
 */
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
			breakpoints: false,
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
	 * @param data.breakpoints
	 * @param data.focalPoint
	 * @return the image block data
	 */
	protected prepareData(data: ImageBlockData): ImageBlockData {
		return {
			url: data.url || '',
			alt: data.alt || '',
			link: data.link || '',
			openInNewTab: data.openInNewTab || false,
			caption: data.caption || '',
			breakpoints: data.breakpoints || {},
			focalPoint: data.focalPoint || null,
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
		this.renderResponsiveStyles();
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

			const dropdown = create(
				DropdownComponent.TAG_NAME,
				[CSS.button, CSS.buttonIcon, CSS.formGroupItem],
				{},
				buttons,
				'<i class="bi bi-sliders"></i>'
			);

			const dropdownItems = create(
				'div',
				[CSS.dropdownItems],
				{},
				dropdown
			);

			const responsive = create(
				'button',
				[CSS.dropdownLink],
				{},
				dropdownItems,
				html`
					<am-icon-text
						${Attr.icon}="crosshair"
						${Attr.text}="${App.text('responsiveImageSettings')}"
					></am-icon-text>
				`
			);

			const alt = create(
				'button',
				[CSS.dropdownLink],
				{},
				dropdownItems,
				html`
					<am-icon-text
						${Attr.icon}="tag"
						${Attr.text}="${App.text('altAttr')}"
					></am-icon-text>
				`
			);

			const link = create(
				'button',
				[CSS.dropdownLink],
				{},
				dropdownItems,
				html`
					<am-icon-text
						${Attr.icon}="link"
						${Attr.text}="${App.text('link')}"
					></am-icon-text>
				`
			);

			this.listen(select, 'click', this.pickImage.bind(this));

			this.listen(
				responsive,
				'click',
				this.createResponsiveModal.bind(this)
			);

			this.listen(alt, 'click', this.createAltModal.bind(this));
			this.listen(link, 'click', this.createLinkModal.bind(this));
		}

		this.caption = create(
			'div',
			['ce-paragraph'],
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
			([url]) => {
				this.data.breakpoints = {};
				this.data.focalPoint = null;
				this.setImage(url);
			},
			App.text('selectImage'),
			this.data.url
		);
	}

	/**
	 * Render the local style tag with the responsive settings.
	 */
	private renderResponsiveStyles(): void {
		const styleWrapper =
			query('style', this.wrapper) ||
			create('style', [], {}, this.wrapper);

		const cls = `image-${this.blockAPI.id}`;

		this.wrapper.classList.add(cls);

		let styles = `
			.${cls} {
				container-type: inline-size;
				container-name: ${cls};
			}
		`;

		const { x, y } = this.data.focalPoint || { x: 50, y: 50 };

		const maxWidths = Object.keys(this.data.breakpoints).sort((a, b) =>
			b.localeCompare(a, undefined, { numeric: true })
		);

		maxWidths.forEach((maxWidth) => {
			const { aspectRatio } = this.data.breakpoints[maxWidth];

			if (aspectRatio) {
				styles += `
					@container ${cls} (max-width: ${maxWidth}px) {
						.${cls} img {
							aspect-ratio: ${aspectRatio};
							object-fit: cover;
							object-position: ${x}% ${y}%;
						}
					}
				`;
			}
		});

		styleWrapper.textContent = styles;
	}

	/**
	 * Create the responsive settings modal.
	 */
	private createResponsiveModal(): void {
		const { modal, body } = createGenericModal(
			App.text('responsiveImageSettings')
		);

		query(`.${CSS.modalDialog}`, modal).classList.add(CSS.modalDialogLarge);

		const responsiveSettings = create<ResponsiveImageSettingsComponent>(
			ResponsiveImageSettingsComponent.TAG_NAME,
			[],
			{},
			body
		);

		responsiveSettings.init(
			this.data.url,
			this.data.breakpoints,
			this.data.focalPoint
		);

		modal.listen(responsiveSettings, 'change', () => {
			this.data.breakpoints = responsiveSettings.breakpoints;
			this.data.focalPoint = responsiveSettings.focalPoint;

			this.renderResponsiveStyles();
			this.blockAPI.dispatchChange();
		});

		setTimeout(() => {
			modal.open();
		});
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
			hideLabel: true,
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
	 * Create the tunes menu configuration.
	 *
	 * @return the tunes menu configuration
	 */
	renderSettings(): TunesMenuConfig {
		return [
			{
				icon: '<i class="bi bi-crosshair"></i>',
				label: App.text('responsiveImageSettings'),
				closeOnActivate: true,
				onActivate: () => {
					this.createResponsiveModal();
				},
			},
			{
				icon: '<i class="bi bi-tag"></i>',
				label: App.text('altAttr'),
				closeOnActivate: true,
				onActivate: () => {
					this.createAltModal();
				},
			},
			{
				icon: '<i class="bi bi-link"></i>',
				label: App.text('link'),
				closeOnActivate: true,
				onActivate: () => {
					this.createLinkModal();
				},
			},
		];
	}

	/**
	 * Return the section block data.
	 *
	 * @return the saved data
	 */
	save(): Partial<ImageBlockData> {
		this.data.caption = this.caption.innerHTML || '';

		return filterEmptyData(this.data);
	}
}
