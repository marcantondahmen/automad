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
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


+function(Automad, $, UIkit) {

	Automad.nestedEditor = {

		$: $,
		UIkit: UIkit

	}

}(window.Automad = window.Automad || {}, jQuery, UIkit);


class AutomadBlockNested {

	static get cls() {
		return {
			block: 'am-block-nested',
			modal: 'am-block-nested-modal',
			modalContainer: 'am-block-nested-modal-container'
		}
	}

	static get ids() {
		return {
			modal: 'am-block-nested-modal',
			modalEditor: 'am-block-nested-modal-editor'
		}
	}

	static get enableLineBreaks() {
		return true;
	}

	static get sanitize() {
		return {
			nestedData: true // Allow HTML tags
		};
	}

	static get toolbox() {
		return {
			title: 'Nested Block Editor',
			icon: '<svg width="18px" height="18px" viewBox="0 0 18 18"><path d="M13,0H5C2.2,0,0,2.2,0,5v8c0,2.8,2.2,5,5,5h8c2.8,0,5-2.2,5-5V5C18,2.2,15.8,0,13,0z M16,13c0,1.7-1.3,3-3,3H5 c-1.7,0-3-1.3-3-3V5c0-1.7,1.3-3,3-3h8c1.7,0,3,1.3,3,3V13z"/><path d="M10,11H5c-0.6,0-1-0.4-1-1V5c0-0.6,0.4-1,1-1h5c0.6,0,1,0.4,1,1v5C11,10.6,10.6,11,10,11z"/></svg>'
		};
	}

	constructor({data, config, api}) {

		var create = Automad.util.create,
			ne = Automad.nestedEditor,
			block = this;
		
		this.api = api;

		this.data = {
			parentKey: config.parentKey,
			parentEditorId: config.parentEditorId,
			nestedData: data.nestedData || {}
		};

		this.layoutSettings = AutomadLayout.renderSettings(this.data, data, api, true);
		this.parentEditor = document.getElementById(config.parentEditorId);
		this.container = document.querySelector('body');

		this.wrapper = create.element('div', ['am-block-editor-container', AutomadBlockNested.cls.block]);
		this.wrapper.innerHTML = `
			<input type="hidden">
			<section></section>
			<a href="#"><i class="uk-icon-expand"></i></a>
		`;

		this.input = this.wrapper.querySelector('input');
		this.input.value = JSON.stringify(this.data.nestedData, null, 2);
		this.holder = this.wrapper.querySelector('section');
		this.button = this.wrapper.querySelector('a');

		this.renderNested();

		ne.$(this.button).on('click', function(event) {
			event.preventDefault();
			event.stopPropagation();
			block.showModal();
		});
		
	}

	appendCallback() {

		this.showModal();

	}

	renderNested() {

		try {
			this.editor.destroy();
		} catch (e) { }

		this.holder.innerHTML = '';

		this.editor = Automad.blockEditor.createEditor({
			holder: this.holder,
			input: this.input,
			parentKey: this.data.parentKey,
			hasNestedEditor: false,
			readOnly: true
		});

	}

	destroyModal() {

		try {
			this.modalEditor.destroy();
		} catch (e) {}

		var container = Automad.nestedEditor.$(this.container).find(`.${AutomadBlockNested.cls.modalContainer}`);

		try {
			container.prev('.ct').remove();
			container.next('.ct').remove();
		} catch (e) { }

		try {
			container.remove();
		} catch (e) { }

	}

	showModal() {

		const create = Automad.util.create,
			  ne = Automad.nestedEditor,
			  block = this;

		this.destroyModal();

		this.modalWrapper = create.element('div', [AutomadBlockNested.cls.modalContainer]);
		this.modalWrapper.innerHTML = `
			<div id="${AutomadBlockNested.ids.modal}" class="uk-modal ${AutomadBlockNested.cls.modal}">
				<div class="uk-modal-dialog am-block-editor">
					<div class="uk-modal-header">
						<a class="uk-modal-close"><i class="uk-icon-compress"></i></a>
					</div>
					<section 
					id="${AutomadBlockNested.ids.modalEditor}" 
					class="am-block-editor-container"
					></section>
				</div>
			</div>
		`;

		this.container.appendChild(this.modalWrapper);

		const modal = ne.UIkit.modal(`#${AutomadBlockNested.ids.modal}`, { modal: false });

		this.modalEditor = Automad.blockEditor.createEditor({
			holder: AutomadBlockNested.ids.modalEditor,
			input: this.input,
			parentKey: this.data.parentKey,
			hasNestedEditor: false,
			autofocus: true,
			onReady: function () {
				modal.on('hide.uk.modal', function () {
					block.destroyModal();
					block.renderNested();
				});
			}
		});

		modal.show();

	}

	render() {

		return this.wrapper;

	}

	getInputData() {

		let data;

		try {
			data = JSON.parse(this.input.value.replace(/&amp;/g, '&'));
		} catch (e) {
			data = {};
		}

		return data;

	}

	save() {

		return Object.assign(this.data, {
			nestedData: this.getInputData()
		});

	}

	renderSettings() {

		return this.layoutSettings;

	}

}