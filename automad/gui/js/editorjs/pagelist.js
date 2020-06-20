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


class AutomadPagelist {

	constructor({ data, api }) {

		var create = Automad.util.create;

		this.api = api;

		this.data =  {
			type: data.type || '',
			matchUrl: data.matchUrl || '',
			filter: data.filter || '',
			template: data.template || '',
			limit: data.limit || 3,
			sortKey: data.sortKey || ':path',
			sortOrder: data.sortOrder || 'asc',
			file: data.file || ''
		}

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.innerHTML = `
			<div class="am-block-icon">${AutomadPagelist.toolbox.icon}</div>
			<ul class="uk-grid">
				<li class="uk-width-medium-1-1">
					${create.label('Snippet File').outerHTML}
					<div class="uk-form-select uk-button uk-button-primary uk-button-large uk-width-1-1 uk-text-left" data-uk-form-select>
						<i class="uk-icon-file-text"></i>&nbsp;
						<span></span>
						${create.select(
							['am-block-file'], 
							['default'].concat(window.AutomadBlockTemplates.pagelist), 
							this.data.file
						).outerHTML}
					</div>
				</li>
				<li class="uk-width-medium-1-1">
					${create.label('Match URL Regex').outerHTML}
					${create.editable(['cdx-input', 'am-block-match-url'], '/(work|blog)/', this.data.matchUrl).outerHTML}
				</li>
				<li class="uk-width-medium-1-3">
					${create.label('Type').outerHTML}
					${create.select(['cdx-input', 'am-block-type'], ['all', 'children'], this.data.type).outerHTML}
				</li>
				<li class="uk-width-medium-1-3">
					${create.label('Filter by Tag').outerHTML}
					${create.editable(['cdx-input', 'am-block-filter'], '', this.data.filter).outerHTML}
				</li>

				<li class="uk-width-medium-1-3">
					${create.label('Filter by Template Regex').outerHTML}
					${create.editable(['cdx-input', 'am-block-template'], '', this.data.template).outerHTML}
				</li>
				<li class="uk-width-medium-1-3">
					${create.label('Number of Pages').outerHTML}
					${create.editable(['cdx-input', 'am-block-limit'], '', this.data.limit).outerHTML}
				</li>
				<li class="uk-width-medium-1-3">
					${create.label('Sort by Variable').outerHTML}
					${create.editable(['cdx-input', 'am-block-sort-key'], '', this.data.sortKey).outerHTML}
				</li>
				<li class="uk-width-medium-1-3">
					${create.label('Sort Order').outerHTML}
					${create.select(['cdx-input', 'am-block-sort-order'], ['asc', 'desc'], this.data.sortOrder).outerHTML}
				</li>
			</ul>`;

		this.inputs = {
			type: this.wrapper.querySelector('.am-block-type'),
			matchUrl: this.wrapper.querySelector('.am-block-match-url'),
			filter: this.wrapper.querySelector('.am-block-filter'),
			template: this.wrapper.querySelector('.am-block-template'),
			limit: this.wrapper.querySelector('.am-block-limit'),
			sortKey: this.wrapper.querySelector('.am-block-sort-key'),
			sortOrder: this.wrapper.querySelector('.am-block-sort-order'),
			file: this.wrapper.querySelector('.am-block-file')
		}

	}

	static get toolbox() {

		return {
			title: 'Pagelist',
			icon: 'P'
		};

	}

	static get sanitize() {

		return {
			type: false,
			matchUrl: false,
			filter: false,
			template: false,
			limit: false,
			sortKey: false,
			sortOrder: false,
			file: false
		}

	}

	render() {

		return this.wrapper;

	}

	save() {

		return {
			type: this.inputs.type.value,
			matchUrl: this.inputs.matchUrl.innerHTML,
			filter: this.inputs.filter.innerHTML,
			template: this.inputs.template.innerHTML,
			limit: this.inputs.limit.innerHTML,
			sortKey: this.inputs.sortKey.innerHTML,
			sortOrder: this.inputs.sortOrder.value,
			file: this.inputs.file.value
		};

	}


}