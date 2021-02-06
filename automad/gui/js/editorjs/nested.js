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

		this.layoutSettings = Automad.blockEditor.renderLayoutSettings(this.data, data, api, true);
		this.parentEditor = document.getElementById(config.parentEditorId);

		this.wrapper = create.element('div', ['am-block-editor-container', 'am-block-nested']);
		this.wrapper.innerHTML = `
			<input type="hidden">
			<section></section>
			<a href="#">edit</a>
		`;

		this.input = this.wrapper.querySelector('input');
		this.input.value = JSON.stringify(this.data.nestedData, null, 2);
		this.holder = this.wrapper.querySelector('section');
		this.button = this.wrapper.querySelector('a');

		this.modalEditor = null;

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

		if (this.editor !== undefined) {
			this.editor.destroy();
		}

		this.holder.innerHTML = '';

		this.editor = Automad.blockEditor.createEditor({
			holder: this.holder,
			input: this.input,
			parentKey: this.data.parentKey,
			hasNestedEditor: false,
			readOnly: true
		});

	}

	showModal() {

		const create = Automad.util.create,
			  ne = Automad.nestedEditor,
			  container = this.parentEditor.parentNode,
			  block = this;

		try {
			ne.$(modalWrapper).remove();
		} catch (e) {}

		const modalWrapper = create.element('div', ['am-nested-editor-modal-container']);

		modalWrapper.innerHTML = `
			<div id="am-nested-editor-modal" class="uk-modal">
				<div class="uk-modal-dialog uk-modal-dialog-large am-block-editor">
					<section id="am-nested-editor"></section>
				</div>
			</div>
		`;

		container.appendChild(modalWrapper);

		const modal = ne.UIkit.modal('#am-nested-editor-modal', {modal: false});

		modal.on('show.uk.modal', function () {

			try {
				block.modalEditor.destroy();
			} catch (e) { }
			
			block.modalEditor = Automad.blockEditor.createEditor({

				holder: 'am-nested-editor',
				input: block.input,
				parentKey: block.data.parentKey,
				hasNestedEditor: false,
				autofocus: true,
				onReady: function () {

					modal.on('hide.uk.modal', function () {

						try {
							block.modalEditor.destroy();
						} catch (e) {}
						
						block.renderNested();

					});

				}
			});

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