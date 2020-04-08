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


class AutomadSlider {

	constructor({data}) {

		var create = Automad.util.create;

		this.data = {
			globs: data.globs || '*.jpg, *.png, *.gif',
			width: data.width || 800,
			height: data.height || 500,
			stretched: data.stretched !== undefined ? data.stretched : false
		};

		this.inputs = {
			globs: create.editable(['cdx-input'], 'Enter one or more glob patterns', this.data.globs),
			width: create.editable(['cdx-input'], 'Image width in px', this.data.width),
			height: create.editable(['cdx-input'], 'Image height in px', this.data.height)
		};

		var icon = document.createElement('div'),
			controls = document.createElement('ul'),
			width = document.createElement('li'),
			height = document.createElement('li');

		icon.innerHTML = AutomadSlider.toolbox.icon;
		icon.classList.add('am-block-icon');
		controls.classList.add('uk-grid', 'uk-grid-width-medium-1-2');
		width.appendChild(create.label('Image Width'));
		width.appendChild(this.inputs.width);
		height.appendChild(create.label('Image Height'));
		height.appendChild(this.inputs.height);
		controls.appendChild(width);
		controls.appendChild(height);
		
		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.appendChild(icon);
		this.wrapper.appendChild(create.label('Pattern'));
		this.wrapper.appendChild(this.inputs.globs);
		this.wrapper.appendChild(controls);

		this.settings = [{
			name: 'stretched',
			icon: '<svg width="17" height="10" viewBox="0 0 17 10" xmlns="http://www.w3.org/2000/svg"><path d="M13.568 5.925H4.056l1.703 1.703a1.125 1.125 0 0 1-1.59 1.591L.962 6.014A1.069 1.069 0 0 1 .588 4.26L4.38.469a1.069 1.069 0 0 1 1.512 1.511L4.084 3.787h9.606l-1.85-1.85a1.069 1.069 0 1 1 1.512-1.51l3.792 3.791a1.069 1.069 0 0 1-.475 1.788L13.514 9.16a1.125 1.125 0 0 1-1.59-1.591l1.644-1.644z"/></svg>'
		}];

	}

	static get toolbox() {

		return {
			title: 'Slider',
			icon: '<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="15px" viewBox="0 0 18 15"><path d="M14,0H4C1.791,0,0,1.791,0,4v7c0,2.209,1.791,4,4,4h10c2.209,0,4-1.791,4-4V4C18,1.791,16.209,0,14,0z M16,11 c0,1.103-0.897,2-2,2H4c-1.103,0-2-0.897-2-2V4c0-1.103,0.897-2,2-2h10c1.103,0,2,0.897,2,2V11z"/><path d="M6.5,8.5c-0.097,0-0.194-0.028-0.277-0.084l-3-2C3.083,6.323,3,6.167,3,6s0.083-0.323,0.223-0.416l3-2 c0.153-0.102,0.352-0.111,0.513-0.025C6.898,3.646,7,3.815,7,4v4c0,0.185-0.102,0.354-0.264,0.44C6.662,8.48,6.581,8.5,6.5,8.5z"/><path d="M11.264,8.44C11.102,8.354,11,8.185,11,8V4c0-0.185,0.102-0.354,0.264-0.441c0.162-0.086,0.361-0.077,0.514,0.025l3,2 C14.916,5.677,15,5.833,15,6s-0.084,0.323-0.223,0.416l-3,2C11.693,8.472,11.598,8.5,11.5,8.5C11.419,8.5,11.338,8.48,11.264,8.44z"/><circle cx="9" cy="11" r="1"/><circle cx="6" cy="11" r="1"/><circle cx="12" cy="11" r="1"/></svg>'
		};

	}

	render() {

		return this.wrapper;

	}

	save() {

		return Object.assign(this.data, {
			globs: this.inputs.globs.innerHTML,
			width: parseInt(this.inputs.width.innerHTML),
			height: parseInt(this.inputs.height.innerHTML)
		});

	}

	renderSettings() {

		var wrapper = document.createElement('div'),
			block = this;

		this.settings.forEach(function(tune) {
			
			var button = document.createElement('div');

			button.classList.add('cdx-settings-button');
			button.classList.toggle('cdx-settings-button--active', block.data[tune.name]);
			button.innerHTML = tune.icon;
			wrapper.appendChild(button);

			button.addEventListener('click', function() {
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