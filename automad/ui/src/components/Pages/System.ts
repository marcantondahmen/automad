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

import { App, classes, getTagFromRoute, html, Routes } from '../../core';
import { SystemSectionData } from '../../types';
import { Sections } from '../Switcher/Switcher';
import { renderCacheSection } from './Partials/System/Cache';
import { renderConfigFileSection } from './Partials/System/ConfigFile';
import { renderDebugSection } from './Partials/System/Debug';
import { renderFeedSection } from './Partials/System/Feed';
import { renderLanguageSection } from './Partials/System/Language';
import { renderUpdateSection } from './Partials/System/Update';
import { renderUsersSection } from './Partials/System/Users';
import { SidebarLayoutComponent } from './SidebarLayout';

/**
 * The page view.
 *
 * @extends SidebarLayoutComponent
 */
export class SystemComponent extends SidebarLayoutComponent {
	/**
	 * The array of system item properties.
	 */
	private get items(): SystemSectionData[] {
		return [
			{
				section: Sections.cache,
				icon: 'stack',
				title: App.text('systemCache'),
				info: 'info here',
				render: renderCacheSection,
			},
			{
				section: Sections.users,
				icon: 'people',
				title: App.text('systemUsers'),
				info: 'info here',
				render: renderUsersSection,
			},
			{
				section: Sections.update,
				icon: 'arrow-repeat',
				title: App.text('systemUpdate'),
				info: 'info here',
				render: renderUpdateSection,
			},
			{
				section: Sections.feed,
				icon: 'rss',
				title: App.text('systemRssFeed'),
				info: 'info here',
				render: renderFeedSection,
			},
			{
				section: Sections.language,
				icon: 'translate',
				title: App.text('systemLanguage'),
				info: 'info here',
				render: renderLanguageSection,
			},
			{
				section: Sections.headless,
				icon: 'braces',
				title: App.text('systemHeadless'),
				info: 'info here',
				render: () => '',
			},
			{
				section: Sections.debug,
				icon: 'bug',
				title: App.text('systemDebug'),
				info: 'info here',
				render: renderDebugSection,
			},
			{
				section: Sections.config,
				icon: 'file-earmark-code',
				title: App.text('systemConfigFile'),
				info: 'info here',
				render: renderConfigFileSection,
			},
		];
	}

	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('systemTitle');
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		return html`
			<section class="am-l-main__row">
				<nav class="am-l-main__content">
					<div class="${classes.breadcrumbs}">
						<am-link
							class="${classes.breadcrumbsItem}"
							target="system"
						>
							<am-icon-text
								icon="sliders"
								text="$${App.text('systemTitle')}"
							></am-icon-text>
						</am-link>
					</div>
				</nav>
			</section>
			<am-system-menu class="am-l-main__row am-l-main__row--sticky">
				<menu class="am-l-main__content">${this.renderMenu()}</menu>
			</am-system-menu>
			<section class="am-l-main__row">
				<div class="am-l-main__content">${this.renderSections()}</div>
			</section>
		`;
	}

	/**
	 * Render the menu.
	 *
	 * @returns the menu markup
	 */
	private renderMenu(): string {
		let dropdownItems = '';

		this.items.forEach((item: SystemSectionData) => {
			dropdownItems += html`
				<am-switcher-link
					class="${classes.dropdownItem}"
					section="${item.section}"
				>
					${item.title}
				</am-switcher-link>
			`;
		});

		return html`
			<am-switcher class="${classes.flex}">
				<am-switcher-link section="${Sections.overview}">
					<i class="bi bi-grid"></i>
				</am-switcher-link>
				<am-dropdown class="${classes.flexItemGrow}">
					<am-switcher-label></am-switcher-label>
					<div class="${classes.dropdownItems}">${dropdownItems}</div>
				</am-dropdown>
			</am-switcher>
		`;
	}

	/**
	 * Render the sections.
	 *
	 * @returns The actual section content.
	 */
	private renderSections(): string {
		let sections = '';

		this.items.forEach((item: SystemSectionData) => {
			sections += html`
				<am-switcher-section name="${item.section}">
					${item.render()}
				</am-switcher-section>
			`;
		});

		return html`
			<am-switcher-section name="${Sections.overview}">
				<div class="${classes.grid}" style="--min: 15rem;">
					${this.renderOverviewCards()}
				</div>
			</am-switcher-section>
			${sections}
		`;
	}

	/**
	 * Render the main overview section menu.
	 *
	 * @returns the rendered overview
	 */
	private renderOverviewCards(): string {
		let cards = '';

		this.items.forEach((item) => {
			cards += html`
				<am-switcher-link
					class="${classes.card} ${classes.cardLink}"
					section="${item.section}"
				>
					<div class="${classes.cardBody}">
						<div class="${classes.cardIcon}">
							<i class="bi bi-${item.icon}"></i>
						</div>
						<div class="${classes.cardTitle}">${item.title}</div>
						<p>${item.info}</p>
					</div>
				</am-switcher-link>
			`;
		});

		return cards;
	}
}

customElements.define(getTagFromRoute(Routes.system), SystemComponent);
