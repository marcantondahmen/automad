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

// @ts-ignore
import Header from '@editorjs/header';
// @ts-ignore
import Paragraph from '@editorjs/paragraph';
import { ClassTune } from '@/editor/tunes/Class';
import { IdTune } from '@/editor/tunes/Id';
import { SpacingTune } from '@/editor/tunes/Spacing';
import { LargeTune } from '@/editor/tunes/Large';
import {
	TextAlignCenterInline,
	TextAlignLeftInline,
	TextAlignRightInline,
} from '@/editor/inline/TextAlign';

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
						'alignLeft',
						'alignCenter',
						'alignRight',
						'bold',
						'italic',
						'fontSize',
						'lineHeight',
						'link',
						'codeInline',
						'underline',
						'strikeThrough',
						'color',
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
				tunes: ['large', ...this.baseTunes],
			},
			header: {
				class: Header,
				inlineToolbar: true,
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
			alignLeft: { class: TextAlignLeftInline },
			alignCenter: { class: TextAlignCenterInline },
			alignRight: { class: TextAlignRightInline },
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
			large: {
				class: LargeTune,
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
