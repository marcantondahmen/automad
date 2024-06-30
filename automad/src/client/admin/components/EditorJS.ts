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
 * Copyright (c) 2023-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { BaseComponent } from '@/admin/components/Base';
import { SectionBlock } from '@/admin/editor/blocks/Section';
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
import { LayoutTune } from '@/admin/editor/tunes/Layout';
import { EditorOutputData, KeyValueMap } from '@/admin/types';
import EditorJS, { EditorConfig, I18nDictionary } from 'automad-editorjs';
import { App, CSS } from '@/admin/core';
import { Delimiter } from '@/admin/editor/blocks/Delimiter';
import { ImageBlock } from '@/admin/editor/blocks/Image';
import { NestedListBlock } from '@/admin/editor/blocks/NestedList';
import { QuoteBlock } from '@/admin/editor/blocks/Quote';
import { TableBlock } from '@/admin/editor/blocks/Table';
import {
	TextAlignCenterInline,
	TextAlignLeftInline,
	TextAlignRightInline,
} from '@/admin/editor/inline/TextAlign';
import { ClassTune } from '@/admin/editor/tunes/Class';
import { IdTune } from '@/admin/editor/tunes/Id';
import { SpacingTune } from '@/admin/editor/tunes/Spacing';
import { CodeBlock } from '@/admin/editor/blocks/Code';
import { RawBlock } from '@/admin/editor/blocks/Raw';
import { GalleryBlock } from '@/admin/editor/blocks/Gallery';
import { ImageSlideshowBlock } from '@/admin/editor/blocks/ImageSlideshow';
import { ButtonsBlock } from '@/admin/editor/blocks/Buttons';
// @ts-ignore
import Embed from '@editorjs/embed';
import { embedServices } from '@/admin/editor/embedServices';
import { HeaderBlock } from '@/admin/editor/blocks/Header';
import { ParagraphBlock } from '@/admin/editor/blocks/Paragraph';
import { DuplicateTune } from '@/admin/editor/tunes/Duplicate';
import { MailBlock } from '@/admin/editor/blocks/Mail';
import { TableOfContentsBlock } from '@/admin/editor/blocks/TableOfContents';
import { PagelistBlock } from '@/admin/editor/blocks/Pagelist';
import { FilelistBlock } from '@/admin/editor/blocks/Filelist';
import { SnippetBlock } from '@/admin/editor/blocks/Snippet';

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
		window.requestIdleCallback(() => {
			new DragDrop(this);
		});
	}
}

customElements.define(EditorJSComponent.TAG_NAME, EditorJSComponent);
