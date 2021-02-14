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
			title: AutomadEditorTranslation.get('nested_toolbox'),
			icon: '<svg width="18px" height="18px" viewBox="0 0 18 18"><path d="M13,0H5C2.2,0,0,2.2,0,5v8c0,2.8,2.2,5,5,5h8c2.8,0,5-2.2,5-5V5C18,2.2,15.8,0,13,0z M16,13c0,1.7-1.3,3-3,3H5 c-1.7,0-3-1.3-3-3V5c0-1.7,1.3-3,3-3h8c1.7,0,3,1.3,3,3V13z"/><path d="M10,11H5c-0.6,0-1-0.4-1-1V5c0-0.6,0.4-1,1-1h5c0.6,0,1,0.4,1,1v5C11,10.6,10.6,11,10,11z"/></svg>'
		};
	}

	constructor({data, config, api}) {

		var create = Automad.util.create,
			ne = Automad.nestedEditor,
			block = this,
			editText = AutomadEditorTranslation.get('nested_edit');
		
		this.api = api;

		this.data = {
			nestedData: data.nestedData || {},
			card: data.card || ''
		};

		this.layoutSettings = AutomadLayout.renderSettings(this.data, data, api, config);
		this.container = document.querySelector('body');

		this.wrapper = create.element('div', ['am-block-editor-container', AutomadBlockNested.cls.block]);
		this.wrapper.innerHTML = `
			<input type="hidden">
			<section></section>
			<a href="#">
				${editText}&nbsp;
				<i class="uk-icon-expand"></i>
			</a>
		`;

		this.toggleCardClass(this.data.card, true);

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
			isNested: true,
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
			isNested: true,
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

		var create = Automad.util.create,
			wrapper = create.element('div', []),
			inner = create.element('div', ['cdx-settings-1-2']),
			btnCls = this.api.styles.settingsButton,
			btnActiveCls = this.api.styles.settingsButtonActive,
			block = this,
			settings = [
				{
					title: AutomadEditorTranslation.get('nested_card_primary'),
					value: 'primary',
					icon: '<svg width="20px" height="20px" viewBox="0 0 20 20"><path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M3,11c0-0.6,0.4-1,1-1h6 c0.6,0,1,0.4,1,1v1c0,0.6-0.4,1-1,1H4c-0.6,0-1-0.4-1-1V11z M13,17H4c-0.6,0-1-0.4-1-1c0-0.6,0.4-1,1-1h9c0.6,0,1,0.4,1,1 C14,16.6,13.6,17,13,17z M17,7c0,0.6-0.4,1-1,1H4C3.4,8,3,7.6,3,7V4c0-0.6,0.4-1,1-1h12c0.6,0,1,0.4,1,1V7z"/></svg>'
				},
				{
					title: AutomadEditorTranslation.get('nested_card_secondary'),
					value: 'secondary',
					icon: '<svg width="20px" height="20px" viewBox="0 0 20 20"><path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18.5,16c0,1.4-1.1,2.5-2.5,2.5H4 c-1.4,0-2.5-1.1-2.5-2.5V4c0-1.4,1.1-2.5,2.5-2.5h12c1.4,0,2.5,1.1,2.5,2.5V16z"/><path d="M16,8H4C3.4,8,3,7.6,3,7V4c0-0.6,0.4-1,1-1h12c0.6,0,1,0.4,1,1v3C17,7.6,16.6,8,16,8z"/><path d="M10,13H4c-0.6,0-1-0.4-1-1v-1c0-0.6,0.4-1,1-1h6c0.6,0,1,0.4,1,1v1C11,12.6,10.6,13,10,13z"/><path d="M13,17H4c-0.6,0-1-0.4-1-1v0c0-0.6,0.4-1,1-1h9c0.6,0,1,0.4,1,1v0C14,16.6,13.6,17,13,17z"/></svg>'
				}
			];

		settings.forEach((item) => {

			let button = create.element('div', [btnCls]);

			button.classList.toggle(btnActiveCls, (this.data['card'] == item.value));
			button.innerHTML = item.icon;
			this.api.tooltip.onHover(button, item.title, { placement: 'top' });

			button.addEventListener('click', function () {

				const _buttons = inner.querySelectorAll(`.${btnCls}`);

				Array.from(_buttons).forEach((_button) => {
					_button.classList.toggle(btnActiveCls, false);	
				});

				settings.forEach((_item) => {
					block.toggleCardClass(_item.value, false);
				});
				
				if (block.data['card'] != item.value) {
					block.data['card'] = item.value;
					button.classList.toggle(btnActiveCls);
					block.toggleCardClass(item.value, true);
				} else {
					block.data['card'] = '';
				}

			});

			inner.appendChild(button);

		});

		wrapper.appendChild(inner);
		wrapper.appendChild(this.layoutSettings);

		return wrapper;

	}

	toggleCardClass(value, state) {

		if (value) {
			this.wrapper.classList.toggle(`${AutomadBlockNested.cls.block}-card-${value}`, state);
		}

	}

}