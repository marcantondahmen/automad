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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { KeyValueMap } from '../types';
import { ClassTune } from './tunes/Class';
import { DuplicateTune } from './tunes/Duplicate';
import { IdTune } from './tunes/Id';
import { LayoutTune } from './tunes/Layout';
import { SpacingTune } from './tunes/Spacing';

/**
 * The base selection of tunes that is used for all blocks.
 */
export const baseTunes = ['layout', 'spacing', 'className', 'id', 'duplicate'];

/**
 * The block tunes used.
 *
 * @param isSectionBlock
 * @return an object with tune configurations
 */
export const getBlockTunes = (isSectionBlock: boolean): KeyValueMap => {
	return {
		spacing: { class: SpacingTune },
		className: { class: ClassTune },
		id: { class: IdTune },
		layout: {
			class: LayoutTune,
			config: {
				isSectionBlock,
			},
		},
		duplicate: {
			class: DuplicateTune,
		},
	};
};
