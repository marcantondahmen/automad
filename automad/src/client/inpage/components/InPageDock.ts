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
 * Copyright (c) 2024-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { create, Route, Section } from '@/common';
import logo from '@/common/svg/logo.svg';
import {
	restoreDockPosition,
	restoreScrollPosition,
	saveDockPosition,
} from '../sessionStore';
import { BaseInPageComponent } from './BaseInPageComponent';
// @ts-ignore
import Draggabilly from 'draggabilly';

/**
 * The main InPage component.
 */
export class InPageDockComponent extends BaseInPageComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.classList.add('am-inpage-dock');

		const csrf = this.getAttr('csrf');
		const api = this.getAttr('api');
		const dashboard = this.getAttr('dashboard');
		const url = this.getAttr('url');
		const state = this.getAttr('state');
		const labels = JSON.parse(decodeURIComponent(this.getAttr('labels')));
		const editing = this.getAttr('editing');
		const editingEnabled = parseInt(editing) === 1;

		const container = create(
			'div',
			['am-inpage-dock__container'],
			{},
			this
		);

		const handle = create(
			'span',
			['am-inpage-dock__handle'],
			{},
			container,
			'<i class="bi bi-grip-vertical"></i>'
		);

		this.initDrag(container, handle);

		create('span', ['am-inpage-dock__separator'], {}, container);

		create(
			'div',
			['am-inpage-dock__logo'],
			{},
			create(
				'a',
				['am-inpage-dock__item'],
				{ href: dashboard },
				container
			),
			logo
		);

		const createPageLink = (
			icon: string,
			section: Section,
			tooltip: string
		) => {
			create(
				'i',
				['bi', `bi-${icon}`],
				{},
				create(
					'a',
					['am-inpage-dock__item'],
					{
						href: `${dashboard}/${Route.page}?url=${encodeURIComponent(url)}&section=${section}`,
						'data-tooltip': tooltip,
					},
					container
				)
			);
		};

		createPageLink('ui-checks', Section.settings, labels.fieldsSettings);
		createPageLink('body-text', Section.text, labels.fieldsContent);
		createPageLink(
			'transparency',
			Section.customizations,
			labels.fieldsCustomizations
		);
		createPageLink('grid', Section.files, labels.uploadedFiles);

		create('span', ['am-inpage-dock__separator'], {}, container);

		create(
			'am-inpage-publish',
			['am-inpage-dock__item'],
			{ state, url, api, csrf, 'data-tooltip': labels.publish },
			container
		);

		create(
			'am-inpage-toggle',
			[
				'am-inpage-dock__item',
				...(editingEnabled
					? []
					: ['am-inpage-dock__item--toggle-highlight']),
			],
			{
				api,
				csrf,
				editing,
				'data-tooltip': editingEnabled
					? labels.inPageEditDisable
					: labels.inPageEditEnable,
			},
			container
		);

		setTimeout(() => {
			restoreScrollPosition();
		}, 0);
	}

	/**
	 * Initialize draggabilly instance and restore dock position.
	 *
	 * @param container
	 * @param handle
	 */
	private initDrag(container: HTMLElement, handle: HTMLElement): void {
		const draggable = new Draggabilly(container, {
			handle,
		});

		restoreDockPosition(draggable);

		draggable.on('dragEnd', () => {
			saveDockPosition(draggable);
		});

		handle.addEventListener('dblclick', () => {
			draggable.setPosition(0, 0);
			saveDockPosition(draggable);
		});
	}
}

customElements.define('am-inpage-dock', InPageDockComponent);
