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

import { App, createField, CSS, html, Routes, titleCase } from '../../../core';
import { Partials } from '../../../types';
import { createTemplateSelect } from '../../Fields/PageTemplate';
import { Sections } from '../../Switcher/Switcher';

export const dashboardLayout = ({ main }: Partials) => {
	return html`
		<div class="am-l-dashboard">
			<div class="am-l-dashboard__navbar am-l-dashboard__navbar--left">
				<div class="${CSS.navbar}">
					<am-link target="${Routes.home}" class="${CSS.navbarItem}">
						<am-logo></am-logo>
					</am-link>
					<am-modal-toggle
						modal="#am-add-page-modal"
						class="${CSS.navbarItem}"
						am-tooltip="$${App.text('addPage')}"
					>
						<span>New</span>
						<i class="bi bi-plus-lg"></i>
					</am-modal-toggle>
				</div>
			</div>
			<div class="am-l-dashboard__navbar am-l-dashboard__navbar--right">
				<div class="${CSS.navbar}">
					<am-modal-toggle
						class="${CSS.navbarItem}"
						modal="#am-jumpbar-modal"
					>
						<span>$${App.text('jumpbarButtonText')}</span>
						<am-key-combo-badge key="J">
					</am-modal-toggle>
					<span class="${CSS.navbarGroup}">
						<am-navbar-system-update-indicator></am-navbar-system-update-indicator>
						<am-navbar-outdated-packages-indicator></am-navbar-outdated-packages-indicator>
						<am-navbar-debug-indicator></am-navbar-debug-indicator>
						<am-dropdown right>
							<span class="${CSS.navbarItem}">
								<i class="bi bi-three-dots"></i>
							</span>
							<div class="${CSS.dropdownItems}">
								<span
									class="${CSS.dropdownContent} ${CSS.dropdownDivider}"
								>
									<am-dashboard-theme-toggle></am-dashboard-theme-toggle>
								</span>
								<am-link
									class="${CSS.dropdownLink}"
									target="${Routes.system}?section=${Sections.users}"
								>
									<i class="bi bi-people"></i>
									<span>$${App.text('systemUsers')}</span>
								</am-link>
								<am-form api="Session/logout"></am-form>
								<am-submit
									class="${CSS.dropdownLink}"
									form="Session/logout"
								>
									<i class="bi bi-box-arrow-right"></i>
									<span>
										$${App.text('signOut')} $${App.user.name}
									</span>
								</am-submit>
							</div>
						</am-dropdown>
					</span>
				</div>
			</div>
			<am-sidebar class="am-l-dashboard__sidebar">
				<nav class="${CSS.nav}">
					<span class="${CSS.navItem}">
						<a href="${App.baseURL}" class="${CSS.navLink}">
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
						page="system"
						icon="sliders"
						text="systemTitle"
					></am-nav-item>
					<am-nav-item
						page="shared"
						icon="file-medical"
						text="sharedTitle"
					></am-nav-item>
					<am-nav-item
						page="packages"
						icon="box-seam"
						text="packagesTitle"
						badge="am-sidebar-outdated-packages-indicator"
					></am-nav-item>
				</nav>
				<am-nav-tree></am-nav-tree>
			</am-sidebar>
			<div class="am-l-dashboard__main">${main}</div>
			<footer class="am-l-dashboard__footer">
				<div class="${CSS.flex} ${CSS.flexGap} ${CSS.textMuted}">
					<span
						class="${CSS.iconText}"
						am-tooltip="$${App.user.email}"
						am-tooltip-options="placement: top"
					>
						<i class="bi bi-person-square"></i>
						<span>$${App.user.name}</span>
					</span>
					<span>&mdash;</span>
					<span>Automad</span>
					<span>${App.version}</span>
				</div>
			</footer>
		</div>
		<!-- Jumpbar Modal -->
		<am-modal-jumpbar id="am-jumpbar-modal"></am-modal-jumpbar>
		<!-- New Page Modal -->
		<am-modal id="am-add-page-modal">
			<div class="${CSS.modalDialog}">
				<am-form api="Page/add">
					<div class="${CSS.modalHeader}">
						<span>$${App.text('addPage')}</span>
						<am-modal-close
							class="${CSS.modalClose}"
						></am-modal-close>
					</div>
					<div class="${CSS.modalBody}">
						${
							createField(
								'am-input',
								null,
								{
									key: `new-${App.reservedFields.AM_KEY_TITLE}`,
									name: App.reservedFields.AM_KEY_TITLE,
									label: titleCase(
										App.reservedFields.AM_KEY_TITLE
									),
									value: '',
								},
								[],
								{ required: '' }
							).outerHTML
						}
						<div class="${CSS.field}">
							<label class="${CSS.fieldLabel}">$${App.text('pageTemplate')}</label>
							${createTemplateSelect('').outerHTML}
						</div>
						${
							createField('am-toggle-large', null, {
								key: App.reservedFields.AM_KEY_PRIVATE,
								name: App.reservedFields.AM_KEY_PRIVATE,
								value: true,
							}).outerHTML
						}
						<div class="${CSS.field}">
							<label class="${CSS.fieldLabel}">$${App.text('selectTargetNewPage')}</label>
							<am-page-select-tree></am-page-select-tree>
						</div>
					</div>
					<div class="${CSS.modalFooter}">
						<am-modal-close class="${CSS.button} ${CSS.buttonLink}">
							$${App.text('cancel')}
						</am-modal-close>
						<am-submit
							class="${CSS.button} ${CSS.buttonAccent}"
						>
							$${App.text('addPage')}
						</am-submit>
					</div>
				</am-form>
			</div>
		</am-modal>
	`;
};
