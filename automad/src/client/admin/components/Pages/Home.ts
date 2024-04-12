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

import {
	App,
	Attr,
	CSS,
	dateFormat,
	getTagFromRoute,
	html,
	Route,
} from '@/admin/core';
import { Section } from '@/common';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

const systemInfo = (): string => {
	return html`
		<div class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}">
			<div class="${CSS.flex} ${CSS.flexColumn}">
				<am-icon-text
					${Attr.icon}="hdd-network"
					${Attr.text}="${location.hostname}"
					class="${CSS.textMuted}"
				></am-icon-text>
				<am-icon-text
					${Attr.icon}="clock-history"
					${Attr.text}="${App.text('latestActivity')} ${dateFormat(
						App.siteMTime
					)}"
					class="${CSS.textMuted}"
				></am-icon-text>
				<am-link
					${Attr.target}="${Route.system}?section=${Section.cache}"
				>
					<am-system-cache-indicator></am-system-cache-indicator>
				</am-link>
			</div>
			<div class="${CSS.displaySmall}">
				<a href="${App.baseURL || '/'}" class="${CSS.button}">
					${App.text('inPageEdit')}
				</a>
			</div>
			<div class="${CSS.formGroup} ${CSS.displaySmallNone}">
				<a
					href="${App.baseURL || '/'}"
					class="${CSS.button} ${CSS.formGroupItem}"
				>
					${App.text('inPageEdit')}
				</a>
				<am-modal-toggle
					${Attr.modal}="#am-server-info-modal"
					class="${CSS.button} ${CSS.formGroupItem}"
				>
					${App.text('serverInfo')}
				</am-modal-toggle>
			</div>
		</div>
		<am-modal id="am-server-info-modal">
			<am-modal-dialog>
				<am-modal-header>${App.text('serverInfo')}</am-modal-header>
				<am-modal-body>
					<am-server-info></am-server-info>
				</am-modal-body>
			</am-modal-dialog>
		</am-modal>
	`;
};

/**
 * The home view.
 *
 * @extends BaseDashboardLayoutComponent
 */
export class HomeComponent extends BaseDashboardLayoutComponent {
	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('dashboardTitle');
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<section class="${CSS.layoutDashboardSection}">
				<div class="${CSS.layoutDashboardContent}">
					<div
						class="${CSS.flex} ${CSS.flexColumn} ${CSS.flexGapLarge}"
					>
						<div
							class="${CSS.flex} ${CSS.flexGap} ${CSS.flexAlignBaseline}"
						>
							<h1 class="${CSS.baseHomeH1}">${App.sitename}</h1>
							<am-link
								class="${CSS.textMuted}"
								${Attr.target}="${Route.shared}"
							>
								<i class="bi bi-pencil"></i>
							</am-link>
						</div>
						${systemInfo()}
						<div>
							<h2>${App.text('recentlyEditedPages')}</h2>
							<div>
								<am-recently-edited-pages></am-recently-edited-pages>
							</div>
						</div>
					</div>
				</div>
			</section>
		`;
	}
}

customElements.define(getTagFromRoute(Route.home), HomeComponent);
