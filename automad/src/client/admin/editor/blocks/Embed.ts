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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

import {
	App,
	create,
	CSS,
	EmbedController,
	fire,
	html,
	KeyValueMap,
	query,
	requestApi,
} from '@/admin/core';
import { BaseBlock } from './BaseBlock';

interface EmbedBlockData {
	source: string;
	caption: string;
}

/**
 * Handle script tags in injected HTML.
 *
 * @param embed
 */
const handleScript = (embed: HTMLElement): void => {
	const script = query('script', embed);

	if (script) {
		const attributes: KeyValueMap = {};

		for (const attribute of script.attributes) {
			attributes[attribute.name] = attribute.value;
		}

		script.replaceWith(create('script', [], { ...attributes }));
	}
};

export class EmbedBlock extends BaseBlock<EmbedBlockData> {
	/**
	 * The sanitizer config.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			source: true,
			caption: {},
		};
	}

	/**
	 * Paste configuration.
	 */
	static get pasteConfig() {
		const excludedExtensions = [
			...App.fileTypesImage,
			...App.fileTypesVideo,
		].join('|');

		return {
			patterns: {
				url: new RegExp(
					`^https?:\\/\\/\\S+(?<!\\.(${excludedExtensions}))$`,
					'i'
				),
			},
		};
	}

	/**
	 * The caption element.
	 */
	private caption: HTMLDivElement;

	/**
	 * Prepare block data.
	 *
	 * @param data
	 * @param data.source
	 * @param data.caption
	 * @return the embed block data
	 */
	protected prepareData(data: EmbedBlockData): EmbedBlockData {
		return {
			source: data.source || '',
			caption: data.caption || '',
		};
	}

	/**
	 * Render the main block element.
	 *
	 * @return the rendered block
	 */
	render(): HTMLElement {
		this.wrapper.classList.add(CSS.editorBlockEmbed);

		if (this.data.source) {
			this.resolve(this.data.source);
		}

		return this.wrapper;
	}

	/**
	 * Resolve the pasted url.
	 */
	onPaste(event: CustomEvent) {
		if (event.type == 'pattern') {
			this.resolvePastedSource(event.detail.data);
		}
	}

	/**
	 * Resolve a freshly pasted source url; fall back to a plain paragraph
	 * containing the url when it doesn't resolve to a supported service.
	 *
	 * @param source
	 * @async
	 */
	private async resolvePastedSource(source: string): Promise<void> {
		this.data.source = source;

		if (await this.resolve(source)) {
			return;
		}

		const index = this.api.blocks.getBlockIndex(this.blockApi.id);

		this.api.blocks.insert(
			'paragraph',
			{ text: source },
			undefined,
			index,
			true,
			true
		);
	}

	/**
	 * Resolve a source url into an embeddable url via EmbedController and render the preview.
	 *
	 * @param source
	 * @return whether the source resolved to a supported service
	 * @async
	 */
	private async resolve(source: string): Promise<boolean> {
		this.wrapper.innerHTML = '<am-spinner></am-spinner>';

		const { data } = await requestApi(EmbedController.data, { source });

		this.wrapper.innerHTML = '';

		if (!data?.service) {
			return false;
		}

		handleScript(create('div', [], {}, this.wrapper, data.html));

		this.caption = create(
			'div',
			['cdx-block', 'ce-paragraph'],
			{
				contenteditable: this.readOnly ? 'false' : 'true',
				placeholder: App.text('caption'),
			},
			this.wrapper,
			html`${this.data.caption}`
		);

		this.listen(this.caption, 'input', () => {
			fire('change', this.caption);
		});

		return true;
	}

	/**
	 * Return the block data.
	 *
	 * @return the saved data
	 */
	getData(): EmbedBlockData {
		this.data.caption = this.caption.innerHTML || '';

		return this.data;
	}
}
