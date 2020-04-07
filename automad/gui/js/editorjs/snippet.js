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


class AutomadSnippet {

	constructor({data}) {

		var create = Automad.util.create;

		this.data = {
			file: data.file || '',
		};

		this.inputs = {
			file: create.editable(['cdx-input'], 'Enter the file path of a snippet', this.data.file),
		};

		var icon = document.createElement('div');

		icon.innerHTML = AutomadSnippet.toolbox.icon;
		icon.classList.add('am-block-icon');
		
		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.appendChild(icon);
		this.wrapper.appendChild(create.label('Path to Snippet'));
		this.wrapper.appendChild(this.inputs.file);

	}

	static get toolbox() {

		return {
			title: 'Template Snippet',
			icon: '<svg xmlns="http://www.w3.org/2000/svg" width="15px" height="18px" viewBox="0 0 15 18"><path d="M11.016,1.516C9.891,0.391,9.25,0,7,0C5,0,4,0,4,0C1.791,0,0,1.791,0,4v10c0,2.209,1.791,4,4,4h7c2.209,0,4-1.791,4-4 c0,0,0-4.016,0-6c0-2.25-0.328-2.828-1.484-3.984C11.947,2.448,12.526,3.028,11.016,1.516z M9,2.203L12.896,6H11 C9.896,6,9,5.104,9,4V2.203z M13,14c0,1.104-0.896,2-2,2H4c-1.104,0-2-0.896-2-2V4c0-1.104,0.896-2,2-2h3v2c0,2.209,1.791,4,4,4h2 V14z"/><path d="M8.635,9.435c-0.312,0.312-0.312,0.818,0,1.131L10.068,12l-1.434,1.435c-0.312,0.312-0.312,0.818,0,1.131 C8.791,14.722,8.996,14.8,9.2,14.8c0.205,0,0.409-0.078,0.565-0.234l2-2c0.312-0.312,0.312-0.818,0-1.131l-2-2 C9.454,9.124,8.947,9.122,8.635,9.435z"/><path d="M6.366,9.435c-0.313-0.312-0.82-0.311-1.132,0l-2,2c-0.312,0.312-0.312,0.818,0,1.131l2,2 C5.391,14.722,5.595,14.8,5.8,14.8s0.41-0.078,0.566-0.234c0.312-0.312,0.312-0.818,0-1.131L4.932,12l1.435-1.435 C6.678,10.253,6.678,9.747,6.366,9.435z"/></svg>'
		};

	}

	render() {

		return this.wrapper;

	}

	save() {

		return {
			file: this.inputs.file.innerHTML
		};

	}

}