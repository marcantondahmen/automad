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


class AutomadGallery {

	constructor({data, api}) {

		var create = Automad.util.create;

		this.api = api;

		this.data = {
			globs: data.globs || '*.jpg, *.png, *.gif',
			width: data.width || 250,
			stretched: data.stretched !== undefined ? data.stretched : true,
			masonry: data.masonry !== undefined ? data.masonry : true
		};

		this.inputs = {
			globs: create.editable(['cdx-input'], 'Enter one or more glob patterns', this.data.globs),
			width: create.editable(['cdx-input'], 'Image width in px', this.data.width)
		};
		
		var icon = document.createElement('div');
		
		icon.innerHTML = AutomadGallery.toolbox.icon;
		icon.classList.add('am-block-icon');
	
		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.appendChild(icon);
		this.wrapper.appendChild(create.label('Pattern'));
		this.wrapper.appendChild(this.inputs.globs);
		this.wrapper.appendChild(create.label('Image Width'));
		this.wrapper.appendChild(this.inputs.width);

		this.settings = [
			{
				name: 'stretched',
				icon: '<svg width="17" height="10" viewBox="0 0 17 10" xmlns="http://www.w3.org/2000/svg"><path d="M13.568 5.925H4.056l1.703 1.703a1.125 1.125 0 0 1-1.59 1.591L.962 6.014A1.069 1.069 0 0 1 .588 4.26L4.38.469a1.069 1.069 0 0 1 1.512 1.511L4.084 3.787h9.606l-1.85-1.85a1.069 1.069 0 1 1 1.512-1.51l3.792 3.791a1.069 1.069 0 0 1-.475 1.788L13.514 9.16a1.125 1.125 0 0 1-1.59-1.591l1.644-1.644z"/></svg>'
			},
			{
				name: 'masonry',
				icon: '<svg xmlns="http://www.w3.org/2000/svg" width="19px" height="16px" viewBox="0 0 19 16"><path d="M5,4c0,0.552-0.448,1-1,1H1C0.448,5,0,4.552,0,4V1c0-0.552,0.448-1,1-1h3c0.552,0,1,0.448,1,1V4z"/><path d="M12,6c0,0.553-0.447,1-1,1H8C7.448,7,7,6.553,7,6V1c0-0.552,0.448-1,1-1h3c0.553,0,1,0.448,1,1V6z"/><path d="M19,3c0,0.552-0.447,1-1,1h-3c-0.553,0-1-0.448-1-1V1c0-0.552,0.447-1,1-1h3c0.553,0,1,0.448,1,1V3z"/><path d="M12,15c0,0.553-0.447,1-1,1H8c-0.552,0-1-0.447-1-1v-5c0-0.553,0.448-1,1-1h3c0.553,0,1,0.447,1,1V15z"/><path d="M5,12c0,0.553-0.448,1-1,1H1c-0.552,0-1-0.447-1-1V8c0-0.553,0.448-1,1-1h3c0.552,0,1,0.447,1,1V12z"/><path d="M19,13c0,0.553-0.447,1-1,1h-3c-0.553,0-1-0.447-1-1V7c0-0.553,0.447-1,1-1h3c0.553,0,1,0.447,1,1V13z"/></svg>'
			}
		];

		Promise.resolve().then(() => {
			this.api.blocks.stretchBlock(this.api.blocks.getCurrentBlockIndex(), this.data.stretched);
		});

	}

	static get toolbox() {

		return {
			title: 'Gallery',
			icon: '<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="15px" viewBox="0 0 18 15"><path d="M14,0H4C1.791,0,0,1.791,0,4v7c0,2.209,1.791,4,4,4h10c2.209,0,4-1.791,4-4V4C18,1.791,16.209,0,14,0z M4,2h4v6H2V4 C2,2.897,2.897,2,4,2z M4,13c-1.103,0-2-0.897-2-2v-1h6v3H4z M16,11c0,1.103-0.897,2-2,2h-4V7h6V11z M16,5h-6V2h4 c1.103,0,2,0.897,2,2V5z"/></svg>'
		};

	}

	render() {

		return this.wrapper;

	}

	save() {

		return Object.assign(this.data, {
			globs: this.inputs.globs.innerHTML,
			width: parseInt(this.inputs.width.innerHTML)
		});

	}

	renderSettings() {

		var wrapper = document.createElement('div'),
			block = this;

		wrapper.classList.add('cdx-settings-1-2');

		this.settings.forEach(function (tune) {

			var button = document.createElement('div');

			button.classList.add('cdx-settings-button');
			button.classList.toggle('cdx-settings-button--active', block.data[tune.name]);
			button.innerHTML = tune.icon;
			wrapper.appendChild(button);

			button.addEventListener('click', function () {
				block.toggleTune(tune.name);
				button.classList.toggle('cdx-settings-button--active');
			});

		});

		return wrapper;

	}

	toggleTune(tune) {

		this.data[tune] = !this.data[tune];

		if (tune == 'stretched') {
			this.api.blocks.stretchBlock(this.api.blocks.getCurrentBlockIndex(), this.data.stretched);
		}

	}

}