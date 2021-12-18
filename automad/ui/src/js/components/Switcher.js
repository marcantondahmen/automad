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

import { BaseComponent } from './BaseComponent';
import { listen, query, queryAll } from '../utils/core';

const hashUpdatEventName = 'am-hashupdate';
const itemTag = 'am-switcher-item';

const getHash = () => {
	return window.location.hash.replace('#', '');
};

/**
 * The label content is set dynamically when the hash changes.
 * <am-switcher-label></am-switcher-label>
 *
 * A switcher can have any layout and is not more that a container with items.
 * <am-switcher>
 *     <am-switcher-item hash="text">Text</am-switcher-item>
 *     <am-switcher-item hash="settings">Settings</am-switcher-item>
 * </am-switcher>
 *
 * The content visibility is also dynamically controlled by hash updates.
 * <am-switcher-content hash="text">
 *     ...
 * </am-switcher-content>
 * <am-switcher-content hash="settings">
 *     ...
 * </am-switcher-content>
 */

class Switcher extends BaseComponent {
	connectedCallback() {
		setTimeout(this.updateHash.bind(this), 0);

		listen(window, 'hashchange', this.updateHash.bind(this));
	}

	updateHash() {
		let hash = getHash() || false;
		const hashes = [];

		queryAll(itemTag, this).forEach((item) => {
			hashes.push(item.getAttribute('hash'));
		});

		if (hashes.indexOf(hash) == -1) {
			window.location.hash = hashes[0];
		}

		window.dispatchEvent(new Event(hashUpdatEventName));
	}
}

class SwitcherItem extends BaseComponent {
	static get observedAttributes() {
		return ['hash'];
	}

	connectedCallback() {
		listen(window, hashUpdatEventName, this.toggle.bind(this));
		listen(this, 'click', this.select.bind(this));
	}

	toggle() {
		this.classList.toggle(
			this.cls.switcherItemActive,
			this.elementAttributes.hash == getHash()
		);
	}

	select() {
		window.location.hash = this.elementAttributes.hash;
	}
}

class SwitcherContent extends BaseComponent {
	static get observedAttributes() {
		return ['hash'];
	}

	connectedCallback() {
		listen(window, hashUpdatEventName, this.toggle.bind(this));

		this.toggle();
	}

	toggle() {
		this.classList.toggle(
			this.cls.hidden,
			this.elementAttributes.hash != getHash()
		);
	}
}

class SwitcherLabel extends BaseComponent {
	connectedCallback() {
		listen(window, hashUpdatEventName, () => {
			this.innerHTML = query(`${itemTag}[hash="${getHash()}"]`).innerHTML;
		});
	}
}

customElements.define('am-switcher', Switcher);
customElements.define('am-switcher-item', SwitcherItem);
customElements.define('am-switcher-label', SwitcherLabel);
customElements.define('am-switcher-content', SwitcherContent);
