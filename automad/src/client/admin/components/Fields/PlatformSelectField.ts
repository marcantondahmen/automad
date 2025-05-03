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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { CSS, FieldTag, html } from '@/admin/core';
import { InputElement, RepositoryPlatform } from '@/admin/types';
import { create, query } from '@/common';
import { BaseFieldComponent } from './BaseField';

/**
 * The git platform selector component.
 *
 * @extends BaseFieldComponent
 */
export class PlatformSelectorComponent extends BaseFieldComponent {
	/**
	 * Get the actual field input element.
	 */
	get input(): InputElement {
		return query('[name]:checked', this);
	}

	/**
	 * Create a Git platform select field.
	 */
	createInput(): void {
		const wrapper = create('div', [CSS.platformSelect], {}, this);

		this.addOption(wrapper, 'github', 'GitHub');
		this.addOption(wrapper, 'gitlab', 'GitLab');
	}

	private addOption(
		parent: HTMLElement,
		platform: RepositoryPlatform,
		title: string
	): void {
		const { name, id, value } = this._data;
		const container = create(
			'span',
			[CSS.platformSelectOption],
			{},
			parent
		);

		create(
			'input',
			[],
			{
				id: `${id}-${platform}`,
				name,
				value: platform,
				type: 'radio',
				...(value == platform ? { checked: '' } : {}),
			},
			container
		);

		const label = create(
			'label',
			[],
			{ for: `${id}-${platform}` },
			container
		);

		create(
			'span',
			[CSS.platformSelectIcon],
			{},
			label,
			html`<i class="bi bi-${platform}"></i>`
		);

		create('span', [], {}, label, title);
		create(
			'i',
			[CSS.platformSelectActiveIcon, 'bi', 'bi-check-circle'],
			{},
			label
		);
	}
}

customElements.define(FieldTag.platformSelect, PlatformSelectorComponent);
