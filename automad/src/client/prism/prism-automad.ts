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
 * Copyright (c) 2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import Prism from 'prismjs';

const stringDefinition = { pattern: /('([^']|\\')*'|"([^"]|\\")*")/ };

const variableDefinition = {
	pattern: new RegExp('@{.+}', 's'),
	greedy: true,
	inside: {
		punctuation: /[\|\(\)\{\}]+/,
		string: stringDefinition,
	},
};

/**
 * Automad syntax highlighting.
 *
 * @see {@link docs https://prismjs.com/extending.html}
 * @see {@link tokens https://prismjs.com/tokens.html}
 * @see {@link api https://prismjs.com/docs/}
 */
export const PrismAutomad: Prism.Grammar = Prism.languages.extend('html', {
	comment: {
		pattern: new RegExp('<#.+?#>', 's'),
	},
	function: {
		pattern: new RegExp('<@.+?@>', 's'),
		inside: {
			keyword: {
				pattern: /(for|foreach|if|else|end|snippet|with)/,
			},
			boolean: {
				pattern: /(true|false)/,
			},
			property: {
				pattern: /[\w\:]+\:/,
			},
			variable: variableDefinition,
			string: stringDefinition,
			punctuation: {
				pattern: /[\:\{\}\(\)]/,
			},
		},
	},
	number: {
		pattern: /\d(.\d+)?/,
	},
	operator: {
		pattern: /([\+\-\/\*\!]|not)/,
	},
	tag: {
		pattern:
			/<\/?(?!\d)[^\s@>\/=$<%]+(?:\s(?:\s*[^\s>\/=]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))|(?=[\s/>])))+)?\s*\/?>/,
		greedy: true,
		inside: {
			tag: {
				pattern: /^<\/?[^\s>\/]+/,
				inside: {
					punctuation: /^<\/?/,
					namespace: /^[^\s>\/:]+:/,
				},
			},
			'special-attr': [],
			'attr-value': {
				pattern: /=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+)/,
				inside: {
					punctuation: [
						{
							pattern: /^=/,
							alias: 'attr-equals',
						},
						{
							pattern: /^(\s*)["']|["']$/,
							lookbehind: true,
						},
					],
				},
			},
			punctuation: /\/?>/,
			'attr-name': {
				pattern: /[^\s>\/]+/,
				inside: {
					namespace: /^[^\s>\/:]+:/,
				},
			},
		},
	},
	variable: variableDefinition,
});
