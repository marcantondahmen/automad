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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { App } from '@/core';
import { KeyValueMap } from '@/types';
import { BaseSelectInline } from './BaseSelectInline';

export class LineHeightInline extends BaseSelectInline {
	/**
	 * The tool title.
	 *
	 * @static
	 */
	static get title(): string {
		return App.text('lineHeight');
	}

	/**
	 * The tool options.
	 */
	protected readonly options = [
		'normal',
		'100%',
		'105%',
		'110%',
		'115%',
		'120%',
		'125%',
		'130%',
		'135%',
		'140%',
		'145%',
		'150%',
		'155%',
		'160%',
		'165%',
		'170%',
		'175%',
		'180%',
	];

	/**
	 * The tool default.
	 */
	protected default = 'normal';

	/**
	 * The tool property.
	 */
	protected property = 'line-height';

	/**
	 * The tool sanitize config.
	 *
	 * @static
	 */
	static get sanitize(): KeyValueMap {
		return {
			'am-inline-line-height': true,
		};
	}

	/**
	 * The tool tag.
	 */
	get tag(): string {
		return 'AM-INLINE-LINE-HEIGHT';
	}

	/**
	 * The tool icon.
	 */
	get icon(): string {
		return '<i class="bi bi-distribute-vertical"></i>';
	}
}
