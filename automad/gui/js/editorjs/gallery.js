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

	constructor({data}) {

		var create = Automad.util.create,
			gallery = this;

		this.data = {
			globs: data.globs || '*.jpg, *.png, *.gif',
			width: data.width || 200,
			layout: data.layout || 'Masonry',
			stretched: data.stretched !== undefined ? data.stretched : false
		};

		this.inputs = {
			globs: create.editable(['cdx-input'], 'Enter one or more glob patterns', this.data.globs),
			width: create.editable(['cdx-input'], 'Image width in px', this.data.width),
			layoutSelect: create.select(['cdx-input', 'uk-button-success'], ['Masonry', 'Grid'], this.data.layout),
			layoutHidden: create.editable(['uk-hidden'], '', this.data.layout)
		};

		this.inputs.layoutSelect.addEventListener('change', function() {
			gallery.inputs.layoutHidden.innerHTML = gallery.inputs.layoutSelect.value;			
		});
		
		var icon = document.createElement('div'),
			controls = document.createElement('ul'),
			width = document.createElement('li'),
			layout = document.createElement('li');

		icon.innerHTML = AutomadGallery.toolbox.icon;
		icon.classList.add('am-block-icon');
		controls.classList.add('uk-grid', 'uk-grid-width-medium-1-2');
		width.appendChild(create.label('Image Width'));
		width.appendChild(this.inputs.width);
		layout.appendChild(create.label('Layout'));
		layout.appendChild(this.inputs.layoutSelect);
		controls.appendChild(width);
		controls.appendChild(layout);

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.appendChild(icon);
		this.wrapper.appendChild(create.label('Pattern'));
		this.wrapper.appendChild(this.inputs.globs);
		this.wrapper.appendChild(controls);
		this.wrapper.appendChild(this.inputs.layoutHidden);

		this.settings = [{
			name: 'stretched',
			icon: '<svg width="17" height="10" viewBox="0 0 17 10" xmlns="http://www.w3.org/2000/svg"><path d="M13.568 5.925H4.056l1.703 1.703a1.125 1.125 0 0 1-1.59 1.591L.962 6.014A1.069 1.069 0 0 1 .588 4.26L4.38.469a1.069 1.069 0 0 1 1.512 1.511L4.084 3.787h9.606l-1.85-1.85a1.069 1.069 0 1 1 1.512-1.51l3.792 3.791a1.069 1.069 0 0 1-.475 1.788L13.514 9.16a1.125 1.125 0 0 1-1.59-1.591l1.644-1.644z"/></svg>'
		}];

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
			width: parseInt(this.inputs.width.innerHTML),
			layout: this.inputs.layoutHidden.innerHTML
		});

	}

	renderSettings() {

		var wrapper = document.createElement('div'),
			block = this;

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
		Automad.util.triggerBlockChange(this.wrapper);

	}

}