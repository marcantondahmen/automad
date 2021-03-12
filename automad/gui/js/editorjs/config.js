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

	static tools(isNested, readOnly) {

		var tools = {},
			inlineTools = {},
			inlineAll = false,
			inlineReduced = false;

		if (!isNested) {
			tools = {
				nested: {
					class: AutomadBlockNested,
					config: {
						allowStretching: true
					}
				}
			};
		}

		if (!readOnly) {
			inlineAll = ['bold', 'italic', 'underline', 'link', 'fontSize', 'color', 'background', 'editorJSStyle', 'editorJSInspector'];
			inlineReduced = ['bold', 'italic', 'underline', 'fontSize', 'color', 'background'];
			inlineTools = {
				underline: {
					class: Underline,
					shortcut: 'CMD+U'
				},
				inlineCode: {
					class: InlineCode,
					shortcut: 'CMD+SHIFT+M'
				},
				color: AutomadColor,
				background: AutomadBackground,
				fontSize: AutomadFontSize,
				editorJSStyle: {
					class: EditorJSStyle.StyleInlineTool,
					shortcut: ' '
				},
				editorJSInspector: EditorJSInspector
			};
		}

		return Object.assign(tools, {

			paragraph: {
				class: AutomadBlockParagraph,
				inlineToolbar: inlineAll,
				config: {
					isNested: isNested
				}
			},
			header: {
				class: AutomadBlockHeader,
				shortcut: 'CMD+SHIFT+H',
				inlineToolbar: inlineReduced,
				config: {
					levels: [1, 2, 3, 4, 5, 6],
					defaultLevel: 2,
					isNested: isNested
				}
			},
			lists: {
				class: AutomadBlockList,
				inlineToolbar: inlineAll,
				config: {
					isNested: isNested
				}
			},
			table: {
				class: AutomadBlockTable,
				inlineToolbar: inlineAll,
				config: {
					isNested: isNested
				}
			},
			quote: {
				class: AutomadBlockQuote,
				inlineToolbar: inlineAll,
				config: {
					isNested: isNested
				}
			},
			delimiter: { 
				class: AutomadBlockDelimiter,
				config: {
					allowStretching: true,
					isNested: isNested
				}
			},
			image: {
				class: AutomadBlockImage,
				inlineToolbar: inlineAll,
				config: {
					allowStretching: true,
					isNested: isNested
				}
			},
			gallery: {
				class: AutomadBlockGallery,
				config: {
					allowStretching: true,
					isNested: isNested
				}
			},
			slider: {
				class: AutomadBlockSlider,
				config: {
					allowStretching: true,
					isNested: isNested
				}
			},
			buttons: {
				class: AutomadBlockButtons,
				inlineToolbar: inlineReduced,
				config: {
					isNested: isNested
				}
			},
			pagelist: {
				class: AutomadBlockPagelist,
				config: {
					allowStretching: true,
					isNested: isNested
				}
			},
			filelist: { 
				class: AutomadBlockFilelist,
				config: {
					isNested: isNested
				}
			},
			toc: {
				class: AutomadBlockToc,
				config: {
					isNested: isNested
				}
			},
			code: {
				class: AutomadBlockTextareaCode,
				config: {
					isNested: isNested
				}
			},
			raw: {
				class: AutomadBlockTextareaRaw,
				config: {
					isNested: isNested
				}
			},
			mail: {
				class: AutomadBlockMail,
				config: {
					isNested: isNested
				}
			},
			snippet: {
				class: AutomadBlockSnippet,
				config: {
					isNested: isNested
				}
			},
			embed: { 
				class: AutomadBlockEmbed,
				config: {
					allowStretching: true,
					isNested: isNested
				}
			}

		}, inlineTools);

	}

}