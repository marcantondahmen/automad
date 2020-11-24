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
			offset: data.offset || 0,
			sortKey: data.sortKey || ':path',
			sortOrder: data.sortOrder || 'asc',
			file: data.file || ''
		}

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add('uk-panel', 'uk-panel-box');
		this.wrapper.innerHTML = `
			<div class="am-block-icon">${AutomadPagelist.toolbox.icon}</div>
			<div class="am-block-title">${AutomadPagelist.toolbox.title}</div>
			<hr>
			<ul class="uk-grid">
				<li class="uk-width-medium-1-1">
					${create.label('Select Template', ['am-block-label', 'uk-margin-top-remove']).outerHTML}
					<div class="am-block-file-select uk-form-select uk-button uk-text-left uk-width-1-1" data-uk-form-select>
						<i class="uk-icon-file"></i>&nbsp;
						<span></span>
						${create.select(
							['am-block-file'], 
							['default template'].concat(window.AutomadBlockTemplates.pagelist), 
							this.data.file
						).outerHTML}
					</div>
				</li>
				<li class="uk-width-medium-1-1">
					${create.label('Match URL Regex').outerHTML}
					${create.editable(['cdx-input', 'am-block-match-url'], 'work|blog', this.data.matchUrl).outerHTML}
				</li>
				<li class="uk-width-medium-1-3">
					${create.label('Type').outerHTML}
					${create.select(['cdx-input', 'am-block-type'], ['all', 'children', 'related', 'siblings'], this.data.type).outerHTML}
				</li>
				<li class="uk-width-medium-1-3">
					${create.label('Filter by Tag').outerHTML}
					${create.editable(['cdx-input', 'am-block-filter'], '', this.data.filter).outerHTML}
				</li>
				<li class="uk-width-medium-1-3">
					${create.label('Filter by Template').outerHTML}
					${create.editable(['cdx-input', 'am-block-template'], 'post|project', this.data.template).outerHTML}
				</li>
				<li class="uk-width-medium-1-3">
					${create.label('Limit and Offset').outerHTML}
					<div class="am-form-input-group">
						${create.editable(['cdx-input', 'am-block-limit'], '', this.data.limit).outerHTML}
						${create.editable(['cdx-input', 'am-block-offset'], '', this.data.offset).outerHTML}
					</div>
				</li>
				<li class="uk-width-medium-1-3">
					${create.label('Sort by Variable').outerHTML}
					${create.editable(['cdx-input', 'am-block-sort-key'], ':path', this.data.sortKey).outerHTML}
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
			offset: this.wrapper.querySelector('.am-block-offset'),
			sortKey: this.wrapper.querySelector('.am-block-sort-key'),
			sortOrder: this.wrapper.querySelector('.am-block-sort-order'),
			file: this.wrapper.querySelector('.am-block-file')
		}

	}

	static get toolbox() {

		return {
			title: 'Pagelist',
			icon: '<svg width="16px" height="18px" viewBox="0 0 16 18"><path d="M7,3C4.79,3,3,4.79,3,7v7c0,2.21,1.79,4,4,4h5c2.21,0,4-1.79,4-4V7c0-2.21-1.79-4-4-4 M12,5c1.1,0,2,0.9,2,2v7 c0,1.1-0.9,2-2,2H7c-1.1,0-2-0.9-2-2V7c0-1.1,0.9-2,2-2"/><path d="M2,11V6V4c0-1.1,0.9-2,2-2h1h4h3.45C11.75,0.81,10.48,0,9,0H4C1.79,0,0,1.79,0,4v7c0,1.48,0.81,2.75,2,3.45V11z"/><path d="M12,9H7C6.72,9,6.5,8.78,6.5,8.5S6.72,8,7,8h5c0.28,0,0.5,0.22,0.5,0.5S12.28,9,12,9z"/><path d="M12,11H7c-0.28,0-0.5-0.22-0.5-0.5S6.72,10,7,10h5c0.28,0,0.5,0.22,0.5,0.5S12.28,11,12,11z"/><path d="M12,13H7c-0.28,0-0.5-0.22-0.5-0.5S6.72,12,7,12h5c0.28,0,0.5,0.22,0.5,0.5S12.28,13,12,13z"/></svg>'
		};

	}

	static get sanitize() {

		return {
			type: false,
			matchUrl: false,
			filter: false,
			template: false,
			limit: false,
			offset: false,
			sortKey: false,
			sortOrder: false,
			file: false
		}

	}

	render() {

		return this.wrapper;

	}

	save() {

		var stripNbsp = Automad.util.stripNbsp;

		return {
			type: this.inputs.type.value,
			matchUrl: stripNbsp(this.inputs.matchUrl.innerHTML),
			filter: stripNbsp(this.inputs.filter.innerHTML),
			template: stripNbsp(this.inputs.template.innerHTML),
			limit: stripNbsp(this.inputs.limit.innerHTML),
			offset: stripNbsp(this.inputs.offset.innerHTML),
			sortKey: stripNbsp(this.inputs.sortKey.innerHTML),
			sortOrder: this.inputs.sortOrder.value,
			file: this.inputs.file.value
		};

	}


}