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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { create, query, queryAll } from '@/common';
import './styles.less';
import 'dist-font-inter/variable';

type ConsentState = 'accepted' | 'declined';

declare global {
	interface Window {
		amCookieConsentText: string | undefined;
		amCookieConsentAccept: string | undefined;
		amCookieConsentDecline: string | undefined;
		amCookieConsentRevoke: string | undefined;
		amCookieConsentTooltip: string | undefined;
		amCookieConsentPlaceholder: string | undefined;
	}
}

const COOKIE_ICON = `
	<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16">
		<path d="M6 7.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m4.5.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3m-.5 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
		<path d="M8 0a7.96 7.96 0 0 0-4.075 1.114q-.245.102-.437.28A8 8 0 1 0 8 0m3.25 14.201a1.5 1.5 0 0 0-2.13.71A7 7 0 0 1 8 15a6.97 6.97 0 0 1-3.845-1.15 1.5 1.5 0 1 0-2.005-2.005A6.97 6.97 0 0 1 1 8c0-1.953.8-3.719 2.09-4.989a1.5 1.5 0 1 0 2.469-1.574A7 7 0 0 1 8 1c1.42 0 2.742.423 3.845 1.15a1.5 1.5 0 1 0 2.005 2.005A6.97 6.97 0 0 1 15 8c0 .596-.074 1.174-.214 1.727a1.5 1.5 0 1 0-1.025 2.25 7 7 0 0 1-2.51 2.224Z"/>
	</svg>
`;

const CONSENT_KEY = 'am-cookie-consent';
const REVOKED_KEY = 'am-cookie-consent-revoked';
const STATE_CHANGE_EVENT = 'AutomadConsentStateChange';

/**
 * Get all possible versions that can be used as domain value for a cookie
 * such as sub.domain.com and .domain.com.
 *
 * @return the array of domains
 */
const getDomains = (): string[] => {
	const parts = location.hostname.split('.');
	const domains = [];

	domains.push(location.hostname);

	for (let i = 0; i < parts.length - 1; i++) {
		const domain = parts.slice(i).join('.');

		if (parts.length - i >= 2) {
			domains.push(`.${domain}`);
		}
	}

	return [...new Set(domains)];
};

/**
 * Delete a cookie
 *
 * @param name
 */
const deleteCookie = (name: string): void => {
	const domains = getDomains();

	domains.forEach((domain) => {
		document.cookie = `${name}=; Max-Age=0; path=/; domain=${domain}`;
	});

	document.cookie = `${name}=; Max-Age=0; path=/`;
};

/**
 * Clear all third-party cookies as well as local and session storage.
 */
const clearData = (): void => {
	document.cookie
		.split(';')
		.map((cookie) => cookie.trim())
		.forEach((cookie) => {
			const [name] = cookie.split('=');

			if (!/^Automad-/.test(name)) {
				deleteCookie(name);
			}
		});

	localStorage.clear();
	sessionStorage.clear();
};

/**
 * CSS classes
 */
const cls = {
	banner: 'am-consent-banner',
	bannerOpen: 'am-consent-banner--open',
	bannerSmall: 'am-consent-banner--small',
	button: 'am-consent-banner__button',
	placeholder: 'am-consent-placeholder',
};

/**
 * This component can be used for embedded content as well as for scripts
 * that should only be loaded depending on the cookie consent.
 *
 * @extends HTMLElement
 */
class ConsentComponent extends HTMLElement {
	/**
	 * The banner element
	 */
	static banner: HTMLDivElement | null = null;

	/**
	 * The elements that are waiting for consent.
	 */
	static pendingInstances: ConsentComponent[] = [];

	/**
	 * The actual state.
	 */
	static set state(value: ConsentState) {
		localStorage.setItem(CONSENT_KEY, value);

		ConsentComponent.banner.classList.remove(cls.bannerOpen);
		ConsentComponent.banner.dispatchEvent(new Event(STATE_CHANGE_EVENT));
	}

	/**
	 * The actual state.
	 */
	static get state(): ConsentState {
		return localStorage.getItem(CONSENT_KEY) as ConsentState;
	}

	/**
	 * True when consent == 'accepted'.
	 */
	static get hasConsent(): boolean {
		return ConsentComponent.state === 'accepted';
	}

	/**
	 * True when consent == 'declined'.
	 */
	static get hasNoConsent(): boolean {
		return ConsentComponent.state === 'declined';
	}

	/**
	 * The type of element that will be created.
	 */
	get type(): 'iframe' | 'script' {
		return (this.getAttribute('type') as 'iframe' | 'script') ?? 'iframe';
	}

	/**
	 * The constructor.
	 */
	constructor() {
		super();
	}

	/**
	 * The connected callback.
	 */
	connectedCallback(): void {
		if (ConsentComponent.hasConsent) {
			this.injectContent();
		}

		ConsentComponent.pendingInstances.push(this);

		if (!ConsentComponent.banner) {
			this.renderBanner();

			if (localStorage.getItem(REVOKED_KEY)) {
				clearData();
			}
		}

		this.renderPlaceholder();
	}

	/**
	 * Render the cookie banner.
	 */
	private renderBanner(): void {
		ConsentComponent.banner = create(
			'div',
			[cls.banner],
			{},
			document.body,
			`${COOKIE_ICON} <span>${decodeURIComponent(window.amCookieConsentText || 'This site uses third-party cookies.')}</span>`
		);

		const icon = query('svg', ConsentComponent.banner);

		icon.addEventListener('click', () => {
			ConsentComponent.banner.classList.toggle(cls.bannerOpen);
		});

		const renderAccept = (): void => {
			const accept = create(
				'button',
				[cls.button],
				{},
				ConsentComponent.banner,
				decodeURIComponent(
					window.amCookieConsentAccept || "That's fine"
				)
			);

			accept.addEventListener('click', () => {
				ConsentComponent.state = 'accepted';
				ConsentComponent.pendingInstances.forEach((i) =>
					i.injectContent()
				);
				ConsentComponent.pendingInstances = [];
			});
		};

		const renderDecline = (): void => {
			const decline = create(
				'button',
				[cls.button],
				{},
				ConsentComponent.banner,
				decodeURIComponent(
					window.amCookieConsentDecline || 'No, thanks'
				)
			);

			decline.addEventListener('click', () => {
				ConsentComponent.state = 'declined';
				ConsentComponent.banner.classList.add(cls.bannerOpen);
			});
		};

		const renderRevoke = (): void => {
			const revoke = create(
				'button',
				[cls.button],
				{},
				ConsentComponent.banner,
				decodeURIComponent(
					window.amCookieConsentRevoke || 'Revoke consent'
				)
			);

			revoke.addEventListener('click', () => {
				ConsentComponent.state = null;
				ConsentComponent.banner.remove();

				localStorage.setItem(REVOKED_KEY, 'true');
				location.reload();
			});
		};

		const renderButtons = () => {
			queryAll(`.${cls.button}`, ConsentComponent.banner).forEach(
				(button) => {
					button.remove();
				}
			);

			if (ConsentComponent.hasNoConsent) {
				ConsentComponent.banner.classList.add(
					cls.bannerSmall,
					cls.bannerOpen
				);

				renderAccept();

				return;
			}

			if (ConsentComponent.hasConsent) {
				ConsentComponent.banner.classList.add(cls.bannerSmall);
				ConsentComponent.banner.setAttribute(
					'title',
					window.amCookieConsentTooltip || 'Manage cookie consent'
				);

				renderRevoke();

				return;
			}

			renderAccept();
			renderDecline();
		};

		ConsentComponent.banner.addEventListener(
			STATE_CHANGE_EVENT,
			renderButtons.bind(this)
		);

		ConsentComponent.banner.dispatchEvent(new Event(STATE_CHANGE_EVENT));
	}

	/**
	 * Render the placeholder.
	 */
	private renderPlaceholder(): void {
		if (this.type != 'iframe') {
			return;
		}

		this.innerHTML = '<am-consent-placeholder></am-consent-placeholder>';
	}

	/**
	 * Create the actually pending element.
	 */
	private injectContent(): void {
		const type = this.type;

		this.removeAttribute('type');

		const attributes = Array.from(this.attributes).reduce(
			(acc, attr) => {
				acc[attr.name] = attr.value;

				return acc;
			},
			{} as Record<string, string>
		);

		if (type === 'iframe') {
			const iframe = create('iframe', [], attributes);

			this.replaceWith(iframe);
		}

		if (type === 'script') {
			const script = create('script', [], attributes);

			setTimeout(() => {
				script.text = atob(this.textContent);

				this.replaceWith(script);
			}, 0);
		}
	}
}

/**
 * The placeholder element component.
 *
 * @extends HTMLElement
 */
class ConsentPlaceholderComponent extends HTMLElement {
	/**
	 * The constructor.
	 */
	constructor() {
		super();
	}

	/**
	 * The connected callback.
	 */
	connectedCallback(): void {
		this.classList.add(cls.placeholder);

		this.innerHTML = `
			${COOKIE_ICON}
			<span>${window.amCookieConsentPlaceholder || 'Cookies are disabled'}</span>
		`;
	}
}

customElements.define('am-consent', ConsentComponent);
customElements.define('am-consent-placeholder', ConsentPlaceholderComponent);
