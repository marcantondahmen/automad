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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { isActivePage, App, html, CSS, Attr } from '@/core';
import { BaseComponent } from '@/components/Base';

/**
 * A simple link in the sidebar navigation.
 *
 * @example
 * <am-nav-item ${Attr.page}="system" ${Attr.icon}="sliders" ${Attr.text}="System"></am-nav-item>
 *
 * @extends BaseComponent
 */
class NavItemComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return [Attr.page, Attr.icon, Attr.text, Attr.badge];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.navItem);
		this.classList.toggle(
			CSS.navItemActive,
			isActivePage(this.elementAttributes[Attr.page])
		);

		this.innerHTML = this.render();

		NavItemComponent.observedAttributes.forEach((item) => {
			this.removeAttribute(item);
		});
	}

	/**
	 * Render the component.
	 *
	 * @returns the rendered HTML
	 */
	render(): string {
		const badge = this.elementAttributes[Attr.badge];
		const icon = this.elementAttributes[Attr.icon];
		const page = this.elementAttributes[Attr.page];
		const text = this.elementAttributes[Attr.text];

		return html`
			<am-link ${Attr.target}="${page}" class="${CSS.navLink}">
				<span class="${CSS.iconText}">
					<i class="bi bi-${icon}"></i>
					<span>${App.text(text)}</span>
					${badge ? `<${badge}></${badge}>` : ''}
				</span>
			</am-link>
		`;
	}
}

customElements.define('am-nav-item', NavItemComponent);
