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

import { fractions } from '@/editor/tunes/Layout';

export interface ClassTuneData {
	value: string;
}

export interface IdTuneData {
	value: string;
}

export interface PaddingTuneData {
	top: string;
	right: string;
	bottom: string;
	left: string;
}

export type LayoutFraction = (typeof fractions)[number];

export interface LayoutTuneData {
	stretched: boolean;
	width: LayoutFraction;
}

export type TextAlignOption = 'left' | 'center' | 'right';

export interface TextAlignRadio {
	value: TextAlignOption;
	icon: string;
}

export interface TextAlignTuneData {
	align: TextAlignOption;
}
