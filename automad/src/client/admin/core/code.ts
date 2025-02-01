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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { CodeEditorData, CodeLanguage } from '@/admin/types';
import CodeFlask from 'codeflask';
import { create, CSS, debounce, getLogger, html } from '@/admin/core';
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
	 * @param options
	 * @param options.element
	 * @param options.code
	 * @param options.language
	 * @param [options.onChange]
	 * @param [options.placeholder]
	 * @param [options.readonly]
	 */
	constructor({
		element,
		code,
		language,
		onChange = () => {},
		placeholder = '',
		readonly = false,
	}: CodeEditorData) {
		element.innerHTML = '';

		this.codeFlask = new CodeFlask(element, {
			lineNumbers: false,
			defaultTheme: false,
			handleTabs: true,
			handleNewLineIndentation: true,
			handleSelfClosingCharacters: true,
			tabSize: 2,
			language,
			readonly,
		});

		create(
			'pre',
			[CSS.codeflaskPlaceholder, 'codeflask__pre'],
			{},
			element,
			html`$${placeholder}`
		);

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
