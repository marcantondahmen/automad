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
 * Copyright (c) 2024-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import Prism from 'prismjs';

const common = {
	number: {
		pattern: /\d(.\d+)?/,
	},
	operator: {
		pattern: /(\+(?![a-z])|[\-\/\*\!]|not)/,
	},
	boolean: {
		pattern: /(true|false)/,
	},
	string: { pattern: /('([^']|\\')*'|"([^"]|\\")*")/ },
	builtin: {
		pattern: /(\|\s+)\w+/,
		lookbehind: true,
	},
	variable: {
		pattern: /(@\{\s*)[\w\?%\+\:]+/,
		lookbehind: true,
	},
	punctuation: {
		pattern: /[.,;\(\)=]/,
	},
	symbol: {
		pattern: /(@?\{|\}|\||~)/,
	},
};

/**
 * Automad syntax highlighting.
 *
 * @see {@link docs https://prismjs.com/extending.html}
 * @see {@link tokens https://prismjs.com/tokens.html}
 * @see {@link api https://prismjs.com/docs/}
 */
export const PrismAutomad = Prism.languages.insertBefore('html', 'tag', {
	comment: {
		pattern: /<#.+?#>/s,
	},
	function: {
		pattern: /<@.+?@>/s,
		inside: {
			'type-hint': /(pagelist|filelist|tags|filters)/,
			keyword: {
				pattern: /(<@\s+)\w+/,
				lookbehind: true,
			},
			property: {
				pattern: /[\w\:%]+\:\s/,
			},
			...common,
		},
	},
	...common,
});
