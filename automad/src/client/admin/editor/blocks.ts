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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { KeyValueMap } from '../types';
import { ButtonsBlock } from './blocks/Buttons';
import { CalloutBlock } from './blocks/Callout';
import { CodeBlock } from './blocks/Code';
import { CollapsibleSectionBlock } from './blocks/CollapsibleSection';
import { ComponentBlock } from './blocks/Component';
import { Delimiter } from './blocks/Delimiter';
import { EmbedBlock } from './blocks/Embed';
import { FilelistBlock } from './blocks/Filelist';
import { GalleryBlock } from './blocks/Gallery';
import { HeaderBlock } from './blocks/Header';
import { ImageBlock } from './blocks/Image';
import { ImageSlideshowBlock } from './blocks/ImageSlideshow';
import { MailBlock } from './blocks/Mail';
import { NestedListBlock } from './blocks/NestedList';
import { PagelistBlock } from './blocks/Pagelist';
import { ParagraphBlock } from './blocks/Paragraph';
import { QuoteBlock } from './blocks/Quote';
import { RawBlock } from './blocks/Raw';
import { LayoutSectionBlock } from './blocks/LayoutSection';
import { SnippetBlock } from './blocks/Snippet';
import { TableBlock } from './blocks/Table';
import { TableOfContentsBlock } from './blocks/TableOfContents';
import { VideoBlock } from './blocks/Video';
import { embedServices } from './embedServices';
import { TeXBlock } from './blocks/TeX';

/**
 * The blocks used.
 *
 * @return an object with block configurations
 */
export const getBlockTools = (isComponentEditor: boolean): KeyValueMap => {
	let component: KeyValueMap = {
		component: {
			class: ComponentBlock,
			stretchable: true,
		},
	};

	if (isComponentEditor) {
		component = {};
	}

	return {
		paragraph: {
			class: ParagraphBlock,
			inlineToolbar: true,
		},
		header: {
			class: HeaderBlock,
			inlineToolbar: true,
		},
		layoutSection: { class: LayoutSectionBlock, stretchable: true },
		collapsibleSection: {
			class: CollapsibleSectionBlock,
			stretchable: true,
			inlineToolbar: [
				'bold',
				'italic',
				'codeInline',
				'underline',
				'strikeThrough',
				'color',
			],
		},
		...component,
		nestedList: {
			class: NestedListBlock,
			inlineToolbar: true,
		},
		table: {
			class: TableBlock,
			inlineToolbar: true,
			stretchable: true,
		},
		callout: {
			class: CalloutBlock,
			inlineToolbar: true,
		},
		quote: {
			class: QuoteBlock,
			inlineToolbar: true,
		},
		delimiter: {
			class: Delimiter,
			stretchable: true,
		},
		image: {
			class: ImageBlock,
			inlineToolbar: true,
			stretchable: true,
		},
		video: {
			class: VideoBlock,
			inlineToolbar: true,
			stretchable: true,
		},
		gallery: {
			class: GalleryBlock,
			inlineToolbar: false,
			stretchable: true,
		},
		imageSlideshow: {
			class: ImageSlideshowBlock,
			inlineToolbar: false,
			stretchable: true,
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
			stretchable: true,
		},
		teX: {
			class: TeXBlock,
			stretchable: true,
		},
		raw: {
			class: RawBlock,
			inlineToolbar: false,
			stretchable: true,
		},
		mail: {
			class: MailBlock,
			inlineToolbar: true,
		},
		pagelist: {
			class: PagelistBlock,
			stretchable: true,
		},
		filelist: {
			class: FilelistBlock,
			stretchable: true,
		},
		snippet: {
			class: SnippetBlock,
			tunes: [],
		},
		embed: {
			class: EmbedBlock,
			inlineToolbar: true,
			config: { services: embedServices },
			stretchable: true,
		},
	};
};
