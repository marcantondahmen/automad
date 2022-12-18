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

import {
	App,
	Attr,
	createField,
	CSS,
	html,
	Route,
	titleCase,
} from '../../../core';
import { Partials } from '../../../types';
import { createTemplateSelect } from '../../Fields/PageTemplate';
import { Section } from '../../Switcher/Switcher';

export const dashboardLayout = ({ main }: Partials) => {
	return html`
		<div class="${CSS.layoutDashboard}">
			<div class="${CSS.layoutDashboardNavbar} ${CSS.layoutDashboardNavbarLeft}">
				<div class="${CSS.navbar}">
					<am-link ${Attr.target}="${Route.home}" class="${CSS.navbarItem}">
						<am-logo></am-logo>
					</am-link>
					<am-modal-toggle
						${Attr.modal}="#am-add-page-modal"
						class="${CSS.navbarItem}"
						${Attr.tooltip}="${App.text('addPage')}"
					>
						<span>New</span>
						<i class="bi bi-plus-lg"></i>
					</am-modal-toggle>
				</div>
			</div>
			<div class="${CSS.layoutDashboardNavbar} ${CSS.layoutDashboardNavbarRight}">
				<div class="${CSS.navbar}">
					<am-modal-toggle
						class="${CSS.navbarItem}"
						${Attr.modal}="#am-jumpbar-modal"
					>
						<span>${App.text('jumpbarButtonText')}</span>
						<am-key-combo-badge ${Attr.key}="J">
					</am-modal-toggle>
					<span class="${CSS.navbarGroup}">
						<am-navbar-update-indicator></am-navbar-update-indicator>
						<am-navbar-outdated-packages-indicator></am-navbar-outdated-packages-indicator>
						<am-navbar-debug-indicator></am-navbar-debug-indicator>
						<am-dropdown ${Attr.right}>
							<span class="${CSS.navbarItem}">
								<i class="bi bi-three-dots"></i>
							</span>
							<div class="${CSS.dropdownItems}">
								<span
									class="${CSS.dropdownContent} ${CSS.dropdownDivider}"
								>
									<am-dashboard-theme-toggle></am-dashboard-theme-toggle>
								</span>
								<am-modal-toggle class="${CSS.dropdownLink}" ${Attr.modal}="#am-about-modal">
									<i class="bi bi-info-circle"></i>
									<span>${App.text('aboutAutomad')}</span>
								</am-modal-toggle>
								<am-link
									class="${CSS.dropdownLink} ${CSS.dropdownDivider}"
									${Attr.target}="${Route.system}?section=${Section.users}"
								>
									<i class="bi bi-people"></i>
									<span>${App.text('systemUsers')}</span>
								</am-link>
								<am-form ${Attr.api}="Session/logout"></am-form>
								<am-submit
									class="${CSS.dropdownLink}"
									${Attr.form}="Session/logout"
								>
									<i class="bi bi-box-arrow-right"></i>
									<span>
										${App.text('signOut')} ${App.user.name}
									</span>
								</am-submit>
							</div>
						</am-dropdown>
					</span>
				</div>
			</div>
			<am-sidebar class="${CSS.layoutDashboardSidebar}">
				<nav class="${CSS.nav}">
					<span class="${CSS.navItem}">
						<a href="${App.baseURL}" class="${CSS.navLink}">
							<am-icon-text
								${Attr.icon}="window-desktop"
								${Attr.text}="$${App.sitename}"
							></am-icon-text>
						</a>
					</span>
					<am-nav-item
						${Attr.page}="search"
						${Attr.icon}="search"
						${Attr.text}="searchTitle"
					></am-nav-item>
					<am-nav-item
						${Attr.page}="system"
						${Attr.icon}="sliders"
						${Attr.text}="systemTitle"
					></am-nav-item>
					<am-nav-item
						${Attr.page}="shared"
						${Attr.icon}="file-medical"
						${Attr.text}="sharedTitle"
					></am-nav-item>
					<am-nav-item
						${Attr.page}="packages"
						${Attr.icon}="box-seam"
						${Attr.text}="packagesTitle"
						${Attr.badge}="am-sidebar-outdated-packages-indicator"
					></am-nav-item>
				</nav>
				<am-nav-tree></am-nav-tree>
			</am-sidebar>
			<div class="${CSS.layoutDashboardMain}">${main}</div>
			<footer class="${CSS.layoutDashboardFooter}">
				<div class="${CSS.flex} ${CSS.flexGap} ${CSS.textMuted}">
					<span
						class="${CSS.iconText}"
						${Attr.tooltip}="${App.user.email}"
						${Attr.tooltipOptions}="placement: top"
					>
						<i class="bi bi-person-square"></i>
						<span>${App.user.name}</span>
					</span>
					<span>&mdash;</span>
					<am-modal-toggle class="${CSS.cursorPointer}" ${Attr.modal}="#am-about-modal">
						Automad
						${App.version}
					</am-modal-toggle>
				</div>
			</footer>
		</div>
		<!-- Jumpbar Modal -->
		<am-modal-jumpbar id="am-jumpbar-modal"></am-modal-jumpbar>
		<!-- New Page Modal -->
		<am-modal id="am-add-page-modal">
			<div class="${CSS.modalDialog}">
				<am-form ${Attr.api}="Page/add">
					<div class="${CSS.modalHeader}">
						<span>${App.text('addPage')}</span>
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
							<label class="${CSS.fieldLabel}">${App.text('pageTemplate')}</label>
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
							<label class="${CSS.fieldLabel}">${App.text('selectTargetNewPage')}</label>
							<am-page-select-tree></am-page-select-tree>
						</div>
					</div>
					<div class="${CSS.modalFooter}">
						<am-modal-close class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('cancel')}
						</am-modal-close>
						<am-submit
							class="${CSS.button} ${CSS.buttonAccent}"
						>
							${App.text('addPage')}
						</am-submit>
					</div>
				</am-form>
			</div>
		</am-modal>
		<!-- About Automad Modal -->
		<am-modal id="am-about-modal">
			<div class="${CSS.modalDialog} ${CSS.modalDialogSmall}">
				<div class="${CSS.modalBody}">
					<am-logo></am-logo>
					<hr>
					<strong>Automad</strong>
					is a modern flat-file content management system and templating engine
					<hr>
					<span class="${CSS.flex} ${CSS.flexBetween}">
						<a href="https://automad.org" target="_blank" class="${
							CSS.textLink
						}">Open Website</a>
						<a 
							href="https://automad.org/release-notes" 
							class="${CSS.badge}"
							target="_blank"
						>
							${App.version}
						</a> 
					</span>
				</div>
			</div>
		</am-modal>
	`;
};
