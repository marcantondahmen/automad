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

/**
 * The label content is set dynamically when the item changes.
 * <am-switcher-label></am-switcher-label>
 *
 * A switcher can have any layout and is not more that a container with items.
 * <am-switcher>
 *     <am-switcher-link section="text">Text</am-switcher-link>
 *     <am-switcher-link section="settings">Settings</am-switcher-link>
 * </am-switcher>
 *
 * The content visibility is also dynamically controlled by item updates.
 * <am-switcher-section name="text">
 *     ...
 * </am-switcher-section>
 * <am-switcher-section name="settings">
 *     ...
 * </am-switcher-section>
 */

const switcherChangeEventName = 'am-switcher-change';
const linkTag = 'am-switcher-link';

const getActiveSection = () => {
	const searchParams = new URLSearchParams(window.location.search);

	return searchParams.get('section') || '';
};

const setActiveSection = (section) => {
	const url = new URL(window.location.href);

	url.searchParams.set('section', section);
	window.history.pushState(null, null, url);
	window.dispatchEvent(new Event(switcherChangeEventName));
};

class Switcher extends BaseComponent {
	connectedCallback() {
		setTimeout(() => {
			this.onChange();
			window.dispatchEvent(new Event(switcherChangeEventName));
		}, 0);

		listen(window, switcherChangeEventName, this.onChange.bind(this));
	}

	onChange() {
		let activeSection = getActiveSection();
		const sections = [];

		queryAll(linkTag, this).forEach((link) => {
			sections.push(link.getAttribute('section'));
		});

		if (sections.indexOf(activeSection) == -1) {
			setActiveSection(sections[0]);
		}
	}
}

class SwitcherLink extends BaseComponent {
	static get observedAttributes() {
		return ['section'];
	}

	connectedCallback() {
		listen(window, switcherChangeEventName, this.toggle.bind(this));
		listen(this, 'click', this.select.bind(this));
	}

	toggle() {
		this.classList.toggle(
			this.cls.switcherLinkActive,
			this.elementAttributes.section == getActiveSection()
		);
	}

	select() {
		setActiveSection(this.elementAttributes.section);
	}
}

class SwitcherSection extends BaseComponent {
	static get observedAttributes() {
		return ['name'];
	}

	connectedCallback() {
		listen(window, switcherChangeEventName, this.toggle.bind(this));
	}

	toggle() {
		this.classList.toggle(
			this.cls.hidden,
			this.elementAttributes.name != getActiveSection()
		);
	}
}

class SwitcherLabel extends BaseComponent {
	connectedCallback() {
		listen(window, switcherChangeEventName, () => {
			this.innerHTML = query(
				`${linkTag}[section="${getActiveSection()}"]`
			).innerHTML;
		});
	}
}

customElements.define('am-switcher', Switcher);
customElements.define('am-switcher-link', SwitcherLink);
customElements.define('am-switcher-label', SwitcherLabel);
customElements.define('am-switcher-section', SwitcherSection);
