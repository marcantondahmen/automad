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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	create,
	createImagePickerModal,
	CSS,
	fire,
	html,
	listen,
	resizeImageUrl,
} from '@/core';
import Sortable from 'sortablejs';
import { BaseComponent } from './Base';

/**
 * An image collection grid component.
 *
 * @extends BaseComponent
 */
export class ImageCollectionComponent extends BaseComponent {
	/**
	 * The tag name for the component.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-image-collection';

	/**
	 * The images getter.
	 *
	 * @return the image collection
	 */
	get images(): string[] {
		return this.sortable?.toArray() ?? [];
	}

	/**
	 * The images setter.
	 *
	 * @param images
	 */
	set images(images: string[]) {
		this.render(images);
	}

	/**
	 * The Sortable instance.
	 */
	private sortable: Sortable;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.imageCollection);
	}

	/**
	 * Render the component and init all listeners.
	 *
	 * @paramm images
	 */
	private render(images: string[]): void {
		this.removeListeners();
		this.innerHTML = '';

		const grid = create('div', [CSS.imageCollectionGrid], {}, this);

		images.forEach((url) => {
			const item = create(
				'div',
				[CSS.imageCollectionItem],
				{ 'data-url': url, title: url },
				grid,
				html`
					<am-img
						src="${resizeImageUrl(url)}"
						class="${CSS.imageCollectionImage}"
					></am-img>
				`
			);

			const deleteButton = create(
				'span',
				[CSS.imageCollectionDelete],
				{},
				item
			);

			this.addListener(
				listen(deleteButton, 'click', () => {
					item.remove();
					fire('change', this);
				})
			);
		});

		this.sortable = new Sortable(grid, {
			group: 'gallery',
			dataIdAttr: 'data-url',
			dragoverBubble: false,
			ghostClass: CSS.imageCollectionItemGhost,
			dragClass: CSS.imageCollectionItemDrag,
			chosenClass: CSS.imageCollectionItemChosen,
			animation: 300,
			onSort: () => {
				fire('change', this);
			},
		});

		this.addListener(
			listen(grid, 'dragover', (event: Event) => {
				event.preventDefault();
				event.stopPropagation();
			})
		);

		this.addListener(
			listen(grid, 'click', (event: Event) => {
				if (event.target === grid) {
					this.add();
				}
			})
		);

		const addButton = create(
			'button',
			[CSS.button, CSS.imageCollectionAdd],
			{},
			this,
			App.text('addImage')
		);

		this.addListener(listen(addButton, 'click', this.add.bind(this)));
	}

	/**
	 * Open the image picker in order to add an image to the collection.
	 */
	private add(): void {
		createImagePickerModal((file) => {
			this.images = [...this.images, file];

			fire('change', this);
		}, App.text('addImage'));
	}
}

customElements.define(
	ImageCollectionComponent.TAG_NAME,
	ImageCollectionComponent
);
