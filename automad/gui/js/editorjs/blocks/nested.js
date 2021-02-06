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
			icon: ''
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
		this.container = this.parentEditor.parentNode;

		this.wrapper = create.element('div', ['am-block-editor-container', AutomadBlockNested.cls.block]);
		this.wrapper.innerHTML = `
			<input type="hidden">
			<section></section>
			<a href="#">edit</a>
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

		Automad.nestedEditor.$(this.container).find(`.${AutomadBlockNested.cls.modalContainer}`).remove();

	}

	showModal() {

		const create = Automad.util.create,
			  ne = Automad.nestedEditor,
			  block = this;

		this.destroyModal();

		this.modalWrapper = create.element('div', [AutomadBlockNested.cls.modalContainer]);
		this.modalWrapper.innerHTML = `
			<div id="${AutomadBlockNested.ids.modal}" class="uk-modal">
				<div class="uk-modal-dialog uk-modal-dialog-large am-block-editor">
					<section id="${AutomadBlockNested.ids.modalEditor}"></section>
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