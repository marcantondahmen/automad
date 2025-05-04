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
	create,
	CSS,
	getTagFromRoute,
	html,
	Route,
} from '@/admin/core';
import {
	SwitcherDropdownData,
	SwitcherDropdownItem,
	SystemSectionData,
} from '@/admin/types';
import { Section } from '@/common';
import { renderCacheSection } from './Partials/System/Cache';
import { renderDebugSection } from './Partials/System/Debug';
import { renderFeedSection } from './Partials/System/Feed';
import { renderI18nSection } from './Partials/System/I18n';
import { renderLanguageSection } from './Partials/System/Language';
import { renderUpdateSection } from './Partials/System/Update';
import { renderUsersSection } from './Partials/System/Users';
import { renderMailSection } from './Partials/System/Mail';
import { BaseDashboardLayoutComponent } from './BaseDashboardLayout';

/**
 * The system settings sections data.
 */
const getSystemSections = (): SystemSectionData[] => {
	const mail = App.isCloud
		? []
		: [
				{
					section: Section.mail,
					icon: 'envelope-at',
					title: App.text('systemMail'),
					info: App.text('systemMailCardInfo'),
					state: '<am-system-mail-indicator></am-system-mail-indicator>',
					render: renderMailSection,
					narrowIcon: false,
				},
			];

	const debug = App.isCloud
		? []
		: [
				{
					section: Section.debug,
					icon: 'bug',
					title: App.text('systemDebug'),
					info: App.text('systemDebugCardInfo'),
					state: '<am-system-debug-indicator></am-system-debug-indicator>',
					render: renderDebugSection,
				},
			];

	return [
		{
			section: Section.cache,
			icon: 'device-ssd',
			title: App.text('systemCache'),
			info: App.text('systemCacheCardInfo'),
			state: '<am-system-cache-indicator></am-system-cache-indicator>',
			render: renderCacheSection,
			narrowIcon: true,
		},
		{
			section: Section.users,
			icon: 'person-badge',
			title: App.text('systemUsers'),
			info: App.text('systemUsersCardInfo'),
			state: html`
				<span class="${CSS.textMuted}">
					${App.text('systemUsersRegistered')}:
					<am-user-count-indicator></am-user-count-indicator>
				</span>
			`,
			render: renderUsersSection,
			narrowIcon: true,
		},
		{
			section: Section.feed,
			icon: 'rss',
			title: App.text('systemRssFeed'),
			info: App.text('systemRssFeedCardInfo'),
			state: '<am-system-feed-indicator></am-system-feed-indicator>',
			render: renderFeedSection,
		},
		...mail,
		{
			section: Section.i18n,
			icon: 'globe',
			title: App.text('systemI18n'),
			info: App.text('systemI18nCardInfo'),
			state: '<am-system-i18n-indicator></am-system-i18n-indicator>',
			render: renderI18nSection,
		},
		{
			section: Section.language,
			icon: 'translate',
			title: App.text('systemLanguage'),
			info: App.text('systemLanguageCardInfo'),
			state: html`
				<span class="${CSS.textMuted}">
					${Object.keys(App.languages).find(
						(lang) => App.languages[lang] == App.system.translation
					)}
				</span>
			`,
			render: renderLanguageSection,
		},
		...debug,
		{
			section: Section.update,
			icon: 'arrow-repeat',
			title: App.text('systemUpdate'),
			info: App.text('systemUpdateCardInfo'),
			state: '<am-system-update-indicator></am-system-update-indicator>',
			render: renderUpdateSection,
		},
	];
};

/**
 * The system view.
 *
 * @extends BaseDashboardLayoutComponent
 */
export class SystemComponent extends BaseDashboardLayoutComponent {
	/**
	 * The section data array.
	 */
	private sectionData: SystemSectionData[] = null;

	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected get pageTitle(): string {
		return App.text('systemTitle');
	}

	/**
	 * Create the switcher dropdown data.
	 *
	 * @returns the menu data
	 */
	private get menuData(): SwitcherDropdownData {
		const items: SwitcherDropdownItem[] = [];

		this.sectionData.forEach((item: SystemSectionData) => {
			items.push({ title: item.title, section: item.section });
		});

		return { overview: { icon: 'grid', section: Section.overview }, items };
	}

	/**
	 * Render the main partial.
	 *
	 * @returns the rendered HTML
	 */
	protected renderMainPartial(): string {
		const menu = create('am-switcher-dropdown', [], { [Attr.narrow]: '' });

		this.sectionData = getSystemSections();
		menu.data = this.menuData;

		return html`
			<am-breadcrumbs-route
				${Attr.target}="${Route.system}"
				${Attr.text}="${this.pageTitle}"
				${Attr.narrow}
			></am-breadcrumbs-route>
			${menu.outerHTML}
			<section class="${CSS.layoutDashboardSection}">
				<div
					class="${CSS.layoutDashboardContent} ${CSS.layoutDashboardContentNarrow}"
				>
					${this.renderSections()}${this.renderOverviewSection()}
				</div>
			</section>
		`;
	}

	/**
	 * Render the sections.
	 *
	 * @returns The actual section content.
	 */
	private renderSections(): string {
		let sections = '';

		this.sectionData.forEach((item: SystemSectionData) => {
			sections += html`
				<am-switcher-section name="${item.section}">
					${item.render()}
				</am-switcher-section>
			`;
		});

		return sections;
	}

	/**
	 * Render the overview section grid.
	 *
	 * @returns the rendered overview
	 */
	private renderOverviewSection(): string {
		return html`
			<am-switcher-section name="${Section.overview}">
				<div class="${CSS.grid}" style="--min: 17rem;">
					${this.renderOverviewCards(this.sectionData.slice(0, 2))}
				</div>
				<div
					class="${CSS.grid}"
					style="--min: ${App.isCloud ? `17` : `13`}rem;"
				>
					${this.renderOverviewCards(this.sectionData.slice(2, 8))}
				</div>
			</am-switcher-section>
		`;
	}

	/**
	 * Render the main overview section menu.
	 *
	 * @param data
	 * @returns the rendered overview cards
	 */
	private renderOverviewCards(data: SystemSectionData[]): string {
		let cards = '';

		data.forEach((item) => {
			cards += html`
				<am-switcher-link
					class="${CSS.card} ${CSS.cardHover}"
					${Attr.section}="${item.section}"
				>
					<div class="${CSS.flexItemGrow}">
						<span
							class="${CSS.cardIcon} ${CSS.displaySmallNone} ${item.narrowIcon
								? CSS.cardIconNarrow
								: ''}"
						>
							<i class="bi bi-${item.icon}"></i>
						</span>
						<div class="${CSS.cardTitle}">${item.title}</div>
						<div class="${CSS.cardBody}">${item.info}</div>
					</div>
					${item.state
						? `<div class="${CSS.cardState}">${item.state}</div>`
						: ''}
				</am-switcher-link>
			`;
		});

		return cards;
	}
}

customElements.define(getTagFromRoute(Route.system), SystemComponent);
