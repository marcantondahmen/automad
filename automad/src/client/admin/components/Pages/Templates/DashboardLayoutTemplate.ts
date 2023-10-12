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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
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
	html,
	PageController,
	Route,
	SessionController,
	titleCase,
} from '@/core';
import { Partials } from '@/types';
import { createTemplateSelect } from '@/components/Fields/PageTemplate';
import { Section } from '@/components/Switcher/Switcher';

export const dashboardLayout = ({ main }: Partials) => {
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
						class="${CSS.displaySmall} ${CSS.navbarItem}"
					>
						<am-logo></am-logo>
					</am-link>
					<div class="${CSS.flex}">
						<am-modal-toggle
							class="${CSS.navbarItem}"
							${Attr.modal}="#am-jumpbar-modal"
						>
							<span class="${CSS.displaySmallNone}"
								>${App.text('jumpbarButtonText')}</span
							>
							<am-key-combo-badge
								class="${CSS.displaySmallNone}"
								${Attr.key}="J"
							></am-key-combo-badge>
							<span class="${CSS.displaySmall}"
								><i class="bi bi-search"></i
							></span>
						</am-modal-toggle>
						<am-sidebar-toggle
							class="${CSS.navbarItem} ${CSS.displaySmall}"
						>
							<i class="bi bi-list"></i>
						</am-sidebar-toggle>
					</div>
					<span class="${CSS.navbarGroup} ${CSS.displaySmallNone}">
						<am-navbar-update-indicator></am-navbar-update-indicator>
						<am-navbar-outdated-packages-indicator></am-navbar-outdated-packages-indicator>
						<am-navbar-debug-indicator></am-navbar-debug-indicator>
						<am-undo-buttons></am-undo-buttons>
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
								<am-modal-toggle
									class="${CSS.dropdownLink}"
									${Attr.modal}="#am-about-modal"
								>
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
								<am-submit
									class="${CSS.dropdownLink}"
									${Attr.form}="${SessionController.logout}"
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
			<am-sidebar-toggle
				class="${CSS.layoutDashboardSidebarBackdrop}"
			></am-sidebar-toggle>
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
						${Attr.icon}="asterisk"
						${Attr.text}="sharedTitle"
					></am-nav-item>
					<am-nav-item
						${Attr.page}="packages"
						${Attr.icon}="box-seam"
						${Attr.text}="packagesTitle"
						${Attr.badge}="am-sidebar-outdated-packages-indicator"
					></am-nav-item>
					<am-nav-item
						${Attr.page}="trash"
						${Attr.icon}="trash3"
						${Attr.text}="trashTitle"
					></am-nav-item>
				</nav>
				<am-nav-tree></am-nav-tree>
				<div class="${CSS.nav} ${CSS.displaySmall}">
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
					<am-dashboard-theme-toggle></am-dashboard-theme-toggle>
				</div>
			</am-sidebar>
			<div class="${CSS.layoutDashboardMain}">${main}</div>
			<footer class="${CSS.layoutDashboardFooter}">
				<div class="${CSS.flex} ${CSS.flexGap} ${CSS.textMuted}">
					<span
						class="${CSS.iconText}"
						${Attr.tooltip}="${App.user.email}"
						${Attr.tooltipOptions}="placement: top"
					>
						<i class="bi bi-person-fill"></i>
						<span>${App.user.name}</span>
					</span>
					<span>&mdash;</span>
					<am-modal-toggle
						class="${CSS.cursorPointer}"
						${Attr.modal}="#am-about-modal"
					>
						Automad ${App.version}
					</am-modal-toggle>
				</div>
			</footer>
		</div>
		<!-- Sign out form -->
		<am-form ${Attr.api}="${SessionController.logout}"></am-form>
		<!-- Jumpbar Modal -->
		<am-modal-jumpbar id="am-jumpbar-modal"></am-modal-jumpbar>
		<!-- New Page Modal -->
		<am-modal id="am-add-page-modal">
			<div class="${CSS.modalDialog}">
				<am-form ${Attr.api}="${PageController.add}">
					<div class="${CSS.modalHeader}">
						<span>${App.text('addPage')}</span>
						<am-modal-close
							class="${CSS.modalClose}"
						></am-modal-close>
					</div>
					<div class="${CSS.modalBody}">
						${createField(
							FieldTag.input,
							null,
							{
								key: `new-${App.reservedFields.TITLE}`,
								name: App.reservedFields.TITLE,
								label: titleCase(App.reservedFields.TITLE),
								value: '',
							},
							[],
							{ required: '' }
						).outerHTML}
						<div class="${CSS.field}">
							<label class="${CSS.fieldLabel}"
								>${App.text('pageTemplate')}</label
							>
							${createTemplateSelect('').outerHTML}
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
					</div>
					<div class="${CSS.modalFooter}">
						<am-modal-close
							class="${CSS.button} ${CSS.buttonPrimary}"
						>
							${App.text('cancel')}
						</am-modal-close>
						<am-submit class="${CSS.button} ${CSS.buttonAccent}">
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
					<hr />
					<strong>Automad</strong>
					is a modern flat-file content management system and
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
				</div>
			</div>
		</am-modal>
	`;
};
