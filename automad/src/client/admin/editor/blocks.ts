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

// @ts-ignore
import Embed from '@editorjs/embed';
import { KeyValueMap } from '../types';
import { ButtonsBlock } from './blocks/Buttons';
import { CodeBlock } from './blocks/Code';
import { ComponentBlock } from './blocks/Component';
import { Delimiter } from './blocks/Delimiter';
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
import { SectionBlock } from './blocks/Section';
import { SnippetBlock } from './blocks/Snippet';
import { TableBlock } from './blocks/Table';
import { TableOfContentsBlock } from './blocks/TableOfContents';
import { VideoBlock } from './blocks/Video';
import { embedServices } from './embedServices';

/**
 * The blocks used.
 *
 * @return an object with block configurations
 */
export const getBlockTools = (isComponentEditor: boolean): KeyValueMap => {
	let component: KeyValueMap = {
		component: {
			class: ComponentBlock,
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
		section: { class: SectionBlock },
		...component,
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
		video: {
			class: VideoBlock,
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
};
