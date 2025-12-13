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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 */

import { TunesMenuConfig } from '@/vendor/editorjs';
import { ModalComponent } from '@/admin/components/Modal/Modal';
import {
	App,
	Attr,
	create,
	createGenericModal,
	CSS,
	EventName,
	fire,
	getPageURL,
	html,
	query,
	requestAPI,
	resolveFileUrl,
	VideoCollectionController,
} from '@/admin/core';
import { VideoBlockData } from '@/admin/types';
import { BaseBlock } from './BaseBlock';

export class VideoBlock extends BaseBlock<VideoBlockData> {
	/**
	 * Sanitizer settings.
	 *
	 * @static
	 */
	static get sanitize() {
		return {
			url: true,
			autoplay: false,
			loop: false,
			muted: false,
			controls: false,
			caption: {},
		};
	}

	/**
	 * Paste configuration
	 */
	static get pasteConfig() {
		return {
			patterns: {
				image: new RegExp(
					`(https?:\\/\\/)?\\S+\\.(${App.fileTypesVideo.join('|')})$`,
					'i'
				),
			},
		};
	}

	/**
	 * Toolbox settings.
	 */
	static get toolbox() {
		return {
			title: App.text('videoTool'),
			icon: '<i class="bi bi-film"></i>',
		};
	}

	/**
	 * The video element;
	 */
	private video: HTMLVideoElement;

	/**
	 * The video element.
	 */
	private videoSource: HTMLSourceElement;

	/**
	 * The caption element.
	 */
	private caption: HTMLDivElement;

	/**
	 * Prepare block data.
	 *
	 * @param data
	 * @param data.url
	 * @param data.autoplay
	 * @param data.loop
	 * @param data.muted
	 * @param data.controls
	 * @return the video block data
	 */
	protected prepareData(data: VideoBlockData): VideoBlockData {
		return {
			url: data.url || '',
			autoplay: data.autoplay || false,
			loop: data.loop || false,
			muted: data.muted || false,
			controls: data.controls ?? true,
			caption: data.caption || '',
		};
	}

	/**
	 * Set the url property and update the preview.
	 *
	 * @param url
	 */
	private setVideo(url: string): void {
		this.data.url = url;
		this.videoSource.src = resolveFileUrl(url);
		this.videoSource.type = `video/${url.split('.').pop()}`;
		this.video.load();
	}

	/**
	 * Toggle a given video attribute.
	 */
	private toggleProp(prop: string, state: boolean): void {
		if (state) {
			this.video.setAttribute(prop, '');
		} else {
			this.video.removeAttribute(prop);
		}
	}

	/**
	 * Render the main block element.
	 *
	 * @return the rendered block
	 */
	render(): HTMLElement {
		this.video = create('video', [], { playsinline: '' });
		this.videoSource = create('source', [], {}, this.video);

		this.toggleProp('autoplay', this.data.autoplay);
		this.toggleProp('loop', this.data.loop);
		this.toggleProp('muted', this.data.muted);
		this.toggleProp('controls', this.data.controls);

		this.setVideo(this.data.url);

		this.wrapper.appendChild(this.video);
		this.wrapper.classList.add(CSS.editorBlockVideo);

		if (!this.readOnly) {
			const select = create(
				'button',
				[CSS.button, CSS.buttonIcon, CSS.formGroupItem],
				{ [Attr.tooltip]: App.text('selectVideo') },
				this.wrapper,
				'<i class="bi bi-film"></i>'
			);

			this.listen(select, 'click', this.pickVideo.bind(this));
		}

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

		return this.wrapper;
	}

	/**
	 * Create the tunes menu configuration.
	 *
	 * @return the tunes menu configuration
	 */
	renderSettings(): TunesMenuConfig {
		return [
			{
				icon: '<i class="bi bi-play-fill"></i>',
				label: App.text('videoAutoplay'),
				closeOnActivate: false,
				onActivate: () => {
					this.data.autoplay = !this.data.autoplay;
					this.toggleProp('autoplay', this.data.autoplay);
				},
				isActive: this.data.autoplay,
				toggle: 'autoplay',
			},

			{
				icon: '<i class="bi bi-repeat"></i>',
				label: App.text('videoLoop'),
				closeOnActivate: false,
				onActivate: () => {
					this.data.loop = !this.data.loop;
					this.toggleProp('loop', this.data.loop);
				},
				isActive: this.data.loop,
				toggle: 'loop',
			},
			{
				icon: '<i class="bi bi-volume-mute"></i>',
				label: App.text('videoMuted'),
				closeOnActivate: false,
				onActivate: () => {
					this.data.muted = !this.data.muted;
					this.toggleProp('muted', this.data.muted);
				},
				isActive: this.data.muted,
				toggle: 'muted',
			},
			{
				icon: '<i class="bi bi-pause-btn"></i>',
				label: App.text('videoControls'),
				closeOnActivate: false,
				onActivate: () => {
					this.data.controls = !this.data.controls;
					this.toggleProp('controls', this.data.controls);
				},
				isActive: this.data.controls,
				toggle: 'controls',
			},
		];
	}

	/**
	 * Called when block is added.
	 */
	appendCallback(): void {
		this.pickVideo();
	}

	/**
	 * Add image when pasting a valid image url.
	 */
	onPaste(event: CustomEvent) {
		if (event.type == 'pattern') {
			this.setVideo(event.detail.data);
		}
	}

	/**
	 * Create a single list with videos to pick.
	 *
	 * @param modal
	 * @param body
	 * @param url
	 * @async
	 */
	private async createVideoList(
		modal: ModalComponent,
		body: HTMLElement,
		url: string | null = null
	): Promise<void> {
		const label = App.text(url ? 'pageVideos' : 'sharedVideos');
		const baseUrl = url ? '' : '/shared/';
		const field = create('div', [CSS.field], {}, body);

		const render = async () => {
			const { data } = await requestAPI(VideoCollectionController.list, {
				url,
			});

			field.innerHTML = '';

			create('label', [CSS.fieldLabel], {}, field, label);

			if (!data.videos?.length) {
				create('span', [], {}, field, App.text('noVideosFound'));

				return;
			}

			const list = create('div', [CSS.editorBlockVideoList], {}, field);

			data.videos.forEach((item: { name: string; size: string }) => {
				const button = create(
					'span',
					[CSS.editorBlockVideoListItem],
					{},
					list,
					html`
						<am-icon-text
							${Attr.icon}="film"
							${Attr.text}="${item.name}"
						></am-icon-text>
						<small>(${item.size})</small>
					`
				);

				this.listen(button, 'click', () => {
					this.setVideo(`${baseUrl}${item.name}`);

					modal.close();
				});
			});
		};

		render();

		this.listen(window, EventName.filesChangeOnServer, render);
	}

	/**
	 * Pick a video.
	 *
	 * @async
	 */
	private async pickVideo(): Promise<void> {
		const { modal, body } = createGenericModal(App.text('selectVideo'));
		const pageUrl = getPageURL();

		body.innerHTML = html`
			<p>${App.text('linkVideo')}</p>
			<span class="${CSS.formGroup}">
				<input
					type="text"
					class="${CSS.input} ${CSS.formGroupItem}"
					value="${this.data.url}"
					placeholder="${App.text('url')}"
				/>
				<button class="${CSS.button} ${CSS.formGroupItem}">
					${App.text('ok')}
				</button>
			</span>
			<hr />
		`;

		const linkButton = query('button', body);
		const linkInput = query<HTMLInputElement>('input', body);
		const picker = create(
			'div',
			[],
			{},
			body,
			html`
				<p>${App.text('useUploadedVideo')}</p>
				<am-upload></am-upload>
			`
		);

		this.listen(linkButton, 'click', () => {
			this.setVideo(linkInput.value);
			modal.close();
		});

		if (pageUrl) {
			await this.createVideoList(modal, picker, pageUrl);
		}

		await this.createVideoList(modal, picker);

		modal.open();
	}

	/**
	 * Return the section block data.
	 *
	 * @return the saved data
	 */
	save(): VideoBlockData {
		this.data.caption = this.caption.innerHTML || '';

		return this.data;
	}
}
