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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { CodeLanguage } from '@/admin/types';
import CodeFlask from 'codeflask';
import { debounce, getLogger } from '@/admin/core';
import { Prism, Languages } from '@/prism/prism';

/**
 * A thin wrapper around CodeFlask.
 *
 * @see {@link github https://github.com/kazzkiq/CodeFlask}
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

		this.codeFlask.addLanguage(
			language,
			Prism.languages[language] as Languages
		);

		this.codeFlask.updateCode(code);

		getLogger().log('Prism:', language, Prism.languages[language]);

		this.codeFlask.onUpdate(
			debounce(() => {
				onChange(this.codeFlask.getCode());
			}, 20)
		);
	}
}
