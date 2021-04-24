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

	static tools(readOnly, flex) {

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
				inlineToolbar: inlineAll,
				config: {
					flex: flex
				}
			},
			section: {
				class: AutomadBlockSection,
				config: {
					allowStretching: true,
					flex: flex
				}
			},
			header: {
				class: AutomadBlockHeader,
				shortcut: 'CMD+SHIFT+H',
				inlineToolbar: inlineAll,
				config: {
					levels: [1, 2, 3, 4, 5, 6],
					defaultLevel: 2,
					flex: flex
				}
			},
			lists: {
				class: AutomadBlockList,
				inlineToolbar: inlineAll,
				config: {
					flex: flex
				}
			},
			table: {
				class: AutomadBlockTable,
				inlineToolbar: inlineAll,
				config: {
					flex: flex
				}
			},
			quote: {
				class: AutomadBlockQuote,
				inlineToolbar: inlineAll,
				config: {
					flex: flex
				}
			},
			delimiter: { 
				class: AutomadBlockDelimiter,
				config: {
					allowStretching: true,
					flex: flex
				}
			},
			image: {
				class: AutomadBlockImage,
				inlineToolbar: inlineAll,
				config: {
					allowStretching: true,
					flex: flex
				}
			},
			gallery: {
				class: AutomadBlockGallery,
				config: {
					allowStretching: true,
					flex: flex
				}
			},
			slider: {
				class: AutomadBlockSlider,
				config: {
					allowStretching: true,
					flex: flex
				}
			},
			buttons: {
				class: AutomadBlockButtons,
				inlineToolbar: inlineReduced,
				config: {
					flex: flex
				}
			},
			pagelist: {
				class: AutomadBlockPagelist,
				config: {
					allowStretching: true,
					flex: flex
				}
			},
			filelist: { 
				class: AutomadBlockFilelist,
				config: {
					flex: flex
				}
			},
			toc: {
				class: AutomadBlockToc,
				config: {
					flex: flex
				}
			},
			code: {
				class: AutomadBlockTextareaCode,
				config: {
					flex: flex
				}
			},
			raw: {
				class: AutomadBlockTextareaRaw,
				config: {
					flex: flex
				}
			},
			mail: {
				class: AutomadBlockMail,
				config: {
					flex: flex
				}
			},
			snippet: {
				class: AutomadBlockSnippet,
				config: {
					flex: flex
				}
			},
			embed: { 
				class: AutomadBlockEmbed,
				config: {
					allowStretching: true,
					flex: flex
				}
			}

		}, inlineTools);

	}

}