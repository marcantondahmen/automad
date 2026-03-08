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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { App } from '@/admin/core';
import { BaseAttributeTune } from './BaseAttributeTune';
import { AttributeTuneData } from '@/admin/types';

export class ClassTune extends BaseAttributeTune {
	/**
	 * The sort order for this tune.
	 */
	public sort: number = 202;

	/**
	 * The tune title.
	 */
	get title() {
		return App.text('className');
	}

	/**
	 * The tune icon.
	 */
	get icon() {
		return '<i class="bi bi-asterisk"></i>';
	}

	/**
	 * The attribute name.
	 */
	protected getAttrName(): string {
		return 'className';
	}

	/**
	 * The validation pattern for the input field.
	 */
	protected getInputPattern(): string {
		return '[\\w\\-\\s_:]*';
	}

	/**
	 * Render the display value.
	 */
	protected renderAttr(): string {
		return `.${this.data.replace(/\s+/g, '.')}`;
	}

	/**
	 * Sanitize form data before setting the current state.
	 *
	 * @param data
	 * @return the sanitized data
	 */
	protected sanitize(data: AttributeTuneData): AttributeTuneData {
		return (data || '').replace(/[^\w_\s-]+/g, '-').trim();
	}
}
