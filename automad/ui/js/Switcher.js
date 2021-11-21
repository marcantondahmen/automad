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
 * Copyright (c) 2017-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

/*
 * The switcher component.
 */

+(function (Automad, $) {
	Automad.Switcher = function () {
		const dataAttr = {
			item: 'data-am-switcher-item',
		};

		const containers = Array.from(
			document.querySelectorAll(`[${dataAttr.item}]`)
		);

		if (!containers.length) {
			return false;
		}

		const content = {};

		containers.forEach((item) => {
			const hash = item.getAttribute(dataAttr.item);

			content[hash] = item;
		});

		if (!window.location.hash) {
			window.location.hash = Object.keys(content)[0];
		}

		const selectors = {
			link: '.am-switcher-link',
			label: '.am-switcher-dropdown-label',
		};
		const links = {};
		const dropdownLabel = document.querySelector(selectors.label);

		Array.from(document.querySelectorAll(selectors.link)).forEach(
			(link) => {
				links[link.getAttribute('href')] = link;
			}
		);

		const toggleContent = (hash) => {
			Object.values(content).forEach((item) => {
				item.classList.remove('active');
			});

			content[hash].classList.add('active');
		};

		const updateSwitcher = (hash) => {
			for (const [key, link] of Object.entries(links)) {
				link.classList.toggle('active', hash === key);
			}

			if (links && dropdownLabel) {
				dropdownLabel.innerHTML = links[hash].innerHTML;
			}
		};

		const update = () => {
			const hash = window.location.hash;

			toggleContent(hash);
			updateSwitcher(hash);
		};

		window.addEventListener('hashchange', () => {
			update();
		});

		update();
	};

	$(document).on('ready', function () {
		new Automad.Switcher();
	});
})((window.Automad = window.Automad || {}), jQuery);
