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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import {
	App,
	Attr,
	createGenericModal,
	createSelect,
	CSS,
	EventName,
	html,
	listen,
} from '@/admin/core';
import { ComponentBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

const getComponent = (id: string) => {
	return App.components.find((c) => c.id === id);
};

export class ComponentBlock extends BaseBlock<ComponentBlockData> {
	/**
	 * Sanitizer rules
	 */
	static get sanitize() {
		return {
			id: false,
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('componentBlockTitle'),
			icon: '<i class="bi bi-boxes"></i>',
		};
	}

	/**
	 * Prepare the data that is passed to the constructor.
	 *
	 * @param data
	 * @return the prepared data
	 */
	protected prepareData(data: ComponentBlockData): ComponentBlockData {
		return { id: data.id || '' };
	}

	/**
	 * Render the block.
	 *
	 * @return the rendered element
	 */
	render(): HTMLElement {
		if (!this.data.id) {
			return this.wrapper;
		}

		this.renderWrapper();

		return this.wrapper;
	}

	/**
	 * Render the actual wrapper content.
	 */
	private renderWrapper(): void {
		const component = getComponent(this.data.id ?? '');

		this.wrapper.innerHTML = html`
			<div
				class="${CSS.card} ${CSS.userSelectNone}"
				title="${component?.name}"
			>
				<div class="${CSS.cardTitle} ${CSS.flex} ${CSS.flexGap}">
					<am-icon-text
						${Attr.icon}="boxes"
						${Attr.text}="${component?.name}"
					></am-icon-text>
				</div>
			</div>
		`;
	}

	/**
	 * Called when block is added.
	 */
	appendCallback(): void {
		const { modal, body } = createGenericModal(
			App.text('componentBlockTitle'),
			App.text('ok')
		);

		const options = App.components.map((component) => {
			return {
				value: component.id,
				text: component.name,
			};
		});

		const select = createSelect(options, options[0].value, body);

		modal.open();

		listen(modal, EventName.modalClose, () => {
			this.data.id = select.value;
			this.renderWrapper();
		});
	}

	/**
	 * Save the block data.
	 *
	 * @return the saved data
	 */
	save(): ComponentBlockData {
		return this.data;
	}
}
