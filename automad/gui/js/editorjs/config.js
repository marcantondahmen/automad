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
			toolbar: 'ce-toolbar',
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

	static tools(readOnly) {

		var inlineTools = {},
			inlineAll = false,
			inlineReduced = false;

		if (!readOnly) {
			inlineAll = ['bold', 'italic', 'underline', 'inlineCode', 'link', 'fontSize', 'lineHeight', 'color', 'background', 'editorJSStyle'];
			inlineReduced = ['bold', 'italic', 'underline', 'inlineCode', 'fontSize', 'lineHeight', 'color', 'background'];
			inlineTools = {
				bold: AutomadBold,
				italic: AutomadItalic,
				underline: AutomadUnderline,
				inlineCode: AutomadInlineCode,
				link: AutomadLink,
				color: AutomadColor,
				background: AutomadBackground,
				fontSize: AutomadFontSize,
				lineHeight: AutomadLineHeight,
				editorJSStyle: {
					class: EditorJSStyle.StyleInlineTool,
					shortcut: ' '
				}
			};
		}

		return Object.assign({

			paragraph: {
				class: AutomadBlockParagraph,
				inlineToolbar: inlineAll
			},
			nested: {
				class: AutomadBlockNested,
				config: {
					allowStretching: true
				}
			},
			header: {
				class: AutomadBlockHeader,
				shortcut: 'CMD+SHIFT+H',
				inlineToolbar: inlineAll,
				config: {
					levels: [1, 2, 3, 4, 5, 6],
					defaultLevel: 2
				}
			},
			lists: {
				class: AutomadBlockList,
				inlineToolbar: inlineAll
			},
			table: {
				class: AutomadBlockTable,
				inlineToolbar: inlineAll
			},
			quote: {
				class: AutomadBlockQuote,
				inlineToolbar: inlineAll
			},
			delimiter: { 
				class: AutomadBlockDelimiter,
				config: {
					allowStretching: true
				}
			},
			image: {
				class: AutomadBlockImage,
				inlineToolbar: inlineAll,
				config: {
					allowStretching: true
				}
			},
			gallery: {
				class: AutomadBlockGallery,
				config: {
					allowStretching: true
				}
			},
			slider: {
				class: AutomadBlockSlider,
				config: {
					allowStretching: true
				}
			},
			buttons: {
				class: AutomadBlockButtons,
				inlineToolbar: inlineReduced
			},
			pagelist: {
				class: AutomadBlockPagelist,
				config: {
					allowStretching: true
				}
			},
			filelist: { 
				class: AutomadBlockFilelist
			},
			toc: {
				class: AutomadBlockToc
			},
			code: {
				class: AutomadBlockTextareaCode
			},
			raw: {
				class: AutomadBlockTextareaRaw
			},
			mail: {
				class: AutomadBlockMail
			},
			snippet: {
				class: AutomadBlockSnippet
			},
			embed: { 
				class: AutomadBlockEmbed,
				config: {
					allowStretching: true
				}
			}

		}, inlineTools);

	}

}