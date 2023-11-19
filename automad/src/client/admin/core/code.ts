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
import { debounce } from '@/core';

// https://github.com/PrismJS/prism/issues/1020#issuecomment-602180996
import Prism, { Languages } from 'prismjs';
import 'prismjs/components/prism-apacheconf';
import 'prismjs/components/prism-basic';
import 'prismjs/components/prism-c';
import 'prismjs/components/prism-csharp';
import 'prismjs/components/prism-cpp';
import 'prismjs/components/prism-go';
import 'prismjs/components/prism-handlebars';
import 'prismjs/components/prism-graphql';
import 'prismjs/components/prism-java';
import 'prismjs/components/prism-jsx';
import 'prismjs/components/prism-latex';
import 'prismjs/components/prism-less';
import 'prismjs/components/prism-lua';
import 'prismjs/components/prism-markdown';
import 'prismjs/components/prism-nginx';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-python';
import 'prismjs/components/prism-ruby';
import 'prismjs/components/prism-rust';
import 'prismjs/components/prism-sass';
import 'prismjs/components/prism-bash';
import 'prismjs/components/prism-sql';
import 'prismjs/components/prism-tsx';
import 'prismjs/components/prism-typescript';
import 'prismjs/components/prism-vim';
import 'prismjs/components/prism-yaml';

export const codeLanguages = [
	'apacheconf',
	'bash',
	'basic',
	'c',
	'clike',
	'csharp',
	'cpp',
	'css',
	'go',
	'graphql',
	'handlebars',
	'html',
	'java',
	'javascript',
	'jsx',
	'latex',
	'less',
	'lua',
	'markdown',
	'nginx',
	'none',
	'php',
	'powershell',
	'python',
	'ruby',
	'rust',
	'sass',
	'sql',
	'tsx',
	'typescript',
	'vim',
	'yaml',
] as const;

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

		this.codeFlask.onUpdate(
			debounce(() => {
				onChange(this.codeFlask.getCode());
			}, 20)
		);
	}
}
