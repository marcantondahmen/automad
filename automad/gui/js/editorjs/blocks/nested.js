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
			style: {
				css: false
			}
		};
	}

	static get toolbox() {
		return {
			title: AutomadEditorTranslation.get('nested_toolbox'),
			icon: '<svg width="18px" height="18px" viewBox="0 0 18 18"><path d="M14,0H4C1.8,0,0,1.8,0,4v10c0,2.2,1.8,4,4,4h10c2.2,0,4-1.8,4-4V4C18,1.8,16.2,0,14,0z M3,4c0-0.6,0.4-1,1-1h7 c0.6,0,1,0.4,1,1v2c0,0.6-0.4,1-1,1H4C3.4,7,3,6.6,3,6V4z M9,15H4c-0.6,0-1-0.4-1-1c0-0.6,0.4-1,1-1h5c0.6,0,1,0.4,1,1 C10,14.6,9.6,15,9,15z M14,11H4c-0.6,0-1-0.4-1-1c0-0.6,0.4-1,1-1h10c0.6,0,1,0.4,1,1C15,10.6,14.6,11,14,11z"/></svg>'
		};
	}

	constructor({data, config, api}) {

		var create = Automad.util.create;
		
		this.api = api;

		this.data = {
			nestedData: data.nestedData || {},
			style: data.style || {}
		};

		this.layoutSettings = AutomadLayout.renderSettings(this.data, data, api, config);
		this.container = document.querySelector('body');

		this.wrapper = create.element('div', ['am-block-editor-container', AutomadBlockNested.cls.block]);
		this.wrapper.innerHTML = `
			<input type="hidden">
			<section></section>
			<div class="am-nested-overlay-focus"></div>
			<a href="#" class="am-nested-edit-button">
				${AutomadEditorTranslation.get('nested_edit')}&nbsp;
				<i class="uk-icon-expand"></i>
			</a>
			`;

		this.input = this.wrapper.querySelector('input');
		this.input.value = JSON.stringify(this.data.nestedData, null, 2);
		this.holder = this.wrapper.querySelector('section');
		this.overlay = this.wrapper.querySelector('.am-nested-overlay-focus');
		this.button = this.wrapper.querySelector('.am-nested-edit-button');

		this.renderNested();

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

		this.applyStyleSettings(this.holder);

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

	numberUnit(clsPrefix, value, placeholder) {

		const create = Automad.util.create,
			  units = ['px', 'em', 'rem', '%', 'vw', 'vh'];
		
		var	number = parseInt(value) || '',
			unit = value.replace(/.+?(px|em|rem|%|vh|vw)/g, '$1') || 'px';

		return `<div class="am-form-input-group">
					${create.editable(
						[AutomadEditorConfig.cls.input, `am-nested-${clsPrefix}-number`], 
						placeholder, 
						number
					).outerHTML}
					${create.select(
						[AutomadEditorConfig.cls.input, `am-nested-${clsPrefix}-unit`],
						units,
						unit
					).outerHTML}
				</div>`;

	}

	renderStyleSettings() {

		const create = Automad.util.create,
			  t = AutomadEditorTranslation.get,
			  style = Object.assign({
				  card: false,
				  shadow: false,
				  matchRowHeight: false,
				  color: '',
				  backgroundColor: '',
				  borderColor: '',
				  borderWidth: '',
				  borderRadius: '',
				  backgroundImage: '',
				  paddingTop: '',
				  paddingBottom: ''
			  }, this.data.style);

		return `
			<div
			id="${AutomadBlockNested.ids.modalDropdown}" 
			class="uk-form" 
			data-uk-dropdown="{mode:'click', pos:'bottom-left'}"
			>
				<div class="uk-button am-nested-style-button">
					<i class="uk-icon-sliders"></i>&nbsp;
					${t('nested_style')}&nbsp;
					<i class="uk-icon-caret-down"></i>
				</div>
				<div class="uk-dropdown uk-dropdown-blank">
			  		<div class="uk-form-row uk-margin-small-bottom">
						<label
						class="am-toggle-switch uk-text-truncate uk-button uk-text-left uk-width-1-1"
						data-am-toggle
						>
							${t('nested_card')}
							<input id="am-nested-card" type="checkbox" ${style.card == true ? 'checked' : ''}>
						</label>
					</div>
					<div class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
						<div>
							<div class="uk-form-row">
								<label
								class="am-toggle-switch uk-text-truncate uk-button uk-text-left uk-width-1-1"
								data-am-toggle
								>
									${t('nested_shadow')}
									<input id="am-nested-shadow" type="checkbox" ${style.shadow == true ? 'checked' : ''}>
								</label>
							</div>
						</div>
						<div>
							<div class="uk-form-row">
								<label
								class="am-toggle-switch uk-text-truncate uk-button uk-text-left uk-width-1-1"
								data-am-toggle
								>
									${t('nested_match_row_height')}
									<input id="am-nested-match-row-height" type="checkbox" ${style.matchRowHeight == true ? 'checked' : ''}>
								</label>
							</div>
						</div>
					</div>
					<div class="uk-grid uk-grid-width-medium-1-2 uk-margin-top-remove">
			  			<div>
							${create.label(t('nested_color')).outerHTML}
							${this.colorPicker('am-nested-color', style.color)}
						</div>
						<div>
			  				${create.label(t('nested_background_color')).outerHTML}
							${this.colorPicker('am-nested-background-color', style.backgroundColor)}
						</div>
					</div>
					<div class="uk-grid uk-grid-width-medium-1-3 uk-margin-top-remove">
			  			<div>
							${create.label(t('nested_border_color')).outerHTML}
							${this.colorPicker('am-nested-border-color', style.borderColor)}
						</div>
						<div>
			  				${create.label(t('nested_border_width')).outerHTML}
							${this.numberUnit('border-width', style.borderWidth, '')}
						</div>
						<div>
			  				${create.label(t('nested_border_radius')).outerHTML}
							${this.numberUnit('border-radius', style.borderRadius, '')}
						</div>
					</div>
					${create.label(t('nested_background_image')).outerHTML}
					<div class="am-form-icon-button-input uk-flex" data-am-select-image-field>
						<button type="button" class="uk-button">
							<i class="uk-icon-folder-open-o"></i>
						</button>
						<input 
						type="text" 
						class="am-nested-background-image uk-form-controls uk-width-1-1" 
						value="${style.backgroundImage}"
						/>
					</div>
					<div class="uk-grid uk-grid-width-medium-1-2 uk-margin-top-remove">
			  			<div>
			  				${create.label(`${t('nested_padding_top')} (padding top)`).outerHTML}
							${this.numberUnit('padding-top', style.paddingTop, '')}
			  			</div>
						<div>
			  				${create.label(`${t('nested_padding_bottom')} (padding bottom)`).outerHTML}
							${this.numberUnit('padding-bottom', style.paddingBottom, '')}
			  			</div>
					</div>
				</div>
			</div>
		`;

	}

	saveStyleSettings() {

		let inputs = {},
			wrapper = this.modalWrapper;

		inputs.card = wrapper.querySelector('#am-nested-card');
		inputs.shadow = wrapper.querySelector('#am-nested-shadow');
		inputs.matchRowHeight = wrapper.querySelector('#am-nested-match-row-height');
		inputs.color = wrapper.querySelector('#am-nested-color');
		inputs.backgroundColor = wrapper.querySelector('#am-nested-background-color');
		inputs.borderColor = wrapper.querySelector('#am-nested-border-color');
		inputs.borderWidthNumber = wrapper.querySelector('.am-nested-border-width-number');
		inputs.borderWidthUnit = wrapper.querySelector('.am-nested-border-width-unit');
		inputs.borderRadiusNumber = wrapper.querySelector('.am-nested-border-radius-number');
		inputs.borderRadiusUnit = wrapper.querySelector('.am-nested-border-radius-unit');
		inputs.backgroundImage = wrapper.querySelector('.am-nested-background-image');
		inputs.paddingTopNumber = wrapper.querySelector('.am-nested-padding-top-number');
		inputs.paddingTopUnit = wrapper.querySelector('.am-nested-padding-top-unit');
		inputs.paddingBottomNumber = wrapper.querySelector('.am-nested-padding-bottom-number');
		inputs.paddingBottomUnit = wrapper.querySelector('.am-nested-padding-bottom-unit');
		
		this.data.style = {
			card: inputs.card.checked,
			shadow: inputs.shadow.checked,
			matchRowHeight: inputs.matchRowHeight.checked,
			color: inputs.color.value,
			backgroundColor: inputs.backgroundColor.value,
			borderColor: inputs.borderColor.value,
			borderWidth: this.getNumberUnit(inputs.borderWidthNumber, inputs.borderWidthUnit),
			borderRadius: this.getNumberUnit(inputs.borderRadiusNumber, inputs.borderRadiusUnit),
			backgroundImage: inputs.backgroundImage.value,
			paddingTop: this.getNumberUnit(inputs.paddingTopNumber, inputs.paddingTopUnit),
			paddingBottom: this.getNumberUnit(inputs.paddingBottomNumber, inputs.paddingBottomUnit)
		};

	}

	applyStyleSettings(element) {

		const style = this.data.style;

		element.removeAttribute('style');
		
		if (style.backgroundImage) {
			element.style.backgroundImage = `url('${Automad.util.resolveUrl(style.backgroundImage)}')`;
			element.style.backgroundPosition = '50% 50%';
			element.style.backgroundSize = 'cover';
		}

		if (style.backgroundImage || 
			style.borderColor || 
			style.backgroundColor || 
			style.shadow ||
			style.borderWidth) { element.style.padding = '1rem'; }
			
		if (style.borderWidth && !style.borderWidth.startsWith('0')) {
			element.style.borderStyle = 'solid';
		}

		if (style.shadow) {
			element.style.boxShadow = '0 0.2rem 2rem rgba(0,0,0,0.1)';
		}

		if (style.matchRowHeight) {
			element.style.height = '100%';
		}

		['color', 
		'backgroundColor', 
		'paddingTop', 
		'paddingBottom', 
		'borderColor', 
		'borderWidth', 
		'borderRadius'].forEach((item) => {

			if (style[item] && !style[item].startsWith('0')) {
				element.style[item] = style[item];
			}

		});

	}

	initToggles() {

		const toggles = this.modalWrapper.querySelectorAll('label > input');

		Array.from(toggles).forEach((item) => {
			Automad.toggle.update(Automad.nestedEditor.$(item));
		});

	}

	showModal() {

		const create = Automad.util.create,
			  ne = Automad.nestedEditor;

		this.destroyModal();

		this.modalWrapper = create.element('div', [AutomadBlockNested.cls.modalContainer]);
		this.modalWrapper.innerHTML = `
			<div id="${AutomadBlockNested.ids.modal}" class="uk-modal ${AutomadBlockNested.cls.modal}">
				<div class="uk-modal-dialog am-block-editor uk-form">
					<div class="uk-modal-header">
						<div class="uk-position-relative">
							${this.renderStyleSettings()}
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
		this.applyDialogSize();

		ne.$(`#${AutomadBlockNested.ids.modalDropdown}`).on('hide.uk.dropdown', () => {
			this.saveStyleSettings();
			this.applyStyleSettings(document.getElementById(AutomadBlockNested.ids.modalEditor));
		});

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
			onReady: () => {

				this.applyStyleSettings(document.getElementById(AutomadBlockNested.ids.modalEditor));

				modal.on('hide.uk.modal', () => {
					this.saveStyleSettings();
					ne.$(this.input).trigger('change');
					this.destroyModal();
					this.renderNested();
				});

			}
		});

		modal.show();

	}

	applyDialogSize() {

		const dialog = this.modalWrapper.querySelector('.uk-modal-dialog');
		var span = 0.7;

		if (this.data.columnSpan) {
			span = this.data.columnSpan / 12;
		} 

		if (this.data.stretched) {
			span = 1;
		}

		dialog.style.width = `${span * 74}rem`;
		dialog.style.maxWidth = '90vw';

	}

	render() {

		return this.wrapper;

	}

	getNumberUnit(numberInput, unitSelect) {

		const number = Automad.util.stripNbsp(numberInput.textContent).trim() || '0',
			  unit = unitSelect.value;
			  
		return `${number}${unit}`;
		
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