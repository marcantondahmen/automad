/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


class AutomadImage {

	constructor({data, api}) {

		this.api = api;

		this.data = {
			url: data.url || '',
			caption: data.caption || '',
			stretched: data.stretched !== undefined ? data.stretched : false
		};
		
		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('cdx-block');
		this.img = document.createElement('img');
		this.caption = Automad.util.create.editable(['cdx-input'], 'Enter a caption', this.data.caption);

		this.wrapper.appendChild(this.img);
		this.wrapper.appendChild(this.caption);

		this.button = document.createElement('div');
		this.button.innerHTML = '<i class="uk-icon-image uk-icon-small"></i>&nbsp;&nbsp;Select Image';
		this.button.classList.add('uk-panel', 'uk-panel-box', 'uk-text-muted', 'uk-text-center');

		var block = this;

		this.button.addEventListener('click', function () {
			block.select();
		});

		this.settings = this.renderStretchSetting();

	}

	static get toolbox() {

		return {
			title: 'Image',
			icon: '<svg width="18px" height="15px" viewBox="0 0 18 15"><path d="M18,4c0-2.209-1.791-4-4-4H4C1.791,0,0,1.791,0,4v7c0,2.209,1.791,4,4,4h10c2.209,0,4-1.791,4-4V4z M4,2h10 c1.103,0,2,0.897,2,2v3.636l-1.279-1.33C14.534,6.113,14.278,6.002,14.01,6c-0.287-0.012-0.527,0.103-0.717,0.293l-2.302,2.302 L6.573,4.284c-0.389-0.379-1.008-0.379-1.396,0L2,7.383V4C2,2.897,2.897,2,4,2z M14,13H4c-1.103,0-2-0.897-2-2v-0.822l3.875-3.781 l4.427,4.319C10.496,10.905,10.748,11,11,11c0.256,0,0.512-0.098,0.707-0.293l2.279-2.279L16,10.521V11C16,12.103,15.103,13,14,13z" /></svg>'
		};

	}

	static get pasteConfig() {

		return {
			patterns: {
				image: /(https?:\/\/)?\S+\.(gif|jpe?g|tiff|png)$/i
			}
		}

	}

	insertImage(url) {

		if (url) {
			this.img.src = Automad.util.resolvePath(url);
			this.data.url = url;
			this.button.parentNode.replaceChild(this.wrapper, this.button);
		}

	}

	select() {

		var block = this;

		Automad.selectImage.dialog(false, true, function(url) {
			block.insertImage(url);
		});

	}

	appendCallback() {

		this.select();

	}

	render() {

		if (this.data && this.data.url) {
			this.img.src = Automad.util.resolvePath(this.data.url);
			return this.wrapper;
		} else {
			return this.button;
		}

	}

	save() {

		return Object.assign(this.data, {
			url: this.data.url,
			caption: this.caption.innerHTML
		});

	}

	onPaste(event) {

		if (event.type == 'pattern') {
			this.insertImage(event.detail.data);
		}

	}

	renderSettings() {

		return this.settings;

	}

	renderStretchSetting() {

		var block = this,
			wrapper = document.createElement('div'),
			button = document.createElement('div'),
			icon = '<svg width="17" height="10" viewBox="0 0 17 10"><path d="M13.568 5.925H4.056l1.703 1.703a1.125 1.125 0 0 1-1.59 1.591L.962 6.014A1.069 1.069 0 0 1 .588 4.26L4.38.469a1.069 1.069 0 0 1 1.512 1.511L4.084 3.787h9.606l-1.85-1.85a1.069 1.069 0 1 1 1.512-1.51l3.792 3.791a1.069 1.069 0 0 1-.475 1.788L13.514 9.16a1.125 1.125 0 0 1-1.59-1.591l1.644-1.644z"/></svg>',
			title = 'Full Width',
			toggleStretch = function () {
				block.data.stretched = !block.data.stretched;
				block.api.blocks.stretchBlock(block.api.blocks.getCurrentBlockIndex(), block.data.stretched);
			};

		wrapper.classList.add('cdx-settings-1-1');
		button.classList.add(block.api.styles.settingsButton);
		button.classList.toggle(block.api.styles.settingsButtonActive, block.data.stretched);
		button.innerHTML = icon;
		wrapper.appendChild(button);

		button.addEventListener('click', function () {
			toggleStretch();
			button.classList.toggle(block.api.styles.settingsButtonActive);
		});

		Promise.resolve().then(() => {
			block.api.blocks.stretchBlock(block.api.blocks.getCurrentBlockIndex(), block.data.stretched);
		});

		block.api.tooltip.onHover(button, title, { placement: 'top' });

		return wrapper;

	}

}