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

import { BaseComponent } from '@/components/Base';
import { SectionBlock } from '@/editor/blocks/Section';
import { BoldInline } from '@/editor/inline/Bold';
import { CodeInline } from '@/editor/inline/Code';
import { ColorInline } from '@/editor/inline/Color';
import { FontSizeInline } from '@/editor/inline/FontSize';
import { ItalicInline } from '@/editor/inline/Italic';
import { LineHeightInline } from '@/editor/inline/LineHeight';
import { LinkInline } from '@/editor/inline/Link';
import { StrikeThroughInline } from '@/editor/inline/StrikeThrough';
import { UnderlineInline } from '@/editor/inline/Underline';
import { DragDrop } from '@/editor/plugins/DragDrop';
import { LayoutTune } from '@/editor/tunes/Layout';
import { EditorOutputData, KeyValueMap } from '@/types';
import EditorJS, { EditorConfig, I18nDictionary } from 'automad-editorjs';
import { App, CSS } from '@/core';
import { Delimiter } from '@/editor/blocks/Delimiter';
import { ImageBlock } from '@/editor/blocks/Image';
import { NestedListBlock } from '@/editor/blocks/NestedList';
import { QuoteBlock } from '@/editor/blocks/Quote';
import { TableBlock } from '@/editor/blocks/Table';
import {
	TextAlignCenterInline,
	TextAlignLeftInline,
	TextAlignRightInline,
} from '@/editor/inline/TextAlign';
import { ClassTune } from '@/editor/tunes/Class';
import { IdTune } from '@/editor/tunes/Id';
import { SpacingTune } from '@/editor/tunes/Spacing';
import { CodeBlock } from '@/editor/blocks/Code';
import { RawBlock } from '@/editor/blocks/Raw';
import { GalleryBlock } from '@/editor/blocks/Gallery';
import { ImageSlideshowBlock } from '@/editor/blocks/ImageSlideshow';
import { ButtonsBlock } from '@/editor/blocks/Buttons';
// @ts-ignore
import Embed from '@editorjs/embed';
import { embedServices } from '@/editor/embedServices';
import { HeaderBlock } from '@/editor/blocks/Header';
import { ParagraphBlock } from '@/editor/blocks/Paragraph';
import { DuplicateTune } from '@/editor/tunes/Duplicate';
import { MailBlock } from '@/editor/blocks/Mail';
import { TableOfContentsBlock } from '@/editor/blocks/TableOfContents';
import { PagelistBlock } from '@/editor/blocks/Pagelist';
import { FilelistBlock } from '@/editor/blocks/Filelist';
import { SnippetBlock } from '@/editor/blocks/Snippet';

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
		return ['layout', 'spacing', 'className', 'id', 'duplicate'];
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
		this.classList.add(CSS.contents);

		this.editor = new EditorJS(
			Object.assign(
				{
					data,
					holder: this,
					logLevel: 'ERROR',
					minHeight: 50,
					autofocus: false,
					placeholder: isSectionBlock
						? ''
						: App.text('editorPlaceholder'),
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
					i18n: {
						messages: this.getI18nDictionary(),
					},
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
				class: ParagraphBlock,
				inlineToolbar: true,
			},
			header: {
				class: HeaderBlock,
				inlineToolbar: true,
			},
			section: { class: SectionBlock },
			nestedList: {
				class: NestedListBlock,
				inlineToolbar: true,
			},
			table: {
				class: TableBlock,
				inlineToolbar: true,
			},
			quote: {
				class: QuoteBlock,
				inlineToolbar: true,
			},
			delimiter: Delimiter,
			image: {
				class: ImageBlock,
				inlineToolbar: true,
			},
			gallery: {
				class: GalleryBlock,
				inlineToolbar: false,
			},
			imageSlideshow: {
				class: ImageSlideshowBlock,
				inlineToolbar: false,
			},
			buttons: {
				class: ButtonsBlock,
				inlineToolbar: true,
			},
			tableOfContents: {
				class: TableOfContentsBlock,
			},
			code: {
				class: CodeBlock,
				inlineToolbar: false,
			},
			raw: {
				class: RawBlock,
				inlineToolbar: false,
			},
			mail: {
				class: MailBlock,
				inlineToolbar: true,
			},
			pagelist: {
				class: PagelistBlock,
			},
			filelist: {
				class: FilelistBlock,
			},
			snippet: {
				class: SnippetBlock,
				tunes: [],
			},
			embed: {
				class: Embed,
				inlineToolbar: true,
				config: { services: embedServices },
			},
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
			duplicate: {
				class: DuplicateTune,
			},
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
		new DragDrop(this);
	}
}

customElements.define(EditorJSComponent.TAG_NAME, EditorJSComponent);
