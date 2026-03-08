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

import { AttributeTuneData } from '@/admin/types';
import { BaseAttributeTune } from './BaseAttributeTune';

export class IdTune extends BaseAttributeTune {
	/**
	 * The sort order for this tune.
	 */
	public sort: number = 201;

	/**
	 * The tune title.
	 */
	get title() {
		return 'ID';
	}

	/**
	 * The tune icon.
	 */
	get icon() {
		return '<span style="font-size: 1.2rem">#</span>';
	}

	/**
	 * The attribute name.
	 */
	protected getAttrName(): string {
		return 'id';
	}

	/**
	 * The validation pattern for the input field.
	 */
	protected getInputPattern(): string {
		return '[\\w\\-_]*';
	}

	/**
	 * Render the display value.
	 */
	protected renderAttr(): string {
		return `#${this.data}`;
	}

	/**
	 * Sanitize form data before setting the current state.
	 *
	 * @param data
	 * @return the sanitized data
	 */
	protected sanitize(data: AttributeTuneData): AttributeTuneData {
		return (data || '').replace(/[^\w_-]+/g, '-').trim();
	}
}
