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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { CodeLanguage } from '@/types';
import CodeFlask from 'codeflask';
import { query, debounce } from './utils';

export const codeLanguages = ['js', 'html', 'css', 'php'] as const;

/**
 * A thin wrapper around CodeFlask.
 */
export class CodeEditor {
	/**
	 * The CodeFlask instance.
	 */
	codeFlask: CodeFlask;

	/**
	 * The constructor
	 *
	 * @param element
	 * @param code
	 * @param language
	 * @param onChange
	 */
	constructor(
		element: HTMLElement,
		code: string,
		language: CodeLanguage,
		onChange: (code: string) => void = () => {}
	) {
		element.innerHTML = '';

		this.codeFlask = new CodeFlask(element, {
			lineNumbers: false,
			defaultTheme: false,
			handleTabs: true,
			handleNewLineIndentation: true,
			handleSelfClosingCharacters: true,
			tabSize: 2,
			language,
		});

		this.codeFlask.updateCode(code);

		const pre = query('pre', element);

		this.codeFlask.onUpdate(
			debounce(() => {
				element.style.height = `${
					pre.getBoundingClientRect().height
				}px`;

				onChange(this.codeFlask.getCode());
			}, 50)
		);
	}
}
