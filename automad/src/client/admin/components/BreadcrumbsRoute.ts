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
 * Copyright (c) 2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, html, Route } from '../core';
import { BaseComponent } from './Base';

/**
 * A breadcrumbs nav for a main page of the top level route.
 *
 * @example
 * <am-route-breadcrumbs
 *     ${Attr.target}="..."
 *     ${Attr.text}="..."
 *     ${Attr.narrow}
 * ></am-route-breadcrumbs>
 *
 * @extends BaseComponent
 */
class BreadcrumbsRouteComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return [Attr.target, Attr.text, Attr.icon];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.layoutDashboardSection);
		const isNarrow = this.hasAttribute(Attr.narrow);

		this.innerHTML = html`
			<div
				class="${CSS.layoutDashboardContent} ${isNarrow
					? CSS.layoutDashboardContentNarrow
					: ''}"
			>
				<div class="${CSS.breadcrumbs}">
					<am-link
						class="${CSS.breadcrumbsItem}"
						${Attr.target}="${Route.home}"
					>
						${App.text('dashboardTitle')}
					</am-link>
					<am-link
						class="${CSS.breadcrumbsItem}"
						${Attr.target}="${this.elementAttributes[Attr.target]}"
					>
						${this.elementAttributes[Attr.text]}
					</am-link>
				</div>
			</div>
		`;
	}
}

customElements.define('am-breadcrumbs-route', BreadcrumbsRouteComponent);
