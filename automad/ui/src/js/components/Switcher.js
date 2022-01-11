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
import { classes, listen, query, queryAll } from '../utils/core';

const switcherChangeEventName = 'am-switcher-change';
const linkTag = 'am-switcher-link';

/**
 * Get the section name from the query string.
 *
 * @returns {string} the section name that is used in the query string
 */
const getActiveSection = () => {
	const searchParams = new URLSearchParams(window.location.search);

	return searchParams.get('section') || '';
};

/**
 * Update the URL and its query string to reflect the active section.
 *
 * @param {string} section
 */
const setActiveSection = (section) => {
	const url = new URL(window.location.href);

	url.searchParams.set('section', section);
	window.history.pushState(null, null, url);
	window.dispatchEvent(new Event(switcherChangeEventName));
};

/**
 * A switcher menu component. A switcher can have any layout and is not more that a container with items.
 *
 * ```
 * <am-switcher>
 *     <am-switcher-link section="text">Text</am-switcher-link>
 *     <am-switcher-link section="settings">Settings</am-switcher-link>
 * </am-switcher>
 * ```
 * @see {@link SwitcherLink}
 * @see {@link SwitcherLabel}
 * @see {@link SwitcherSection}
 * @extends BaseComponent
 */
class Switcher extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		setTimeout(() => {
			this.onChange();
			window.dispatchEvent(new Event(switcherChangeEventName));
		}, 0);

		listen(window, switcherChangeEventName, this.onChange.bind(this));
	}

	/**
	 * The callback function triggered when the active item is changed.
	 */
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

/**
 * A switcher link that is part of a switcher menu component.
 *
 * @see {@link Switcher}
 * @extends BaseComponent
 */
class SwitcherLink extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @type {Array}
	 * @static
	 */
	static get observedAttributes() {
		return ['section'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		listen(window, switcherChangeEventName, this.toggle.bind(this));
		listen(this, 'click', this.select.bind(this));
	}

	/**
	 * Toggle the active state of the switcher link.
	 */
	toggle() {
		this.classList.toggle(
			classes.switcherLinkActive,
			this.elementAttributes.section == getActiveSection()
		);
	}

	/**
	 * Set the active section.
	 */
	select() {
		setActiveSection(this.elementAttributes.section);
	}
}

/**
 * A switcher section that contains the content that will be toggled by a switcher menu.
 *
 * ```
 * <am-switcher-section name="settings">...</am-switcher-section>
 * <am-switcher-section name="text">...</am-switcher-section>
 * ```
 *
 * @see {@link Switcher}
 * @extends BaseComponent
 */
class SwitcherSection extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @type {Array}
	 * @static
	 */
	static get observedAttributes() {
		return ['name'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback() {
		listen(window, switcherChangeEventName, this.toggle.bind(this));
	}

	/**
	 * Toggle the section visiblity based on the query string.
	 */
	toggle() {
		this.classList.toggle(
			classes.hidden,
			this.elementAttributes.name != getActiveSection()
		);
	}
}

/**
 * A label that reflects the active switcher link content.
 *
 * @see {@link Switcher}
 * @extends BaseComponent
 */

class SwitcherLabel extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
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
