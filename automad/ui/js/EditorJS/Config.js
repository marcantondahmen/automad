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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
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
			settingsLayout: 'am-block-settings-layout',
		};
	}

	static tools(readOnly, flex) {
		const t = AutomadEditorTranslation.get;
		let inlineTools = {};
		let inlineAll = false;
		let inlineReduced = false;

		if (!readOnly) {
			inlineAll = [
				'bold',
				'italic',
				'underline',
				'inlineCode',
				'link',
				'fontSize',
				'lineHeight',
				'color',
				'background',
				'editorJSStyle',
			];
			inlineReduced = [
				'bold',
				'italic',
				'underline',
				'inlineCode',
				'fontSize',
				'lineHeight',
				'color',
				'background',
			];
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
					shortcut: ' ',
				},
			};
		}

		return Object.assign(
			{
				layout: {
					class: AutomadTuneLayout,
					config: {
						flex: flex,
					},
				},
				paragraph: {
					class: AutomadBlockParagraph,
					inlineToolbar: inlineAll,
				},
				section: {
					class: AutomadBlockSection,
				},
				header: {
					class: AutomadBlockHeader,
					shortcut: 'CMD+SHIFT+H',
					inlineToolbar: inlineAll,
					config: {
						levels: [1, 2, 3, 4, 5, 6],
						defaultLevel: 2,
					},
				},
				lists: {
					class: AutomadBlockList,
					inlineToolbar: inlineAll,
				},
				table: {
					class: AutomadBlockTable,
					inlineToolbar: inlineAll,
				},
				quote: {
					class: AutomadBlockQuote,
					inlineToolbar: inlineAll,
					config: {
						quotePlaceholder: t('quote_placeholder'),
						captionPlaceholder: t('quote_placeholder_caption'),
					},
				},
				delimiter: {
					class: AutomadBlockDelimiter,
				},
				image: {
					class: AutomadBlockImage,
					inlineToolbar: inlineAll,
				},
				gallery: {
					class: AutomadBlockGallery,
				},
				slider: {
					class: AutomadBlockSlider,
				},
				buttons: {
					class: AutomadBlockButtons,
					inlineToolbar: inlineReduced,
				},
				pagelist: {
					class: AutomadBlockPagelist,
				},
				filelist: {
					class: AutomadBlockFilelist,
				},
				toc: {
					class: AutomadBlockToc,
				},
				code: {
					class: AutomadBlockTextareaCode,
				},
				raw: {
					class: AutomadBlockTextareaRaw,
				},
				mail: {
					class: AutomadBlockMail,
				},
				snippet: {
					class: AutomadBlockSnippet,
				},
				embed: {
					class: AutomadBlockEmbed,
				},
			},
			inlineTools
		);
	}
}
