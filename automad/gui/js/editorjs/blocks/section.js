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

	Automad.sectionEditor = {

		$: $,
		UIkit: UIkit

	}

}(window.Automad = window.Automad || {}, jQuery, UIkit);


class AutomadBlockSection {

	static get isReadOnlySupported() {
		return true;
	}

	static get cls() {
		return {
			block: 'am-block-section',
			modal: 'am-block-section-modal',
			modalContainer: 'am-block-section-modal-container',
			flex: 'am-block-section-flex'
		}
	}

	static get ids() {
		return {
			modal: 'am-block-section-modal',
			modalEditor: 'am-block-section-modal-editor',
			modalDropdown: 'am-block-section-modal-dropdown'
		}
	}

	static get enableLineBreaks() {
		return true;
	}

	static get sanitize() {
		return {
			sectionData: true, // Allow HTML tags
			style: {
				css: false
			}
		};
	}

	static get toolbox() {
		return {
			title: AutomadEditorTranslation.get('section_toolbox'),
			icon: '<svg width="18px" height="18px" viewBox="0 0 18 18"><path d="M14,0H4C1.8,0,0,1.8,0,4v10c0,2.2,1.8,4,4,4h10c2.2,0,4-1.8,4-4V4C18,1.8,16.2,0,14,0z M3,4c0-0.6,0.4-1,1-1h7 c0.6,0,1,0.4,1,1v2c0,0.6-0.4,1-1,1H4C3.4,7,3,6.6,3,6V4z M9,15H4c-0.6,0-1-0.4-1-1c0-0.6,0.4-1,1-1h5c0.6,0,1,0.4,1,1 C10,14.6,9.6,15,9,15z M14,11H4c-0.6,0-1-0.4-1-1c0-0.6,0.4-1,1-1h10c0.6,0,1,0.4,1,1C15,10.6,14.6,11,14,11z"/></svg>'
		};
	}

	constructor({data, config, api}) {

		var create = Automad.util.create,
			t = AutomadEditorTranslation.get,
			idSuffix = Date.now();
		
		this.modalContainerCls = `${AutomadBlockSection.cls.modalContainer}-${idSuffix}`;
		this.modalId = `${AutomadBlockSection.ids.modal}-${idSuffix}`;
		this.modalEditorId = `${AutomadBlockSection.ids.modalEditor}-${idSuffix}`;
		this.modalDropdownId = `${AutomadBlockSection.ids.modalDropdown}-${idSuffix}`;

		this.api = api;

		this.data = {
			sectionData: data.sectionData || {},
			style: data.style || {},
			justifyContent: data.justifyContent || ''
		};

		this.container = document.querySelector('body');

		this.wrapper = create.element('div', ['am-block-editor-container', AutomadBlockSection.cls.block]);
		this.wrapper.innerHTML = `
			<input type="hidden">
			<section class="${AutomadBlockSection.cls.flex}"></section>
			<div class="am-section-overlay-focus"></div>
			<a href="#" class="am-section-edit-button">
				${AutomadEditorTranslation.get('section_edit')}&nbsp;
				<i class="uk-icon-expand"></i>
			</a>
			`;

		this.input = this.wrapper.querySelector('input');
		this.input.value = JSON.stringify(this.data.sectionData, null, 2);
		this.holder = this.wrapper.querySelector('section');
		this.overlay = this.wrapper.querySelector('.am-section-overlay-focus');
		this.button = this.wrapper.querySelector('.am-section-edit-button');

		this.renderSection();

		[this.button, this.overlay].forEach((item) => {

			item.addEventListener('click', (event) => {
				event.preventDefault();
				event.stopPropagation();
				this.showModal();
			});

		});

		this.justifySettings = [
			{
				value: 'flex-start',
				icon: '<svg width="1.6em" height="1.6em" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.5 1a.5.5 0 0 1 .5.5v13a.5.5 0 0 1-1 0v-13a.5.5 0 0 1 .5-.5z"/><path d="M3 7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7z"/></svg>',
				title: t('section_justify_start')
			},
			{
				value: 'center',
				icon: '<svg width="1.6em" height="1.6em" viewBox="0 0 16 16"><path d="M8 1a.5.5 0 0 1 .5.5V6h-1V1.5A.5.5 0 0 1 8 1zm0 14a.5.5 0 0 1-.5-.5V10h1v4.5a.5.5 0 0 1-.5.5zM2 7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V7z"/></svg>',
				title: t('section_justify_center')
			},
			{
				value: 'flex-end',
				icon: '<svg width="1.6em" height="1.6em" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M14.5 1a.5.5 0 0 0-.5.5v13a.5.5 0 0 0 1 0v-13a.5.5 0 0 0-.5-.5z"/><path d="M13 7a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7z"/></svg>',
				title: t('section_justify_end')
			}
		];

		this.justifyWrapper = this.renderJustifySettings();
		this.layoutSettings = AutomadLayout.renderSettings(this.data, data, api, config);

	}

	appendCallback() {

		this.showModal();

	}

	renderSection() {

		try {
			this.editor.destroy();
		} catch (e) { }

		this.holder.innerHTML = '';

		this.editor = Automad.blockEditor.createEditor({
			holder: this.holder,
			input: this.input,
			readOnly: true
		});

		this.applyStyleSettings(this.holder);

	}

	destroyModal() {

		try {
			this.modalEditor.destroy();
		} catch (e) {}

		var container = Automad.sectionEditor.$(this.container).find(`.${this.modalContainerCls}`);

		try {
			// Remove all tooltips that haven't been created by the initial editors. 
			Automad.sectionEditor.$('.ct:not(.init)').remove();
		} catch (e) { }

		try {
			container.remove();
		} catch (e) { }

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
			id="${this.modalDropdownId}" 
			class="uk-form" 
			data-uk-dropdown="{mode:'click', pos:'bottom-left'}"
			>
				<div class="uk-button am-section-style-button">
					<i class="uk-icon-sliders"></i>&nbsp;
					${t('edit_style')}&nbsp;
					<i class="uk-icon-caret-down"></i>
				</div>
				<div class="uk-dropdown uk-dropdown-blank">
			  		<div class="uk-form-row uk-margin-small-bottom">
						<label
						class="am-toggle-switch uk-text-truncate uk-button uk-text-left uk-width-1-1"
						data-am-toggle
						>
							${t('section_card')}
							<input id="am-section-card" type="checkbox" ${style.card == true ? 'checked' : ''}>
						</label>
					</div>
					<div class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
						<div>
							<div class="uk-form-row">
								<label
								class="am-toggle-switch uk-text-truncate uk-button uk-text-left uk-width-1-1"
								data-am-toggle
								>
									${t('section_shadow')}
									<input id="am-section-shadow" type="checkbox" ${style.shadow == true ? 'checked' : ''}>
								</label>
							</div>
						</div>
						<div>
							<div class="uk-form-row">
								<label
								class="am-toggle-switch uk-text-truncate uk-button uk-text-left uk-width-1-1"
								data-am-toggle
								>
									${t('section_match_row_height')}
									<input id="am-section-match-row-height" type="checkbox" ${style.matchRowHeight == true ? 'checked' : ''}>
								</label>
							</div>
						</div>
					</div>
					<div class="uk-grid uk-grid-width-medium-1-2 uk-margin-top-remove">
			  			<div>
							${create.label(t('section_color')).outerHTML}
							${create.colorPicker('am-section-color', style.color).outerHTML}
						</div>
						<div>
			  				${create.label(t('section_background_color')).outerHTML}
							${create.colorPicker('am-section-background-color', style.backgroundColor).outerHTML}
						</div>
					</div>
					<div class="uk-grid uk-grid-width-medium-1-3 uk-margin-top-remove">
			  			<div>
							${create.label(t('section_border_color')).outerHTML}
							${create.colorPicker('am-section-border-color', style.borderColor).outerHTML}
						</div>
						<div>
			  				${create.label(t('section_border_width')).outerHTML}
							${create.numberUnit('section-border-width', style.borderWidth, '').outerHTML}
						</div>
						<div>
			  				${create.label(t('section_border_radius')).outerHTML}
							${create.numberUnit('section-border-radius', style.borderRadius, '').outerHTML}
						</div>
					</div>
					${create.label(t('section_background_image')).outerHTML}
					<div class="am-form-icon-button-input uk-flex" data-am-select-image-field>
						<button type="button" class="uk-button">
							<i class="uk-icon-folder-open-o"></i>
						</button>
						<input 
						type="text" 
						class="am-section-background-image uk-form-controls uk-width-1-1" 
						value="${style.backgroundImage}"
						/>
					</div>
					<div class="uk-grid uk-grid-width-medium-1-2 uk-margin-top-remove">
			  			<div>
			  				${create.label(`${t('section_padding_top')} (padding top)`).outerHTML}
							${create.numberUnit('section-padding-top', style.paddingTop, '').outerHTML}
			  			</div>
						<div>
			  				${create.label(`${t('section_padding_bottom')} (padding bottom)`).outerHTML}
							${create.numberUnit('section-padding-bottom', style.paddingBottom, '').outerHTML}
			  			</div>
					</div>
				</div>
			</div>
		`;

	}

	saveStyleSettings() {

		let inputs = {},
			wrapper = this.modalWrapper;

		inputs.card = wrapper.querySelector('#am-section-card');
		inputs.shadow = wrapper.querySelector('#am-section-shadow');
		inputs.matchRowHeight = wrapper.querySelector('#am-section-match-row-height');
		inputs.color = wrapper.querySelector('.am-section-color');
		inputs.backgroundColor = wrapper.querySelector('.am-section-background-color');
		inputs.borderColor = wrapper.querySelector('.am-section-border-color');
		inputs.borderWidthNumber = wrapper.querySelector('.am-section-border-width-number');
		inputs.borderWidthUnit = wrapper.querySelector('.am-section-border-width-unit');
		inputs.borderRadiusNumber = wrapper.querySelector('.am-section-border-radius-number');
		inputs.borderRadiusUnit = wrapper.querySelector('.am-section-border-radius-unit');
		inputs.backgroundImage = wrapper.querySelector('.am-section-background-image');
		inputs.paddingTopNumber = wrapper.querySelector('.am-section-padding-top-number');
		inputs.paddingTopUnit = wrapper.querySelector('.am-section-padding-top-unit');
		inputs.paddingBottomNumber = wrapper.querySelector('.am-section-padding-bottom-number');
		inputs.paddingBottomUnit = wrapper.querySelector('.am-section-padding-bottom-unit');
		
		this.data.style = {
			card: inputs.card.checked,
			shadow: inputs.shadow.checked,
			matchRowHeight: inputs.matchRowHeight.checked,
			color: inputs.color.value,
			backgroundColor: inputs.backgroundColor.value,
			borderColor: inputs.borderColor.value,
			borderWidth: Automad.util.getNumberUnitAsString(inputs.borderWidthNumber, inputs.borderWidthUnit),
			borderRadius: Automad.util.getNumberUnitAsString(inputs.borderRadiusNumber, inputs.borderRadiusUnit),
			backgroundImage: inputs.backgroundImage.value,
			paddingTop: Automad.util.getNumberUnitAsString(inputs.paddingTopNumber, inputs.paddingTopUnit),
			paddingBottom: Automad.util.getNumberUnitAsString(inputs.paddingBottomNumber, inputs.paddingBottomUnit)
		};

		Object.keys(this.data.style).forEach((key) => {
			if (!this.data.style[key]) {
				delete this.data.style[key];
			}
		});

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

		element.classList.toggle(`justify-${this.data.justifyContent}`, this.data.justifyContent !== '');

	}

	initToggles() {

		const toggles = this.modalWrapper.querySelectorAll('label > input');

		Array.from(toggles).forEach((item) => {
			Automad.toggle.update(Automad.sectionEditor.$(item));
		});

	}

	showModal() {

		const create = Automad.util.create,
			  se = Automad.sectionEditor;

		this.destroyModal();

		this.modalWrapper = create.element('div', [this.modalContainerCls, AutomadBlockSection.cls.modalContainer]);
		this.modalWrapper.innerHTML = `
			<div id="${this.modalId}" class="uk-modal ${AutomadBlockSection.cls.modal}">
				<div class="uk-modal-dialog am-block-editor uk-form">
					<div class="uk-modal-header">
						<div class="uk-position-relative">
							${this.renderStyleSettings()}
						</div>
						<a class="uk-modal-close"><i class="uk-icon-compress"></i></a>
					</div>
					<section 
					id="${this.modalEditorId}" 
					class="am-block-editor-container ${AutomadBlockSection.cls.flex}"
					></section>
				</div>
			</div>
		`;

		this.container.appendChild(this.modalWrapper);
		this.initToggles();
		this.applyDialogSize();

		se.$(`#${this.modalDropdownId}`).on('hide.uk.dropdown', () => {
			this.saveStyleSettings();
			this.applyStyleSettings(document.getElementById(this.modalEditorId));
		});

		const modal = se.UIkit.modal(`#${this.modalId}`, { 
						modal: false, 
						bgclose: true, 
						keyboard: false 
					  });

		this.modalEditor = Automad.blockEditor.createEditor({
			holder: this.modalEditorId,
			input: this.input,
			flex: true,
			autofocus: true,
			onReady: () => {

				this.applyStyleSettings(document.getElementById(this.modalEditorId));

				modal.on('hide.uk.modal', () => {
					this.saveStyleSettings();
					se.$(this.input).trigger('change');
					this.destroyModal();
					this.renderSection();
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
			sectionData: this.getInputData()
		});

	}

	renderSettings() {

		const create = Automad.util.create,
			  wrapper = create.element('div', []);

		wrapper.appendChild(this.justifyWrapper);
		wrapper.appendChild(this.layoutSettings);

		return wrapper;
	
	}

	renderJustifySettings() {

		const create = Automad.util.create,
			  wrapper = create.element('div', ['cdx-settings']);
	
		this.justifySettings.forEach((item, index) => {

			const button = create.element('div', [this.api.styles.settingsButton]);

			this.justifySettings[index].button = button;

			button.innerHTML = item.icon;
			this.api.tooltip.onHover(button, item.title, { placement: 'top' });

			button.addEventListener('click', () => {
				this.data.justifyContent = item.value !== this.data.justifyContent ? item.value : '';
				this.toggleJustify();
			});

			wrapper.appendChild(button);

		});

		this.toggleJustify();

		return wrapper;

	}

	toggleJustify() {

		this.justifySettings.forEach((item) => {

			item.button.classList.toggle(
				this.api.styles.settingsButtonActive, 
				(item.value === this.data.justifyContent)
			);

			this.holder.classList.toggle(
				`justify-${item.value}`, 
				(item.value === this.data.justifyContent)
			);

		});

	}

}