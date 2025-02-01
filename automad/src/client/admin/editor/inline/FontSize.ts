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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App } from '@/admin/core';
import { KeyValueMap } from '@/admin/types';
import { BaseSelectInline } from './BaseSelectInline';

export class FontSizeInline extends BaseSelectInline {
	/**
	 * The tool title.
	 *
	 * @static
	 */
	static get title(): string {
		return App.text('fontSize');
	}

	/**
	 * The tool options.
	 */
	protected readonly options = [
		'70%',
		'80%',
		'90%',
		'100%',
		'110%',
		'120%',
		'130%',
		'150%',
		'175%',
		'200%',
		'250%',
		'300%',
		'350%',
		'400%',
		'450%',
		'500%',
	];

	/**
	 * The tool default.
	 */
	protected default = '100%';

	/**
	 * The tool property.
	 */
	protected property = 'font-size';

	/**
	 * The tool sanitize config.
	 *
	 * @static
	 */
	static get sanitize(): KeyValueMap {
		return {
			'am-inline-font-size': true,
		};
	}

	/**
	 * The tool tag.
	 */
	get tag(): string {
		return 'AM-INLINE-FONT-SIZE';
	}

	/**
	 * The tool icon.
	 */
	get icon(): string {
		return '<i class="bi bi-fonts"></i>';
	}
}
