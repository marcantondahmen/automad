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

import EditorJS, { EditorConfig } from '@editorjs/editorjs';
import { EditorOutputData } from '@/types';
import { BaseComponent } from '@/components/Base';

// @ts-ignore
import Header from '@editorjs/header';
import { LayoutTune } from '@/editor/tunes/Layout';
import { SectionBlock } from '@/editor/blocks/Section';
import { DragDrop } from '@/editor/plugins/DragDrop';
import { LinkInline } from '@/editor/inline/Link';
import { BoldInline } from '@/editor/inline/Bold';
import { ItalicInline } from '@/editor/inline/Italic';
import { CodeInline } from '@/editor/inline/Code';
import { UnderlineInline } from '@/editor/inline/Underline';
import { ColorInline } from '@/editor/inline/Color';
import { StrikeThroughInline } from '@/editor/inline/StrikeThrough';
import { FontSizeInline } from '@/editor/inline/FontSize';
import { LineHeightInline } from '@/editor/inline/LineHeight';

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
	 * Create an EditorJS instance and bind it to the editor property.
	 *
	 * @param data
	 * @param config
	 * @param isSectionBlock
	 */
	init(
		data: EditorOutputData,
		config: EditorConfig,
		isSectionBlock: boolean
	): void {
		this.style.position = 'relative';

		this.editor = new EditorJS(
			Object.assign(
				{
					data,
					holder: this,
					logLevel: 'ERROR',
					minHeight: 50,
					autofocus: false,
					tools: {
						layout: {
							class: LayoutTune,
							config: {
								isSectionBlock,
							},
						},
						header: { class: Header, inlineToolbar: true },
						section: { class: SectionBlock },
						bold: { class: BoldInline },
						italic: { class: ItalicInline },
						link: { class: LinkInline },
						codeInline: { class: CodeInline },
						underline: { class: UnderlineInline },
						strikeThrough: { class: StrikeThroughInline },
						color: { class: ColorInline },
						fontSize: { class: FontSizeInline },
						lineHeight: { class: LineHeightInline },
					},
					tunes: ['layout'],
					inlineToolbar: [
						'bold',
						'italic',
						'link',
						'codeInline',
						'underline',
						'strikeThrough',
						'color',
						'fontSize',
						'lineHeight',
					],
					onReady: (): void => {
						this.onRender(data);
					},
				},
				config
			)
		);
	}

	/**
	 * Apply layout to rendered blocks and initialyze drag and drop.
	 *
	 * @param data
	 */
	onRender(data: EditorOutputData): void {
		data.blocks?.forEach((_block) => {
			const block = this.editor.blocks.getById(_block.id);
			const layout = _block.tunes?.layout;

			LayoutTune.apply(block, layout);
		});

		new DragDrop(this);
	}
}

customElements.define(EditorJSComponent.TAG_NAME, EditorJSComponent);
