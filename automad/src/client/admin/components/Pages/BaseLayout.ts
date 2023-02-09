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

import { setDocumentTitle } from '../../core';
import { Partials } from '../../types';
import { BaseComponent } from '../Base';

/**
 * The base view component.
 *
 * @extends BaseComponent
 */
export abstract class BaseLayoutComponent extends BaseComponent {
	/**
	 * The template render function used to render the view.
	 */
	protected render: Function;

	/**
	 * Set the page title that is used a document title suffix.
	 */
	protected abstract get pageTitle(): string;

	/**
	 * An array of partials that must be provided in order to render partial references.
	 */
	protected partials: Partials = {};

	/**
	 * The public init function that is called on the created element in order to
	 * init the view befor it is connected.
	 *
	 * @returns the rendered view
	 */
	init(): HTMLElement {
		setDocumentTitle(this.pageTitle);
		this.innerHTML = this.render(this.partials);

		return this;
	}
}
