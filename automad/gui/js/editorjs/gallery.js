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
			layout: data.layout || 'Masonry'
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

		return {
			globs: this.inputs.globs.innerHTML,
			width: parseInt(this.inputs.width.innerHTML),
			layout: this.inputs.layoutHidden.innerHTML
		};

	}

}