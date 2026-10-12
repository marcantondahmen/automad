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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import { Attr, create } from '@/admin/core';
import { Prism, supportedLanguages, type CodeLanguage } from '@/prism/prism';
import { BaseComponent } from '@/admin/components/Base';

import 'prismjs/plugins/line-numbers/prism-line-numbers';
import 'prismjs/plugins/toolbar/prism-toolbar';
import 'prismjs/plugins/copy-to-clipboard/prism-copy-to-clipboard';
import 'prismjs/plugins/toolbar/prism-toolbar.css';
import 'prismjs/plugins/line-numbers/prism-line-numbers.css';

/**
 * A read-only, syntax highlighted code snippet with line numbers and a copy button.
 *
 * @example
 * <am-syntax am-lang="json">{ "key": "value" }</am-syntax>
 *
 * @extends BaseComponent
 */
class SyntaxComponent extends BaseComponent {
	/**
	 * The callback function used when an element is created in the DOM.
	 */
	connectedCallback(): void {
		setTimeout(() => {
			// Zero-width spaces are added by the html template function when escaping slashes.
			const code = (this.textContent || '').replace(/​/g, '').trim();
			const requested = (this.getAttribute(Attr.lang) ||
				'none') as CodeLanguage;
			const language = supportedLanguages.includes(requested)
				? requested
				: 'none';
			const multiline = code.includes('\n');

			this.innerHTML = '';

			const pre = create(
				'pre',
				[
					`language-${language}`,
					...(multiline ? ['line-numbers'] : []),
				],
				{},
				this
			);

			const codeElement = create(
				'code',
				[`language-${language}`],
				{},
				pre
			);

			codeElement.textContent = code;

			Prism.highlightElement(codeElement);
		}, 0);
	}
}

customElements.define('am-syntax', SyntaxComponent);
