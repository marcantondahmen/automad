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

import { EditorJS, EditorConfig, I18nDictionary } from '@/admin/vendor/editorjs';
import { getBlockTools } from '@/admin/editor/blocks';
import { baseTunes, getBlockTunes } from '@/admin/editor/tunes';
import { BaseComponent } from '@/admin/components/Base';
import { BoldInline } from '@/admin/editor/inline/Bold';
import { CodeInline } from '@/admin/editor/inline/Code';
import { ColorInline } from '@/admin/editor/inline/Color';
import { FontSizeInline } from '@/admin/editor/inline/FontSize';
import { ItalicInline } from '@/admin/editor/inline/Italic';
import { LineHeightInline } from '@/admin/editor/inline/LineHeight';
import { LinkInline } from '@/admin/editor/inline/Link';
import { StrikeThroughInline } from '@/admin/editor/inline/StrikeThrough';
import { UnderlineInline } from '@/admin/editor/inline/Underline';
import { DragDrop } from '@/admin/editor/plugins/DragDrop';
import { EditorOutputData, KeyValueMap } from '@/admin/types';
import { App, Attr, CSS, getLogger, getSlug, query, Route } from '@/admin/core';
import {
	TextAlignCenterInline,
	TextAlignLeftInline,
	TextAlignRightInline,
} from '@/admin/editor/inline/TextAlign';
import {
	removeDeleteComponents,
	unknownBlockHandler,
} from '@/admin/editor/utils';
import { TeXInline } from '@/admin/editor/inline/TeX';

/**
 * A wrapper component for EditorJS that is basically a DOM element that represents an EditorJS instance.
 *
 * @extends BaseComponent
 */
export class EditorJSComponent extends BaseComponent {
	/**
	 * The tag name.
	 *
	 * @static
	 */
	static TAG_NAME = 'am-editor-js';

	/**
	 * The EditorJS instance that is associated with the holder.
	 */
	editor: EditorJS;

	/**
	 * Return true if the editor is place in the shared component page.
	 */
	private get isComponentEditor() {
		return getSlug() === Route.components;
	}

	/**
	 * Prepare data for rendering the editor.
	 *
	 * @param data
	 * @return The prepared data
	 */
	private prepareData(data: EditorOutputData): EditorOutputData {
		return removeDeleteComponents(data);
	}

	/**
	 * Create an EditorJS instance and bind it to the editor property.
	 *
	 * @param data
	 * @param config
	 * @param isSectionBlock
	 * @param readOnly
	 */
	init(
		data: EditorOutputData,
		config: EditorConfig,
		isSectionBlock: boolean,
		readOnly: boolean = false
	): void {
		this.style.position = 'relative';
		this.classList.add(CSS.contents);

		const createEditor = this.createEditor.bind(
			this,
			config,
			isSectionBlock,
			readOnly
		);

		try {
			this.editor = createEditor(data);
		} catch (error) {
			getLogger().error(
				'Error creating a new EditorJS instance with given data. Creating a fresh instance without data.',
				data
			);
			this.editor = createEditor();
		}
	}

	/**
	 * An isolated wrapper for creating a fresh instance with or without initial data.
	 *
	 * @param config
	 * @param isSectionBlock
	 * @param readOnly
	 * @param [data]
	 * @return the created instance
	 */
	private createEditor(
		config: EditorConfig,
		isSectionBlock: boolean,
		readOnly: boolean,
		data?: EditorOutputData
	): EditorJS {
		return new EditorJS({
			data: this.prepareData(data ?? null),
			holder: this,
			minHeight: readOnly ? 0 : 50,
			autofocus: false,
			readOnly,
			placeholder: isSectionBlock ? '' : App.text('editorPlaceholder'),
			tools: {
				...getBlockTools(this.isComponentEditor),
				...getBlockTunes(isSectionBlock),
				...this.getInlineTools(),
			},
			tunes: baseTunes,
			inlineToolbar: [
				'alignLeft',
				'alignCenter',
				'alignRight',
				'bold',
				'italic',
				'fontSize',
				'lineHeight',
				'link',
				'codeInline',
				'texInline',
				'underline',
				'strikeThrough',
				'color',
			],
			i18n: {
				messages: this.getI18nDictionary(),
			},
			onReady: (): void => {
				this.onRender();
			},
			unknownBlockHandler,
			canUseKeyboard: () => !query(`[${Attr.modalOpen}]`),
			...config,
		});
	}

	/**
	 * The inline tools used.
	 *
	 * @return an object with tool configurations
	 */
	private getInlineTools(): KeyValueMap {
		return {
			alignLeft: { class: TextAlignLeftInline },
			alignCenter: { class: TextAlignCenterInline },
			alignRight: { class: TextAlignRightInline },
			bold: { class: BoldInline },
			italic: { class: ItalicInline },
			link: { class: LinkInline },
			codeInline: { class: CodeInline },
			texInline: { class: TeXInline },
			underline: { class: UnderlineInline },
			strikeThrough: { class: StrikeThroughInline },
			color: { class: ColorInline },
			fontSize: { class: FontSizeInline },
			lineHeight: { class: LineHeightInline },
		};
	}

	/**
	 * The i18n dictionary.
	 *
	 * @return the translations object
	 */
	private getI18nDictionary(): I18nDictionary {
		return {
			ui: {
				blockTunes: {
					toggler: {
						'Click to tune': App.text('tuneOrMove'),
					},
				},
				inlineToolbar: {
					converter: {
						'Convert to': App.text('convertTo'),
					},
				},
				toolbar: {
					toolbox: {
						Add: App.text('add'),
					},
				},
			},
			toolNames: {},
			tools: {
				table: {
					'Add column to left': App.text('tableAddColumnLeft'),
					'Add column to right': App.text('tableAddColumnRight'),
					'Delete column': App.text('tableDeleteColumn'),
					'Add row above': App.text('tableAddRowAbove'),
					'Add row below': App.text('tableAddRowBelow'),
					'Delete row': App.text('tableDeleteRow'),
				},
				embed: {
					'Enter a caption': App.text('caption'),
				},
			},
			blockTunes: {
				delete: {
					Delete: App.text('delete'),
					'Click to delete': App.text('clickToDelete'),
				},
				moveUp: {
					'Move up': App.text('moveUp'),
				},
				moveDown: {
					'Move down': App.text('moveDown'),
				},
			},
		};
	}

	/**
	 * Initialyze drag and drop.
	 */
	onRender(): void {
		window.requestIdleCallback(() => {
			new DragDrop(this);
		});
	}

	/**
	 * Clean up on diconnect.
	 */
	disconnectedCallback(): void {
		try {
			this.editor.destroy();
		} catch {}

		super.disconnectedCallback();
	}
}

customElements.define(EditorJSComponent.TAG_NAME, EditorJSComponent);
