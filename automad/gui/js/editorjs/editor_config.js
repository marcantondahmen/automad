/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2020-2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


class AutomadEditorConfig {

	static get cls() {

		return {
			editor: 'codex-editor',
			redactor: 'codex-editor__redactor',
			actionsButton: 'ce-toolbar__actions',
			actionsOpened: 'ce-toolbar__actions--opened',
			block: 'ce-block',
			blockFocused: 'ce-block--focused',
			blockContent: 'ce-block__content',
			input: 'cdx-input',
			settingsButton: 'cdx-settings-button',
			settingsLayout: 'am-block-settings-layout'
		}

	}

	static tools(holder, key, hasNestedEditor) {

		var tools = {};

		if (hasNestedEditor) {
			tools = {
				nested: {
					class: AutomadBlockNested,
					config: {
						parentEditorId: holder,
						parentKey: key
					}
				}
			};
		}

		return Object.assign(tools, {

			paragraph: {
				class: AutomadBlockParagraph,
				inlineToolbar: true
			},
			header: {
				class: AutomadBlockHeader,
				shortcut: 'CMD+SHIFT+H',
				inlineToolbar: ['italic', 'underline', 'link', 'editorJSStyle'],
				config: {
					levels: [1, 2, 3, 4, 5, 6],
					defaultLevel: 2
				}
			},
			lists: {
				class: AutomadBlockList,
				inlineToolbar: true,
			},
			table: {
				class: AutomadBlockTable,
				inlineToolbar: true
			},
			quote: {
				class: AutomadBlockQuote,
				inlineToolbar: true
			},
			delimiter: AutomadBlockDelimiter,
			image: {
				class: AutomadBlockImage,
				inlineToolbar: true
			},
			gallery: AutomadBlockGallery,
			slider: AutomadBlockSlider,
			buttons: {
				class: AutomadBlockButtons,
				inlineToolbar: ['italic', 'bold', 'underline', 'editorJSStyle']
			},
			pagelist: AutomadBlockPagelist,
			filelist: AutomadBlockFilelist,
			toc: {
				class: AutomadBlockToc,
				config: { key: key }
			},
			code: AutomadBlockTextareaCode,
			raw: AutomadBlockTextareaRaw,
			mail: AutomadBlockMail,
			snippet: AutomadBlockSnippet,
			embed: AutomadBlockEmbed,
			underline: Underline,
			inlineCode: {
				class: InlineCode,
				shortcut: 'CMD+SHIFT+M'
			},
			marker: {
				class: Marker
			},
			editorJSStyle: {
				class: EditorJSStyle,
				shortcut: 'CMD+SHIFT+S'
			},
			editorJSInspector: EditorJSInspector

		});

	}

}