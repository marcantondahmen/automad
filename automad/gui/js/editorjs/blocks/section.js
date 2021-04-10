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
			flex: 'am-block-section-flex',
			flexSettings: 'am-block-section-flex-settings'
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
			content: true, // Allow HTML tags
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
			content: data.content || {},
			style: data.style || {},
			justify: data.justify || 'flex-start',
			gap: data.gap !== undefined ? data.gap : true
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
		this.input.value = JSON.stringify(this.data.content, null, 2);
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
				icon: '<svg width="16px" height="16px" viewBox="0 0 20 20"><path d="M5,20L5,20c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h0c0.6,0,1,0.4,1,1v18C6,19.6,5.6,20,5,20z"/><path d="M9.5,16L9.5,16C8.7,16,8,15.3,8,14.5v-9C8,4.7,8.7,4,9.5,4h0C10.3,4,11,4.7,11,5.5v9C11,15.3,10.3,16,9.5,16z"/><path d="M14.5,16L14.5,16c-0.8,0-1.5-0.7-1.5-1.5v-9C13,4.7,13.7,4,14.5,4h0C15.3,4,16,4.7,16,5.5v9C16,15.3,15.3,16,14.5,16z"/></svg>',
				title: t('section_justify_start')
			},
			{
				value: 'center',
				icon: '<svg width="16px" height="16px" viewBox="0 0 20 20"><path d="M10,20L10,20c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h0c0.6,0,1,0.4,1,1v18C11,19.6,10.6,20,10,20z"/><path d="M5.5,16L5.5,16C4.7,16,4,15.3,4,14.5v-9C4,4.7,4.7,4,5.5,4h0C6.3,4,7,4.7,7,5.5v9C7,15.3,6.3,16,5.5,16z"/><path d="M14.5,16L14.5,16c-0.8,0-1.5-0.7-1.5-1.5v-9C13,4.7,13.7,4,14.5,4h0C15.3,4,16,4.7,16,5.5v9C16,15.3,15.3,16,14.5,16z"/></svg>',
				title: t('section_justify_center')
			},
			{
				value: 'flex-end',
				icon: '<svg width="16px" height="16px" viewBox="0 0 20 20"><path d="M15,20L15,20c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h0c0.6,0,1,0.4,1,1v18C16,19.6,15.6,20,15,20z"/><path d="M5.5,16L5.5,16C4.7,16,4,15.3,4,14.5v-9C4,4.7,4.7,4,5.5,4h0C6.3,4,7,4.7,7,5.5v9C7,15.3,6.3,16,5.5,16z"/><path d="M10.5,16L10.5,16C9.7,16,9,15.3,9,14.5v-9C9,4.7,9.7,4,10.5,4h0C11.3,4,12,4.7,12,5.5v9C12,15.3,11.3,16,10.5,16z"/></svg>',
				title: t('section_justify_end')
			},
			{
				value: 'space-between',
				icon: '<svg width="16px" height="16px" viewBox="0 0 20 20"><path d="M1,20L1,20c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h0c0.6,0,1,0.4,1,1v18C2,19.6,1.6,20,1,20z"/><path d="M19,20L19,20c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h0c0.6,0,1,0.4,1,1v18C20,19.6,19.6,20,19,20z"/><path d="M5.5,16L5.5,16C4.7,16,4,15.3,4,14.5v-9C4,4.7,4.7,4,5.5,4h0C6.3,4,7,4.7,7,5.5v9C7,15.3,6.3,16,5.5,16z"/><path d="M14.5,16L14.5,16c-0.8,0-1.5-0.7-1.5-1.5v-9C13,4.7,13.7,4,14.5,4h0C15.3,4,16,4.7,16,5.5v9C16,15.3,15.3,16,14.5,16z"/></svg>',
				title: t('section_justify_space_between')
			},
			{
				value: 'space-evenly',
				icon: '<svg width="16px" height="16px" viewBox="0 0 20 20"><path d="M1,20L1,20c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h0c0.6,0,1,0.4,1,1v18C2,19.6,1.6,20,1,20z"/><path d="M19,20L19,20c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h0c0.6,0,1,0.4,1,1v18C20,19.6,19.6,20,19,20z"/><path d="M7,4C6.2,4,5.5,4.7,5.5,5.5v9C5.5,15.3,6.2,16,7,16s1.5-0.7,1.5-1.5v-9C8.5,4.7,7.8,4,7,4z"/><path d="M13,4c-0.8,0-1.5,0.7-1.5,1.5v9c0,0.8,0.7,1.5,1.5,1.5s1.5-0.7,1.5-1.5v-9C14.5,4.7,13.8,4,13,4z"/></svg>',
				title: t('section_justify_space_evenly')
			},
			{
				value: 'fill-row',
				icon: '<svg width="16px" height="16px" viewBox="0 0 20 20"><path d="M1,20L1,20c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h0c0.6,0,1,0.4,1,1v18C2,19.6,1.6,20,1,20z"/><path d="M19,20L19,20c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h0c0.6,0,1,0.4,1,1v18C20,19.6,19.6,20,19,20z"/><path d="M7,16H6c-1.1,0-2-0.9-2-2V6c0-1.1,0.9-2,2-2h1c1.1,0,2,0.9,2,2v8C9,15.1,8.1,16,7,16z"/><path d="M14,16h-1c-1.1,0-2-0.9-2-2V6c0-1.1,0.9-2,2-2h1c1.1,0,2,0.9,2,2v8C16,15.1,15.1,16,14,16z"/></svg>',
				title: t('section_justify_fill_row')
			}
		];

		this.gapSettings = [
			{
				value: true,
				icon: '<svg width="16px" height="16px" viewBox="0 0 20 20"><path d="M17,0h-5c-0.6,0-1,0.4-1,1v0c0,0.6,0.4,1,1,1h5c0.6,0,1,0.4,1,1v14c0,0.6-0.4,1-1,1h-5c-0.6,0-1,0.4-1,1v0c0,0.6,0.4,1,1,1 h5c1.7,0,3-1.3,3-3V3C20,1.3,18.7,0,17,0z"/><path d="M8,18H3c-0.6,0-1-0.4-1-1V3c0-0.6,0.4-1,1-1h5c0.6,0,1-0.4,1-1v0c0-0.6-0.4-1-1-1H3C1.3,0,0,1.3,0,3v14c0,1.7,1.3,3,3,3h5 c0.6,0,1-0.4,1-1v0C9,18.4,8.6,18,8,18z"/><rect x="7" y="1" width="2" height="18"/><rect x="11" y="1" width="2" height="18"/></svg>',
				title: t('section_gap')
			},
			{
				value: false,
				icon: '<svg width="16px" height="16px" viewBox="0 0 20 20"><path d="M17,0H3C1.3,0,0,1.3,0,3v14c0,1.7,1.3,3,3,3h14c1.7,0,3-1.3,3-3V3C20,1.3,18.7,0,17,0z M18,17c0,0.6-0.4,1-1,1H3 c-0.6,0-1-0.4-1-1V3c0-0.6,0.4-1,1-1h14c0.6,0,1,0.4,1,1V17z"/><rect x="9" y="2" width="2" height="16"/></svg>',
				title: t('section_no_gap')
			}
		];

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
				  backgroundBlendMode: '',
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
					${create.label(t('section_background_image')).outerHTML}
					<div data-am-select-image-field>
						<figure></figure>
						<div>
							<input
							type="text"
							class="am-section-background-image uk-form-controls uk-width-1-1"
							value="${style.backgroundImage}"
							/>
							<button type="button" class="uk-button">
								<i class="uk-icon-folder-open-o"></i>&nbsp;
								${t('ui_browse')}
							</button>
						</div>
					</div>
					${create.label(t('section_background_blend_mode')).outerHTML}
					${create.select(['am-section-background-blend-mode', 'uk-button', 'uk-width-1-1', 'uk-text-left'], [
						'normal',
						'multiply',
						'screen',
						'overlay',
						'darken',
						'lighten',
						'color-dodge',
						'color-burn',
						'hard-light',
						'soft-light',
						'difference',
						'exclusion',
						'hue',
						'saturation',
						'color',
						'luminosity'
					], style.backgroundBlendMode).outerHTML}
					<div class="uk-grid uk-grid-width-medium-1-3 uk-margin-top-remove">
						<div>
							${create.label(t('section_border_color')).outerHTML}
							${create.colorPicker('am-section-border-color', style.borderColor).outerHTML}
						</div>
						<div>
							${create.label(t('section_border_width')).outerHTML}
							${create.numberUnit('am-section-border-width-', style.borderWidth).outerHTML}
						</div>
						<div>
							${create.label(t('section_border_radius')).outerHTML}
							${create.numberUnit('am-section-border-radius-', style.borderRadius).outerHTML}
						</div>
					</div>
					<div class="uk-grid uk-grid-width-medium-1-2 uk-margin-top-remove">
						<div>
							${create.label(`${t('section_padding_top')} (padding top)`).outerHTML}
							${create.numberUnit('am-section-padding-top-', style.paddingTop).outerHTML}
						</div>
						<div>
							${create.label(`${t('section_padding_bottom')} (padding bottom)`).outerHTML}
							${create.numberUnit('am-section-padding-bottom-', style.paddingBottom).outerHTML}
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
		inputs.backgroundBlendMode = wrapper.querySelector('.am-section-background-blend-mode');
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
			backgroundBlendMode: inputs.backgroundBlendMode.value,
			paddingTop: Automad.util.getNumberUnitAsString(inputs.paddingTopNumber, inputs.paddingTopUnit),
			paddingBottom: Automad.util.getNumberUnitAsString(inputs.paddingBottomNumber, inputs.paddingBottomUnit)
		};

		if (this.data.style.backgroundBlendMode == 'normal') {
			delete this.data.style.backgroundBlendMode;
		}

		Object.keys(this.data.style).forEach((key) => {
			if (!this.data.style[key]) {
				delete this.data.style[key];
			}
		});

	}

	applyStyleSettings(element) {

		const style = this.data.style;

		try {

			element.removeAttribute('style');
		
			if (style.backgroundImage) {
				element.style.backgroundImage = `url('${Automad.util.resolvePath(style.backgroundImage)}')`;
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
				element.style.boxShadow = '0 0.2rem 2rem rgba(0,0,0,0.15)';
			}

			if (style.matchRowHeight) {
				element.style.height = '100%';
			}

			['color', 
			'backgroundColor', 
			'backgroundBlendMode',
			'paddingTop', 
			'paddingBottom', 
			'borderColor', 
			'borderWidth', 
			'borderRadius'].forEach((item) => {

				if (style[item]) {
					element.style[item] = style[item];
				}

			});

			this.toggleSectionFlexClass(element, 'justify', this.data.justify);

		} catch (e) {}
		
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
					<div class="${AutomadBlockSection.cls.flexSettings}"></div>
					<section 
					id="${this.modalEditorId}" 
					class="am-block-editor-container ${AutomadBlockSection.cls.flex}"
					></section>
				</div>
			</div>
		`;

		this.container.appendChild(this.modalWrapper);
		Automad.selectImage.preview(this.modalWrapper.querySelector('.am-section-background-image'));

		const flexSettings = this.modalWrapper.querySelector(`.${AutomadBlockSection.cls.flexSettings}`);

		flexSettings.appendChild(this.renderFlexSettings(this.justifySettings, 'justify'));
		flexSettings.appendChild(this.renderFlexSettings(this.gapSettings, 'gap'));

		this.initToggles();
		this.applyDialogSize();

		se.$('input, select, [contenteditable]').on('change input', () => {
			setTimeout(() => {
				this.saveStyleSettings();
				this.applyStyleSettings(document.getElementById(this.modalEditorId));
			}, 50);
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
		var widthFraction = 1;

		if (this.data.widthFraction) {
			// Sanitize widthFraction string before passing it to eval().
			widthFraction = eval(this.data.widthFraction.replace(/[^\d\/]/g, ''));
		} 

		if (this.data.stretched) {
			widthFraction = 1;
		}

		dialog.style.width = `${(widthFraction * 74) + 4}rem`;
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
			content: this.getInputData()
		});

	}

	renderSettings() {

		return this.layoutSettings;

	}

	renderFlexSettings(settings, key) {

		const create = Automad.util.create,
			  wrapper = create.element('div', []);

		settings.forEach(obj => {

			const button = create.element('div', [this.api.styles.settingsButton]);

			obj.button = button;
			button.innerHTML = obj.icon;
			this.api.tooltip.onHover(button, obj.title, { placement: 'top' });
			wrapper.appendChild(button);

			button.addEventListener('click', () => {
				this.data[key] = obj.value;
				this.toggleFlexClasses(settings, key);
			});

		});

		this.toggleFlexClasses(settings, key);

		return wrapper;

	}

	toggleFlexClasses(settings, key) {

		const holder = document.getElementById(this.modalEditorId);

		settings.forEach(obj => {

			obj.button.classList.toggle(
				this.api.styles.settingsButtonActive,
				(obj.value == this.data[key])
			);

		});

		this.toggleSectionFlexClass(holder, key, this.data[key]);

	}

	toggleSectionFlexClass(element, key, value) {

		if (typeof value === 'string') {

			var regex = new RegExp(`${key}[\\-\\w]+`, 'g');

			element.className = element.className.replace(regex, '');
			element.classList.toggle(
				`${key}-${value}`,
				(value == this.data[key])
			);

		} else {

			element.classList.toggle(
				key,
				this.data[key]
			);

		}

	}

}