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

import { create, Route, Section } from 'common';
import logo from 'common/svg/logo.svg';
import { BaseInPageComponent } from './BaseInPageComponent';

/**
 * The main InPage component.
 */
export class InPageDockComponent extends BaseInPageComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add('am-inpage-dock');

		const csrf = this.getAttr('csrf');
		const api = this.getAttr('api');
		const dashboard = this.getAttr('dashboard');
		const url = this.getAttr('url');
		const state = this.getAttr('state');
		const labels = JSON.parse(decodeURIComponent(this.getAttr('labels')));

		create(
			'div',
			['am-inpage-dock__logo'],
			{},
			create('a', ['am-inpage-dock__item'], { href: dashboard }, this),
			logo
		);

		const createPageLink = (
			icon: string,
			section: Section,
			tooltip: string
		) => {
			create(
				'i',
				['bi', `bi-${icon}`],
				{},
				create(
					'a',
					['am-inpage-dock__item'],
					{
						href: `${dashboard}/${Route.page}?url=${encodeURIComponent(url)}&section=${section}`,
						'data-tooltip': tooltip,
					},
					this
				)
			);
		};

		createPageLink('sliders', Section.settings, labels.fieldsSettings);
		createPageLink('file-richtext', Section.text, labels.fieldsContent);
		createPageLink('files', Section.files, labels.uploadedFiles);

		create(
			'am-inpage-publish',
			[],
			{ state, url, api, csrf, label: labels.publish },
			this
		);
	}
}

customElements.define('am-inpage-dock', InPageDockComponent);
