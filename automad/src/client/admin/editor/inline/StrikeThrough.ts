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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App } from '@/admin/core';
import { KeyValueMap } from '@/admin/types';
import { BaseInline } from './BaseInline';

export class StrikeThroughInline extends BaseInline {
	/**
	 * The tool title.
	 *
	 * @static
	 */
	static get title(): string {
		return App.text('strikeThrough');
	}

	/**
	 * The tool sanitize config.
	 *
	 * @static
	 */
	static get sanitize(): KeyValueMap {
		return {
			s: true,
		};
	}

	/**
	 * The tool tag.
	 */
	get icon() {
		return '<i class="bi bi-type-strikethrough"></i>';
	}

	/**
	 * The tool icon.
	 */
	get tag() {
		return 'S';
	}
}
