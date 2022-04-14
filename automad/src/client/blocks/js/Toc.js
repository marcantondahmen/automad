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
 */

+(function (AutomadBlocks) {
	AutomadBlocks.Toc = {
		getItemLevel: function (item) {
			return parseInt(item.tagName.replace(/h/i, ''));
		},

		generateToc: function (container, items) {
			let type = 'ul',
				open = 0,
				lastLevel = 1,
				html = '';

			if (container.classList.contains('am-toc-ordered')) {
				type = 'ol';
			}

			for (var i = 0; i < items.length; ++i) {
				const level = AutomadBlocks.Toc.getItemLevel(items[i]),
					id = items[i].id,
					text = items[i].textContent;

				if (level > lastLevel) {
					let diff = level - lastLevel;

					for (let n = 1; n <= diff; n++) {
						open++;
						html += `<${type}><li>`;
					}
				}

				if (level < lastLevel) {
					let diff = lastLevel - level;

					for (let n = 1; n <= diff; n++) {
						open--;
						html += `</li></${type}>`;
					}
				}

				if (level <= lastLevel) {
					html += `</li><li>`;
				}

				html += `<a href="#${id}">${text}</a>`;
				lastLevel = level;
			}

			for (var i = 1; i <= open; i++) {
				html += `</li></${type}>`;
			}

			container.innerHTML = html;
		},

		init: function () {
			var items = document.querySelectorAll('h2[id], h3[id], h4[id]'),
				containers = document.querySelectorAll('am-toc');

			for (var i = 0; i < containers.length; ++i) {
				AutomadBlocks.Toc.generateToc(containers[i], items);
			}
		},
	};

	document.addEventListener('DOMContentLoaded', AutomadBlocks.Toc.init);
})((window.AutomadBlocks = window.AutomadBlocks || {}));
