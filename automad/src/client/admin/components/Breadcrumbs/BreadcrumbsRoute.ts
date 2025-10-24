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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App, Attr, CSS, html, Route } from '@/admin/core';
import { BaseBreadcrumbsComponent } from './BaseBreadcrumbs';

/**
 * A breadcrumbs nav for a main page of the top level route.
 *
 * @example
 * <am-breadcrumbs-route
 *     ${Attr.target}="..."
 *     ${Attr.text}="..."
 *     ${Attr.narrow}
 * ></am-breadcrumbs-route>
 *
 * @extends BaseBreadcrumbsComponent
 */
class BreadcrumbsRouteComponent extends BaseBreadcrumbsComponent {
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
		super.connectedCallback();

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
						<span>${App.text('dashboardTitle')}</span>
					</am-link>
					<am-link
						class="${CSS.breadcrumbsItem}"
						${Attr.target}="${this.elementAttributes[Attr.target]}"
					>
						<span>${this.elementAttributes[Attr.text]}</span>
					</am-link>
				</div>
			</div>
		`;
	}
}

customElements.define('am-breadcrumbs-route', BreadcrumbsRouteComponent);
