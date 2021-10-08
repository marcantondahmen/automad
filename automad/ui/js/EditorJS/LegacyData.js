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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

class AutomadLegacyData {
	get targetVersion() {
		return '1.9.0';
	}

	constructor(data) {
		this.data = data;
	}

	convert() {
		let data = this.data;

		if (data.blocks === undefined) {
			return data;
		}

		if (
			this.normalizeVersion(data.automadVersion) >=
			this.normalizeVersion(this.targetVersion)
		) {
			return data;
		}

		console.log('Converting legacy block data ...');

		data = this.convertLayout(data);
		data = this.convertLists(data);

		return data;
	}

	normalizeVersion(version) {
		if (version === undefined) {
			version = '0.0.0';
		}

		const normalized = version
			.split('.')
			.map((n) => {
				return n.padStart(3, '0');
			})
			.join('');

		return normalized;
	}

	convertLayout(data) {
		data.blocks.forEach((block) => {
			if (block.tunes === undefined) {
				block.tunes = {
					layout: {
						width: block.data.widthFraction || false,
						stretched: block.data.stretched || false,
					},
				};
			}
		});

		return data;
	}

	convertLists(data) {
		data.blocks.forEach((block) => {
			if (block.type == 'lists') {
				block.data.items = block.data.items.map((item) => {
					if (typeof item == 'string') {
						return { content: item, items: [] };
					}

					return item;
				});
			}
		});

		return data;
	}
}
