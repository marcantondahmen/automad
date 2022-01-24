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

import { Partials, renderTemplate } from '../../core/template';
import { BaseComponent } from '../Base';

/**
 * The base view component.
 *
 * @extends BaseComponent
 */
export abstract class BaseLayoutComponent extends BaseComponent {
	/**
	 * The template for the view.
	 */
	protected template = '';

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
		this.setDocumentTitle();
		this.innerHTML = renderTemplate(this.template, this.partials);

		return this;
	}

	/**
	 * Set the document title.
	 */
	protected setDocumentTitle(): void {
		document.title = 'Automad';
	}
}
