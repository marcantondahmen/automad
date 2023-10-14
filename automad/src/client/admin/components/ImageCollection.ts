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
 * The `am-action` attribute can be used to ad an action button on top of the image grid.
 * This button can be used to implement any functionality. It can be publicly accessed using the `actionButton` property.
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
	 * The actual collection container that is re-rendered when images are added/removed.
	 */
	private container: HTMLElement;

	/**
	 * The action button element that can optionally be used added by defining the
	 * `am-action` attribute. The button has no functionality out of the box
	 * and therefore any kind of listener has to be attached in a later step.
	 */
	actionButton: HTMLElement = null;

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.imageCollection);

		const actionButtonLabel = this.getAttribute(Attr.action);

		if (actionButtonLabel) {
			this.actionButton = create(
				'button',
				[CSS.button, CSS.imageCollectionAction],
				{},
				this,
				actionButtonLabel
			);
		}

		this.container = create('div', [], {}, this);

		const addButton = create(
			'button',
			[CSS.button, CSS.imageCollectionAdd],
			{},
			this,
			App.text('addImage')
		);

		listen(addButton, 'click', this.add.bind(this));
	}

	/**
	 * Render the component and init all listeners.
	 *
	 * @paramm images
	 */
	private render(images: string[]): void {
		this.removeListeners();
		this.sortable?.destroy();
		this.container.innerHTML = '';

		const grid = create(
			'div',
			[CSS.imageCollectionGrid],
			{},
			this.container
		);

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
