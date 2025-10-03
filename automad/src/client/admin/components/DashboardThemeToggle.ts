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

import { Attr, create, CSS, EventName, titleCase } from '@/admin/core';
import { DashboardTheme, getTheme, setTheme } from '@/admin/core/theme';
import { BaseComponent } from '@/admin/components/Base';

/**
 * A theme toggle component for the dashboard.
 *
 * @extends BaseComponent
 */
class DashboardThemeToggleComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add(CSS.dashboardThemeToggle);
		this.render();

		this.listen(
			window,
			EventName.dashboardThemeChange,
			this.render.bind(this)
		);
	}

	/**
	 * Render all toggles.
	 */
	private render(): void {
		document.documentElement.classList.add('am-u-no-transition');

		this.innerHTML = '';
		this.renderThemeToggle(DashboardTheme.light, 'sun-fill');
		this.renderThemeToggle(DashboardTheme.dark, 'moon-stars');
		this.renderThemeToggle(DashboardTheme.lowContrast, 'shadows');

		setTimeout(() => {
			document.documentElement.classList.remove('am-u-no-transition');
		}, 800);
	}

	/**
	 * Render a single toggle.
	 *
	 * @param theme
	 * @param icon
	 */
	private renderThemeToggle = (theme: DashboardTheme, icon: string) => {
		const cls: string[] = [CSS.dashboardThemeToggleButton];

		if (theme == getTheme()) {
			cls.push(CSS.dashboardThemeToggleButtonActive);
		}

		const tooltip = `${titleCase(theme.replace('-', ' '))} Theme`;
		const button = create(
			'span',
			cls,
			{
				[Attr.tooltip]: tooltip,
			},
			this
		);

		button.innerHTML = `<i class="bi bi-${icon}"></i>`;

		this.listen(button, 'click', () => {
			setTimeout(() => {
				setTheme(theme);
				this.render();
			}, 400);
		});
	};
}

customElements.define(
	'am-dashboard-theme-toggle',
	DashboardThemeToggleComponent
);
