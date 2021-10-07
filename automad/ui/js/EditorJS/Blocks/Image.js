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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

class AutomadBlockImage {
	static get isReadOnlySupported() {
		return true;
	}

	static get pasteConfig() {
		return {
			patterns: {
				image: /(https?:\/\/)?\S+\.(gif|jpe?g|tiff|png)$/i,
			},
		};
	}

	static get sanitize() {
		return {
			caption: {},
			link: false,
		};
	}

	static get toolbox() {
		return {
			title: AutomadEditorTranslation.get('image_toolbox'),
			icon: '<svg width="18px" height="15px" viewBox="0 0 18 15"><path d="M18,4c0-2.209-1.791-4-4-4H4C1.791,0,0,1.791,0,4v7c0,2.209,1.791,4,4,4h10c2.209,0,4-1.791,4-4V4z M4,2h10 c1.103,0,2,0.897,2,2v3.636l-1.279-1.33C14.534,6.113,14.278,6.002,14.01,6c-0.287-0.012-0.527,0.103-0.717,0.293l-2.302,2.302 L6.573,4.284c-0.389-0.379-1.008-0.379-1.396,0L2,7.383V4C2,2.897,2.897,2,4,2z M14,13H4c-1.103,0-2-0.897-2-2v-0.822l3.875-3.781 l4.427,4.319C10.496,10.905,10.748,11,11,11c0.256,0,0.512-0.098,0.707-0.293l2.279-2.279L16,10.521V11C16,12.103,15.103,13,14,13z" /></svg>',
		};
	}

	constructor({ data, api }) {
		this.api = api;

		this.data = {
			url: data.url || '',
			caption: data.caption || '',
			link: data.link || '',
		};

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('cdx-block');
		this.img = document.createElement('img');
		this.caption = Automad.Util.create.editable(
			['cdx-input'],
			AutomadEditorTranslation.get('image_caption'),
			this.data.caption
		);

		const linkWrapper = document.createElement('div');
		linkWrapper.classList.add('am-form-icon-button-input', 'uk-flex');

		const linkButton = document.createElement('button');
		linkButton.type = 'button';
		linkButton.classList.add('uk-button', 'uk-button-large');
		linkButton.innerHTML = '<i class="uk-icon-link"></i>';

		api.listeners.on(linkButton, 'click', () => {
			Automad.Link.click(linkButton);
		});

		this.link = document.createElement('input');
		this.link.type = 'text';
		this.link.placeholder = AutomadEditorTranslation.get('image_link');
		this.link.value = this.data.link;
		this.link.classList.add(
			'am-block-link',
			'uk-form-controls',
			'uk-width-1-1'
		);

		linkWrapper.appendChild(linkButton);
		linkWrapper.appendChild(this.link);

		this.wrapper.appendChild(this.img);
		this.wrapper.appendChild(this.caption);
		this.wrapper.appendChild(linkWrapper);

		this.button = document.createElement('div');
		this.button.innerHTML =
			'<i class="uk-icon-image uk-icon-small"></i>&nbsp;&nbsp;Select Image';
		this.button.classList.add(
			'uk-panel',
			'uk-panel-box',
			'uk-text-muted',
			'uk-text-center'
		);

		this.button.addEventListener('click', () => {
			this.select();
		});
	}

	insertImage(url) {
		if (url) {
			this.img.src = Automad.Util.resolvePath(url);
			this.data.url = url;
			this.button.parentNode.replaceChild(this.wrapper, this.button);
		}
	}

	select() {
		var block = this;

		Automad.SelectImage.dialog(false, true, function (url) {
			block.insertImage(url);
		});
	}

	appendCallback() {
		this.select();
	}

	render() {
		if (this.data && this.data.url) {
			this.img.src = Automad.Util.resolvePath(this.data.url);
			return this.wrapper;
		} else {
			return this.button;
		}
	}

	save() {
		return Object.assign(this.data, {
			url: this.data.url,
			caption: this.caption.innerHTML,
			link: this.link.value,
		});
	}

	onPaste(event) {
		if (event.type == 'pattern') {
			this.insertImage(event.detail.data);
		}
	}
}
