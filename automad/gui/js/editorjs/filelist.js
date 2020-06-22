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


class AutomadFilelist {

	constructor({data}) {

		var create = Automad.util.create;

		this.data = {
			file: data.file || '',
			glob: data.glob || '*.*'
		};

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.innerHTML = `
			<div class="am-block-icon">${AutomadFilelist.toolbox.icon}</div>
			${create.label('Snippet File').outerHTML}
			<div class="uk-form-select uk-button uk-button-success uk-button-large uk-width-1-1" data-uk-form-select>
				<i class="uk-icon-file-text-o"></i>&nbsp;
				<span></span>
				${create.select(
					['am-block-file'], 
					['default'].concat(window.AutomadBlockTemplates.filelist), 
					this.data.file
				).outerHTML}
			</div>
			${create.label('Glob Patterns &mdash; Separated by Comma').outerHTML}
			${create.editable(['cdx-input', 'am-block-glob'], '*.pdf, *.zip', this.data.glob).outerHTML}
		`;

		this.inputs = {
			file: this.wrapper.querySelector('.am-block-file'),
			glob: this.wrapper.querySelector('.am-block-glob')
		}

	}

	static get toolbox() {

		return {
			title: 'Filelist',
			icon: 'F'
		};

	}

	render() {

		return this.wrapper;

	}

	save() {

		return {
			file: this.inputs.file.value,
			glob: this.inputs.glob.innerHTML
		};

	}

}