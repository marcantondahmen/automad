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
			modalEditor: 'am-block-nested-modal-editor',
			modalDropdown: 'am-block-nested-modal-dropdown'
		}
	}

	static get enableLineBreaks() {
		return true;
	}

	static get sanitize() {
		return {
			nestedData: true, // Allow HTML tags
			cardStyle: {
				css: false
			}
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
			isCard: data.isCard || false,
			cardStyle: data.cardStyle || {}
		};

		this.layoutSettings = AutomadLayout.renderSettings(this.data, data, api, config);
		this.container = document.querySelector('body');

		this.wrapper = create.element('div', ['am-block-editor-container', AutomadBlockNested.cls.block]);
		this.wrapper.innerHTML = `
			<input type="hidden">
			<section></section>
			<div class="am-nested-overlay-focus"></div>
			<a href="#" class="am-nested-edit-button">
				${editText}&nbsp;
				<i class="uk-icon-expand"></i>
			</a>
		`;

		this.input = this.wrapper.querySelector('input');
		this.input.value = JSON.stringify(this.data.nestedData, null, 2);
		this.holder = this.wrapper.querySelector('section');
		this.overlay = this.wrapper.querySelector('.am-nested-overlay-focus');
		this.button = this.wrapper.querySelector('.am-nested-edit-button');

		this.renderNested();
		this.toggleCardClass();

		[this.button, this.overlay].forEach((item) => {

			item.addEventListener('click', (event) => {
				event.preventDefault();
				event.stopPropagation();
				this.showModal();
			});

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

	colorPicker(id, value) {

		return `<div class="am-u-flex" data-am-colorpicker=""> 
					<input type="color" value="${value}">
					<input type="text" class="am-u-form-controls am-u-width-1-1" id="${id}" value="${value}">
				</div>`

	}

	renderCardSettings() {

		const create = Automad.util.create,
			  t = AutomadEditorTranslation.get,
			  style = Object.assign({
				  shadow: '',
				  matchRowHeight: '',
				  color: '',
				  backgroundColor: '',
				  borderColor: '',
				  css: ''
			  }, this.data.cardStyle);

		return `
			<label 
			class="am-toggle-switch-medium" 
			title="${t('nested_card_tooltip')}"
			data-am-toggle="#${AutomadBlockNested.ids.modalDropdown}"
			data-uk-tooltip="{pos:'bottom'}"
			>
				${t('nested_card_toggle')}
				<input id="am-nested-card-toggle" type="checkbox" ${this.data.isCard == true ? 'checked' : ''}>
			</label>
			<div
			id="${AutomadBlockNested.ids.modalDropdown}" 
			class="am-toggle-container uk-margin-small-left uk-form" 
			data-uk-dropdown="{mode:'click', pos:'bottom-left'}"
			>
				<div class="uk-button uk-button-small uk-button-success">
					${t('nested_card_style')}&nbsp;
					<i class="uk-icon-caret-down"></i>
				</div>
				<div class="uk-dropdown uk-dropdown-blank">
					<div class="uk-form-row">
						<label 
						class="am-toggle-switch uk-button uk-text-left uk-width-1-1" 
						data-am-toggle
						> 
							${t('nested_card_shadow')}
							<input id="am-nested-card-shadow" type="checkbox" ${style.shadow == true ? 'checked' : ''}>
						</label>
					</div>
					<div class="uk-form-row uk-margin-small-top">
						<label
						class="am-toggle-switch uk-button uk-text-left uk-width-1-1"
						data-am-toggle
						>
							${t('nested_card_match_row_height')}
							<input id="am-nested-card-match-row-height" type="checkbox" ${style.matchRowHeight == true ? 'checked' : ''}>
						</label>
					</div>
					${create.label(t('nested_card_color')).outerHTML}
					${this.colorPicker('am-nested-card-color', style.color)}
					${create.label(t('nested_card_background')).outerHTML}
					${this.colorPicker('am-nested-card-background', style.backgroundColor)}
					${create.label(t('nested_card_border')).outerHTML}
					${this.colorPicker('am-nested-card-border', style.borderColor)}
					${create.label(t('nested_card_css')).outerHTML}
					<textarea class="ce-code cdx-input am-nested-card-css">${style.css}</textarea>
				</div>
			</div>
		`;

	}

	saveCardSettings() {

		let inputs = {};

		inputs.toggle = this.modalWrapper.querySelector('#am-nested-card-toggle');
		inputs.shadow = this.modalWrapper.querySelector('#am-nested-card-shadow');
		inputs.matchRowHeight = this.modalWrapper.querySelector('#am-nested-card-match-row-height');
		inputs.color = this.modalWrapper.querySelector('#am-nested-card-color');
		inputs.backgroundColor = this.modalWrapper.querySelector('#am-nested-card-background');
		inputs.borderColor = this.modalWrapper.querySelector('#am-nested-card-border');
		inputs.css = this.modalWrapper.querySelector('.am-nested-card-css');

		this.data.isCard = inputs.toggle.checked;
		this.data.cardStyle = {};

		if (inputs.toggle.checked) {

			this.data.cardStyle = {
				shadow: inputs.shadow.checked,
				matchRowHeight: inputs.matchRowHeight.checked,
				color: inputs.color.value,
				backgroundColor: inputs.backgroundColor.value,
				borderColor: inputs.borderColor.value,
				css: inputs.css.value
			};
			
		}

	}

	initToggles() {

		const toggles = this.modalWrapper.querySelectorAll('label > input');

		Array.from(toggles).forEach((item) => {
			Automad.toggle.update(Automad.nestedEditor.$(item));
		});

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
						<div class="uk-flex uk-flex-middle uk-position-relative">
							${this.renderCardSettings()}
						</div>
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
		this.initToggles();

		const modal = ne.UIkit.modal(`#${AutomadBlockNested.ids.modal}`, { 
						modal: false, 
						bgclose: true, 
						keyboard: false 
					  });

		this.modalEditor = Automad.blockEditor.createEditor({
			holder: AutomadBlockNested.ids.modalEditor,
			input: this.input,
			isNested: true,
			autofocus: true,
			onReady: function () {
				modal.on('hide.uk.modal', function () {
					block.saveCardSettings();
					ne.$(block.input).trigger('change');
					block.destroyModal();
					block.renderNested();
					block.toggleCardClass();
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

	toggleCardClass() {

		this.wrapper.classList.toggle(`${AutomadBlockNested.cls.block}-card`, this.data.isCard);
		
	}

}