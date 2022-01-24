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

import { BaseComponent } from './Base';
import { classes, listen, query, queryAll } from '../core/utils';

const switcherChangeEventName = 'AutomadSwitcherChange';
const linkTag = 'am-switcher-link';

/**
 * Get the section name from the query string.
 *
 * @returns the section name that is used in the query string
 */
const getActiveSection = (): string => {
	const searchParams = new URLSearchParams(window.location.search);

	return searchParams.get('section') || '';
};

/**
 * Update the URL and its query string to reflect the active section.
 *
 * @param section
 */
const setActiveSection = (section: string): void => {
	const url = new URL(window.location.href);

	url.searchParams.set('section', section);
	window.history.pushState(null, null, url);
	window.dispatchEvent(new Event(switcherChangeEventName));
};

/**
 * A switcher menu component. A switcher can have any layout and is not more that a container with items.
 *
 * @example
 * <am-switcher>
 *     <am-switcher-link section="text">Text</am-switcher-link>
 *     <am-switcher-link section="settings">Settings</am-switcher-link>
 * </am-switcher>
 *
 * @see {@link SwitcherLinkComponent}
 * @see {@link SwitcherLabelComponent}
 * @see {@link SwitcherSectionComponent}
 * @extends BaseComponent
 */
class SwitcherComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		setTimeout(() => {
			this.onChange();
			window.dispatchEvent(new Event(switcherChangeEventName));
		}, 0);

		listen(window, switcherChangeEventName, this.onChange.bind(this));
	}

	/**
	 * The callback function triggered when the active item is changed.
	 */
	onChange(): void {
		let activeSection = getActiveSection();
		const sections: string[] = [];

		queryAll(linkTag, this).forEach((link: SwitcherLinkComponent) => {
			sections.push(link.getAttribute('section'));
		});

		if (sections.indexOf(activeSection) == -1) {
			setActiveSection(sections[0]);
		}

		query('html').scrollTop = 0;
	}
}

/**
 * A switcher link that is part of a switcher menu component.
 *
 * @see {@link SwitcherComponent}
 * @extends BaseComponent
 */
class SwitcherLinkComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['section'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		listen(window, switcherChangeEventName, this.toggle.bind(this));
		listen(this, 'click', this.select.bind(this));
	}

	/**
	 * Toggle the active state of the switcher link.
	 */
	toggle(): void {
		this.classList.toggle(
			classes.switcherLinkActive,
			this.elementAttributes.section == getActiveSection()
		);
	}

	/**
	 * Set the active section.
	 */
	select(): void {
		setActiveSection(this.elementAttributes.section);
	}
}

/**
 * A switcher section that contains the content that will be toggled by a switcher menu.
 *
 * @example
 * <am-switcher-section name="settings">...</am-switcher-section>
 * <am-switcher-section name="text">...</am-switcher-section>
 *
 * @see {@link SwitcherComponent}
 * @extends BaseComponent
 */
export class SwitcherSectionComponent extends BaseComponent {
	/**
	 * The array of observed attributes.
	 *
	 * @static
	 */
	static get observedAttributes(): string[] {
		return ['name'];
	}

	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		this.toggle();

		listen(window, switcherChangeEventName, this.toggle.bind(this));
	}

	/**
	 * Toggle the section visiblity based on the query string.
	 */
	toggle(): void {
		this.classList.toggle(
			classes.displayNone,
			this.elementAttributes.name != getActiveSection()
		);

		query('html').scrollTop = 0;
	}
}

/**
 * A label that reflects the active switcher link content.
 *
 * @see {@link SwitcherComponent}
 * @extends BaseComponent
 */

class SwitcherLabelComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		listen(window, switcherChangeEventName, () => {
			this.innerHTML = query(
				`${linkTag}[section="${getActiveSection()}"]`
			).innerHTML;
		});
	}
}

customElements.define('am-switcher', SwitcherComponent);
customElements.define('am-switcher-link', SwitcherLinkComponent);
customElements.define('am-switcher-label', SwitcherLabelComponent);
customElements.define('am-switcher-section', SwitcherSectionComponent);
