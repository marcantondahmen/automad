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


/**
 *	This block is based on the original delimiter block of editor.js
 *	https://github.com/editor-js/delimiter
 */

class AutomadDelimiter {

	static get isReadOnlySupported() {
		return true;
	}

	static get contentless() {
		return true;
	}

	constructor({ data, config, api }) {

		this.api = api;

		this._CSS = {
			block: this.api.styles.block,
			wrapper: 'ce-delimiter'
		};

		this._data = {};
		this._element = this.drawView();

		this.data = data;
		
	}

	drawView() {
		let div = document.createElement('DIV');

		div.classList.add(this._CSS.wrapper, this._CSS.block);

		return div;
	}

	render() {
		return this._element;
	}

	save(toolsContent) {
		return {};
	}

	static get toolbox() {
		return {
			icon: '<svg width="18px" height="16px" viewBox="0 0 18 16"><path d="M17,9H1C0.45,9,0,8.55,0,8v0c0-0.55,0.45-1,1-1h16c0.55,0,1,0.45,1,1v0C18,8.55,17.55,9,17,9z"/><path d="M9,6C8.74,6,8.49,5.9,8.29,5.71l-3-3c-0.39-0.39-0.39-1.02,0-1.41s1.02-0.39,1.41,0L9,3.59l2.29-2.29 c0.39-0.39,1.02-0.39,1.41,0s0.39,1.02,0,1.41l-3,3C9.51,5.9,9.26,6,9,6z"/><path d="M12,15c-0.26,0-0.51-0.1-0.71-0.29L9,12.41l-2.29,2.29c-0.39,0.39-1.02,0.39-1.41,0s-0.39-1.02,0-1.41l3-3 c0.39-0.39,1.02-0.39,1.41,0l3,3c0.39,0.39,0.39,1.02,0,1.41C12.51,14.9,12.26,15,12,15z"/></svg>',
			title: 'Delimiter'
		};
	}

}