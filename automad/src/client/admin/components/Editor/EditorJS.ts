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
import { EditorOutputData, KeyValueMap } from '@/types';
import { BaseComponent } from '@/components/Base';
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
import { TextAlignTune } from '@/editor/tunes/TextAlign';

// @ts-ignore
import Header from '@editorjs/header';
// @ts-ignore
import Paragraph from '@editorjs/paragraph';
import { ClassTune } from '@/editor/tunes/Class';
import { IdTune } from '@/editor/tunes/Id';
import { SpacingTune } from '@/editor/tunes/Spacing';

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
	 * The base selection of tunes that is used for all blocks.
	 */
	private get baseTunes() {
		return ['layout', 'spacing', 'className', 'id'];
	}

	/**
	 * The selection of tunes that is used for text based blocks.
	 */
	private get textTunes() {
		return ['textAlign', ...this.baseTunes];
	}

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
						...this.getBlockTools(),
						...this.getBlockTunes(isSectionBlock),
						...this.getInlineTools(),
					},
					tunes: this.baseTunes,
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
						this.onRender();
					},
				},
				config
			)
		);
	}

	/**
	 * The blocks used.
	 *
	 * @return an object with block configurations
	 */
	private getBlockTools(): KeyValueMap {
		return {
			paragraph: {
				class: Paragraph,
				inlineToolbar: true,
				tunes: this.textTunes,
			},
			header: {
				class: Header,
				inlineToolbar: true,
				tunes: this.textTunes,
			},
			section: { class: SectionBlock },
		};
	}

	/**
	 * The inline tools used.
	 *
	 * @return an object with tool configurations
	 */
	private getInlineTools(): KeyValueMap {
		return {
			bold: { class: BoldInline },
			italic: { class: ItalicInline },
			link: { class: LinkInline },
			codeInline: { class: CodeInline },
			underline: { class: UnderlineInline },
			strikeThrough: { class: StrikeThroughInline },
			color: { class: ColorInline },
			fontSize: { class: FontSizeInline },
			lineHeight: { class: LineHeightInline },
		};
	}

	/**
	 * The block tunes used.
	 *
	 * @param isSectionBlock
	 * @return an object with tune configurations
	 */
	private getBlockTunes(isSectionBlock: boolean): KeyValueMap {
		return {
			spacing: { class: SpacingTune },
			className: { class: ClassTune },
			id: { class: IdTune },
			layout: {
				class: LayoutTune,
				config: {
					isSectionBlock,
				},
			},
			textAlign: {
				class: TextAlignTune,
			},
		};
	}

	/**
	 * Apply layout to rendered blocks and initialyze drag and drop.
	 */
	onRender(): void {
		new DragDrop(this);
	}
}

customElements.define(EditorJSComponent.TAG_NAME, EditorJSComponent);
