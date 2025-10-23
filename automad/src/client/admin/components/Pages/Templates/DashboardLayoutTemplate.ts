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

import {
	App,
	Attr,
	createField,
	CSS,
	FieldTag,
	getSlug,
	html,
	PageController,
	Route,
	SessionController,
	titleCase,
} from '@/admin/core';
import { Partials } from '@/admin/types';
import { Section } from '@/common';

export const dashboardLayout = ({ main, publishForm }: Partials) => {
	return html`
		<div class="${CSS.layoutDashboard}">
			<div
				class="${CSS.layoutDashboardNavbar} ${CSS.layoutDashboardNavbarLeft}"
			>
				<div class="${CSS.navbar}">
					<am-link
						${Attr.target}="${Route.home}"
						class="${CSS.navbarItem}"
					>
						<am-logo></am-logo>
					</am-link>
					<am-modal-toggle
						${Attr.modal}="#am-add-page-modal"
						class="${CSS.navbarItem}"
						${Attr.tooltip}="${App.text('addPage')}"
					>
						<span>${App.text('new')}</span>
						<i class="bi bi-plus-lg"></i>
					</am-modal-toggle>
				</div>
			</div>
			<div
				class="${CSS.layoutDashboardNavbar} ${CSS.layoutDashboardNavbarRight}"
			>
				<div class="${CSS.navbar}">
					<am-link
						${Attr.target}="${Route.home}"
						class="${CSS.displayMedium} ${CSS.navbarItem}"
					>
						<am-logo></am-logo>
					</am-link>
					<am-modal-toggle
						class="${CSS.navbarItem} ${CSS.displayMediumNone}"
						${Attr.modal}="#am-jumpbar-modal"
					>
						<span> ${App.text('jumpbarButtonText')} </span>
						<am-key-combo-badge
							${Attr.key}="J"
						></am-key-combo-badge>
					</am-modal-toggle>
					<span class="${CSS.navbarGroup}">
						<am-navbar-update-indicator
							class="${CSS.displaySmallNone}"
						></am-navbar-update-indicator>
						<am-navbar-outdated-packages-indicator
							class="${CSS.displaySmallNone}"
						></am-navbar-outdated-packages-indicator>
						<am-navbar-debug-indicator
							class="${CSS.displaySmallNone}"
						></am-navbar-debug-indicator>
						<am-undo-buttons
							class="${CSS.displaySmallNone}"
						></am-undo-buttons>
						${publishForm}
						<am-modal-toggle
							class="${CSS.navbarItem} ${CSS.displayMedium}"
							${Attr.modal}="#am-jumpbar-modal"
						>
							<i class="bi bi-menu-button-wide"></i>
						</am-modal-toggle>
						<am-sidebar-toggle
							class="${CSS.navbarItem} ${CSS.displayMedium}"
						>
							<i class="bi bi-list"></i>
						</am-sidebar-toggle>
						<am-dropdown
							class="${CSS.displayMediumNone}"
							${Attr.right}
						>
							<span class="${CSS.navbarItem}">
								<i class="bi bi-three-dots"></i>
							</span>
							<div class="${CSS.dropdownItems}">
								<span
									class="${CSS.dropdownContent} ${CSS.dropdownDivider}"
								>
									<am-dashboard-theme-toggle></am-dashboard-theme-toggle>
								</span>
								<am-modal-toggle
									class="${CSS.dropdownLink}"
									${Attr.modal}="#am-about-modal"
								>
									<i class="bi bi-info-circle"></i>
									<span>${App.text('aboutAutomad')}</span>
								</am-modal-toggle>
								<a
									href="https://automad.org"
									target="_blank"
									class="${CSS.dropdownLink} ${CSS.dropdownDivider}"
								>
									<i class="bi bi-book"></i>
									<span>${App.text('documentation')}</span>
								</a>
								<am-link
									class="${CSS.dropdownLink} ${CSS.dropdownDivider}"
									${Attr.target}="${Route.system}?section=${Section.users}"
								>
									<i class="bi bi-people"></i>
									<span>${App.text('systemUsers')}</span>
								</am-link>
								<span class="${CSS.dropdownLabel}">
									${App.text('signedInAs')} ${App.user.name}
								</span>
								<am-submit
									class="${CSS.dropdownLink}"
									${Attr.form}="${SessionController.logout}"
								>
									<i class="bi bi-box-arrow-right"></i>
									<span> ${App.text('signOut')} </span>
								</am-submit>
							</div>
						</am-dropdown>
					</span>
				</div>
			</div>
			<am-sidebar-toggle
				class="${CSS.layoutDashboardSidebarBackdrop}"
			></am-sidebar-toggle>
			<am-sidebar class="${CSS.layoutDashboardSidebar}">
				<div class="${CSS.layoutDashboardSidebarNavbar}">
					<div class="${CSS.navbar}">
						<am-modal-toggle
							${Attr.modal}="#am-add-page-modal"
							class="${CSS.navbarItem}"
							${Attr.tooltip}="${App.text('addPage')}"
						>
							<span>${App.text('new')}</span>
							<i class="bi bi-plus-lg"></i>
						</am-modal-toggle>
					</div>
				</div>
				<nav class="${CSS.nav}">
					<span class="${CSS.navItem}">
						<a
							href="${App.baseIndex || '/'}"
							class="${CSS.navLink}"
						>
							<span class="${CSS.iconText}">
								<i class="bi bi-window-desktop"></i>
								<span
									${Attr.bind}="sitename"
									${Attr.bindTo}="textContent"
								>
									$${App.sitename}
								</span>
							</span>
						</a>
					</span>
					<am-nav-item
						${Attr.page}="${Route.search}"
						${Attr.icon}="search"
						${Attr.text}="searchTitle"
					></am-nav-item>
					<am-nav-item
						${Attr.page}="${Route.system}"
						${Attr.icon}="sliders"
						${Attr.text}="systemTitle"
					></am-nav-item>
					<am-nav-item
						${Attr.page}="${Route.shared}"
						${Attr.icon}="asterisk"
						${Attr.text}="sharedTitle"
						${Attr.publicationState}="${App.sharedPublicationState}"
						${(getSlug() as Route) == Route.shared
							? `
								${Attr.bind}="publicationState"
								${Attr.bindTo}="${Attr.publicationState}"
							`
							: ''}
					></am-nav-item>
					<am-nav-item
						${Attr.page}="${Route.components}"
						${Attr.icon}="boxes"
						${Attr.text}="componentsTitle"
						${Attr.publicationState}="${App.componentsPublicationState}"
						${(getSlug() as Route) == Route.components
							? `
								${Attr.bind}="publicationState"
								${Attr.bindTo}="${Attr.publicationState}"
							`
							: ''}
					></am-nav-item>
					<am-nav-item
						${Attr.page}="${Route.packages}"
						${Attr.icon}="box-seam"
						${Attr.text}="packagesTitle"
						${Attr.badge}="am-sidebar-outdated-packages-indicator"
					></am-nav-item>
					<am-nav-item
						${Attr.page}="${Route.trash}"
						${Attr.icon}="trash3"
						${Attr.text}="trashTitle"
					></am-nav-item>
				</nav>
				<am-nav-tree></am-nav-tree>
				<div class="${CSS.nav} ${CSS.displayMedium}">
					<span class="${CSS.navItem}">
						<am-modal-toggle
							class="${CSS.navLink}"
							${Attr.modal}="#am-about-modal"
						>
							<i class="bi bi-info-circle"></i>
							<span>${App.text('aboutAutomad')}</span>
						</am-modal-toggle>
					</span>
					<span class="${CSS.navItem}">
						<am-submit
							class="${CSS.navLink}"
							${Attr.form}="${SessionController.logout}"
						>
							<i class="bi bi-box-arrow-right"></i>
							<span>
								${App.text('signOut')} ${App.user.name}
							</span>
						</am-submit>
					</span>
					<am-dashboard-theme-toggle
						${Attr.noTooltip}
					></am-dashboard-theme-toggle>
				</div>
			</am-sidebar>
			<div class="${CSS.layoutDashboardMain}">${main}</div>
			<footer class="${CSS.layoutDashboardFooter}">
				<am-modal-toggle
					class="${CSS.cursorPointer} ${CSS.flex} ${CSS.flexGap}"
					${Attr.modal}="#am-about-modal"
				>
					<span class="${CSS.badge} ${CSS.badgeMuted}">
						${App.version}
					</span>
				</am-modal-toggle>
			</footer>
		</div>
		<!-- Sign out form -->
		<am-form ${Attr.api}="${SessionController.logout}"></am-form>
		<!-- Jumpbar Modal -->
		<am-modal-jumpbar id="am-jumpbar-modal"></am-modal-jumpbar>
		<!-- New Page Modal -->
		<am-modal id="am-add-page-modal">
			<am-modal-dialog>
				<am-form ${Attr.api}="${PageController.add}">
					<am-modal-header>${App.text('addPage')}</am-modal-header>
					<am-modal-body>
						${createField(
							FieldTag.input,
							null,
							{
								key: `new-${App.reservedFields.TITLE}`,
								name: App.reservedFields.TITLE,
								label: titleCase(App.reservedFields.TITLE),
								value: App.text('newPageTitle'),
							},
							[],
							{ required: '' }
						).outerHTML}
						<div class="${CSS.field}">
							<label class="${CSS.fieldLabel}"
								>${App.text('pageTemplate')}</label
							>
							<am-page-template-select
								value=""
							></am-page-template-select>
						</div>
						${createField(FieldTag.toggleLarge, null, {
							key: `new-${App.reservedFields.PRIVATE}`,
							name: App.reservedFields.PRIVATE,
							label: App.text('keepPagePrivate'),
							value: true,
						}).outerHTML}
						<div class="${CSS.field}">
							<label class="${CSS.fieldLabel}"
								>${App.text('selectTargetNewPage')}</label
							>
							<am-page-select-tree></am-page-select-tree>
						</div>
					</am-modal-body>
					<am-modal-footer>
						<am-modal-close class="${CSS.button}">
							${App.text('cancel')}
						</am-modal-close>
						<am-submit class="${CSS.button} ${CSS.buttonPrimary}">
							${App.text('addPage')}
						</am-submit>
					</am-modal-footer>
				</am-form>
			</am-modal-dialog>
		</am-modal>
		<!-- About Automad Modal -->
		<am-modal id="am-about-modal">
			<am-modal-dialog class="${CSS.modalDialogSmall}">
				<am-modal-body>
					<am-logo></am-logo>
					<hr />
					Automad is a modern flat-file content management system and
					templating engine
					<hr />
					<span class="${CSS.flex} ${CSS.flexBetween}">
						<a
							href="https://automad.org"
							target="_blank"
							class="${CSS.textLink}"
							>Open Website</a
						>
						<a
							href="https://automad.org/release-notes"
							class="${CSS.badge}"
							target="_blank"
						>
							${App.version}
						</a>
					</span>
				</am-modal-body>
			</am-modal-dialog>
		</am-modal>
	`;
};
