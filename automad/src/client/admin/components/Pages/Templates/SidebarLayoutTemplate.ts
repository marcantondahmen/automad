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

import { App, classes, createField, html, titleCase } from '../../../core';
import { Partials } from '../../../types';
import { createTemplateSelect } from '../../Fields/PageTemplate';

export const sidebarLayout = ({ main }: Partials) => {
	return html`
		<div class="am-l-page am-l-page--sidebar">
			<am-sidebar-toggle
				class="am-l-sidebar__overlay"
			></am-sidebar-toggle>
			<nav class="am-l-sidebar">
				<am-sidebar class="am-l-sidebar__content">
					<div class="am-l-sidebar__logo">Logo</div>
					<div class="am-l-sidebar__nav">
						<div class="am-l-sidebar__jump">
							<am-jumpbar
								placeholder="jumpbarPlaceholder"
							></am-jumpbar>
						</div>
						<nav class="${classes.nav}">
							<span class="${classes.navLabel}">
								$${App.text('sidebarGlobal')}
							</span>
							<span class="${classes.navItem}">
								<a
									href="${App.baseURL}"
									class="${classes.navLink}"
								>
									<am-icon-text
										icon="window-desktop"
										text="$${App.sitename}"
									></am-icon-text>
								</a>
							</span>
							<am-nav-item
								page="search"
								icon="search"
								text="searchTitle"
							></am-nav-item>
							<am-nav-item
								page="home"
								icon="window-sidebar"
								text="dashboardTitle"
							></am-nav-item>
							<am-nav-item
								page="system"
								icon="sliders"
								text="systemTitle"
							></am-nav-item>
							<am-nav-item
								page="shared"
								icon="file-earmark-medical"
								text="sharedTitle"
							></am-nav-item>
							<am-nav-item
								page="packages"
								icon="box-seam"
								text="packagesTitle"
								badge="am-outdated-packages-badge"
							></am-nav-item>
						</nav>
						<am-nav-tree></am-nav-tree>
					</div>
				</am-sidebar>
			</nav>
			<nav class="am-l-navbar am-l-navbar--sidebar">
				<div class="am-l-navbar__logo">Logo</div>
				<div class="am-l-navbar__jump">
					<am-jumpbar placeholder="jumpbarPlaceholder"></am-jumpbar>
				</div>
				<div class="am-l-navbar__buttons">
					<am-system-update-button></am-system-update-button>
					<am-debug-button></am-debug-button>
					<am-modal-toggle
						class="${classes.button}"
						modal="#am-add-page-modal"
					>
						<am-icon-text
							icon="file-earmark-text"
							text="${App.text('addPage')}"
						></am-icon-text>
					</am-modal-toggle>
					<am-dark-mode-toggle
						class="am-e-button"
						text="$${App.text('toggleDarkMode')}"
					></am-dark-mode-toggle>
					<am-sidebar-toggle class="am-u-display-small">
						<i class="bi bi-list"></i>
					</am-sidebar-toggle>
				</div>
			</nav>
			<main class="am-l-main am-l-main--sidebar">${main}</main>
			<footer class="am-l-footer">
				<div class="am-l-footer__content">Footer</div>
			</footer>
		</div>
		<am-modal id="am-add-page-modal">
			<div class="${classes.modalDialog}">
				<am-form api="Page/add">
					<div class="${classes.modalHeader}">
						<span>$${App.text('addPage')}</span>
						<am-modal-close
							class="${classes.modalClose}"
						></am-modal-close>
					</div>
					${createField(
						'am-title',
						null,
						{
							key: `new-${App.reservedFields.AM_KEY_TITLE}`,
							name: App.reservedFields.AM_KEY_TITLE,
							label: titleCase(App.reservedFields.AM_KEY_TITLE),
							value: '',
						},
						[],
						{ required: '' }
					).outerHTML}
					${createField('am-toggle-large', null, {
						key: App.reservedFields.AM_KEY_PRIVATE,
						name: App.reservedFields.AM_KEY_PRIVATE,
						value: false,
					}).outerHTML}
					<div class="${classes.field}">
						<label class="${classes.fieldLabel}"
							>${App.text('pageTemplate')}
						</label>
						${createTemplateSelect('').outerHTML}
					</div>
					<div class="${classes.field}">
						<label class="${classes.fieldLabel}"
							>${App.text('selectTargetNewPage')}
						</label>
						<am-page-select-tree></am-page-select-tree>
					</div>
					<div class="${classes.modalFooter}">
						<am-submit
							class="${classes.button} ${classes.buttonPrimary}"
						>
							$${App.text('addPage')}
						</am-submit>
					</div>
				</am-form>
			</div>
		</am-modal>
	`;
};
