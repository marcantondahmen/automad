/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

+(function (Automad, $, UIkit) {
	Automad.sectionEditor = {
		$: $,
		UIkit: UIkit,
	};
})((window.Automad = window.Automad || {}), jQuery, UIkit);

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
			flexSettings: 'am-block-section-flex-settings',
		};
	}

	static get ids() {
		return {
			modal: 'am-block-section-modal',
			modalEditor: 'am-block-section-modal-editor',
			modalDropdown: 'am-block-section-modal-dropdown',
		};
	}

	static get enableLineBreaks() {
		return true;
	}

	static get sanitize() {
		return {
			content: true, // Allow HTML tags
			style: false,
		};
	}

	static get toolbox() {
		return {
			title: AutomadEditorTranslation.get('section_toolbox'),
			icon: '<svg width="18px" height="18px" viewBox="0 0 18 18"><path d="M14,0H4C1.8,0,0,1.8,0,4v10c0,2.2,1.8,4,4,4h10c2.2,0,4-1.8,4-4V4C18,1.8,16.2,0,14,0z M3,4c0-0.6,0.4-1,1-1h7 c0.6,0,1,0.4,1,1v2c0,0.6-0.4,1-1,1H4C3.4,7,3,6.6,3,6V4z M9,15H4c-0.6,0-1-0.4-1-1c0-0.6,0.4-1,1-1h5c0.6,0,1,0.4,1,1 C10,14.6,9.6,15,9,15z M14,11H4c-0.6,0-1-0.4-1-1c0-0.6,0.4-1,1-1h10c0.6,0,1,0.4,1,1C15,10.6,14.6,11,14,11z"/></svg>',
		};
	}

	constructor({ data, api }) {
		var create = Automad.Util.create,
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
			justify: data.justify || 'start',
			gap: data.gap !== undefined ? data.gap : '',
			minBlockWidth:
				data.minBlockWidth !== undefined ? data.minBlockWidth : '',
		};

		this.container = document.querySelector('body');

		this.wrapper = create.element('div', [
			'am-block-editor-container',
			AutomadBlockSection.cls.block,
		]);
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
				value: 'start',
				icon: '<svg width="18px" height="18px" viewBox="0 0 20 20"><path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18.5,16c0,1.4-1.1,2.5-2.5,2.5H4 c-1.4,0-2.5-1.1-2.5-2.5V4c0-1.4,1.1-2.5,2.5-2.5h12c1.4,0,2.5,1.1,2.5,2.5V16z"/><path d="M6,17H4c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C7,16.6,6.6,17,6,17z"/><path d="M11,17H9c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C12,16.6,11.6,17,11,17z"/></svg>',
				title: t('section_justify_start'),
			},
			{
				value: 'center',
				icon: '<svg width="18px" height="18px" viewBox="0 0 20 20"><path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18.5,16c0,1.4-1.1,2.5-2.5,2.5H4 c-1.4,0-2.5-1.1-2.5-2.5V4c0-1.4,1.1-2.5,2.5-2.5h12c1.4,0,2.5,1.1,2.5,2.5V16z"/><path d="M8.5,17h-2c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C9.5,16.6,9.1,17,8.5,17z"/><path d="M13.5,17h-2c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C14.5,16.6,14.1,17,13.5,17z"/></svg>',
				title: t('section_justify_center'),
			},
			{
				value: 'end',
				icon: '<svg width="18px" height="18px" viewBox="0 0 20 20"><path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18.5,16c0,1.4-1.1,2.5-2.5,2.5H4 c-1.4,0-2.5-1.1-2.5-2.5V4c0-1.4,1.1-2.5,2.5-2.5h12c1.4,0,2.5,1.1,2.5,2.5V16z"/><path d="M11,17H9c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C12,16.6,11.6,17,11,17z"/><path d="M16,17h-2c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C17,16.6,16.6,17,16,17z"/></svg>',
				title: t('section_justify_end'),
			},
			{
				value: 'space-between',
				icon: '<svg width="18px" height="18px" viewBox="0 0 20 20"><path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18.5,16c0,1.4-1.1,2.5-2.5,2.5H4 c-1.4,0-2.5-1.1-2.5-2.5V4c0-1.4,1.1-2.5,2.5-2.5h12c1.4,0,2.5,1.1,2.5,2.5V16z"/><path d="M6,17H4c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C7,16.6,6.6,17,6,17z"/><path d="M16,17h-2c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C17,16.6,16.6,17,16,17z"/></svg>',
				title: t('section_justify_space_between'),
			},
			{
				value: 'space-evenly',
				icon: '<svg width="18px" height="18px" viewBox="0 0 20 20"><path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18.5,16c0,1.4-1.1,2.5-2.5,2.5H4 c-1.4,0-2.5-1.1-2.5-2.5V4c0-1.4,1.1-2.5,2.5-2.5h12c1.4,0,2.5,1.1,2.5,2.5V16z"/><path d="M7.5,17h-2c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C8.5,16.6,8.1,17,7.5,17z"/><path d="M14.5,17h-2c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h2c0.6,0,1,0.4,1,1v12C15.5,16.6,15.1,17,14.5,17z"/></svg>',
				title: t('section_justify_space_evenly'),
			},
			{
				value: 'fill-row',
				icon: '<svg width="18px" height="18px" viewBox="0 0 20 20"><path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18.5,16c0,1.4-1.1,2.5-2.5,2.5H4 c-1.4,0-2.5-1.1-2.5-2.5V4c0-1.4,1.1-2.5,2.5-2.5h12c1.4,0,2.5,1.1,2.5,2.5V16z"/><path d="M8.5,17H4c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1h4.5c0.6,0,1,0.4,1,1v12C9.5,16.6,9.1,17,8.5,17z"/><path d="M16,17h-4.5c-0.6,0-1-0.4-1-1V4c0-0.6,0.4-1,1-1H16c0.6,0,1,0.4,1,1v12C17,16.6,16.6,17,16,17z"/></svg>',
				title: t('section_justify_fill_row'),
			},
		];

		this.sizeSettings = {
			gap: {
				name: 'gap',
				title: t('section_gap'),
				icon: '<svg width="18px" height="18px" viewBox="0 0 20 20"><rect x="8.5" y="1" width="3" height="18"/><path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18.5,16c0,1.4-1.1,2.5-2.5,2.5H4 c-1.4,0-2.5-1.1-2.5-2.5V4c0-1.4,1.1-2.5,2.5-2.5h12c1.4,0,2.5,1.1,2.5,2.5V16z"/></svg>',
			},
			minBlockWidth: {
				name: 'minBlockWidth',
				title: t('section_min_block_width'),
				icon: '<svg width="18px" height="18px" viewBox="0 0 20 20"><path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18.5,16c0,1.4-1.1,2.5-2.5,2.5H4 c-1.4,0-2.5-1.1-2.5-2.5V4c0-1.4,1.1-2.5,2.5-2.5h12c1.4,0,2.5,1.1,2.5,2.5V16z"/><rect x="4" y="9" width="12" height="2"/><path d="M4.9,6.9L2.1,9.6C2,9.8,2,10.2,2.1,10.4l2.8,2.8c0.3,0.3,0.9,0.1,0.9-0.4V7.2C5.8,6.8,5.3,6.5,4.9,6.9z"/><path d="M15.1,6.9l2.8,2.8c0.2,0.2,0.2,0.5,0,0.7l-2.8,2.8c-0.3,0.3-0.9,0.1-0.9-0.4V7.2C14.2,6.8,14.7,6.5,15.1,6.9z"/></svg>',
			},
		};
	}

	appendCallback() {
		this.showModal();
	}

	renderSection() {
		try {
			this.editor.destroy();
		} catch (e) {}

		this.holder.innerHTML = '';

		this.editor = Automad.BlockEditor.createEditor({
			holder: this.holder,
			input: this.input,
			readOnly: true,
		});

		this.applyStyleSettings(this.holder);
	}

	destroyModal() {
		try {
			this.modalEditor.destroy();
		} catch (e) {}

		var container = Automad.sectionEditor
			.$(this.container)
			.find(`.${this.modalContainerCls}`);

		try {
			// Remove all tooltips that haven't been created by the initial editors.
			Automad.sectionEditor.$('.ct:not(.init)').remove();
		} catch (e) {}

		try {
			container.remove();
		} catch (e) {}
	}

	renderStyleSettings() {
		const create = Automad.Util.create,
			t = AutomadEditorTranslation.get,
			style = Object.assign(
				{
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
					paddingBottom: '',
					overflowHidden: false,
					class: '',
				},
				this.data.style
			);

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
							<input id="am-section-card" type="checkbox" ${
								style.card == true ? 'checked' : ''
							}>
						</label>
					</div>
					<div class="uk-form-row uk-margin-small-bottom">
						<label
						class="am-toggle-switch uk-text-truncate uk-button uk-text-left uk-width-1-1"
						data-am-toggle
						>
							${t('section_overflow_hidden')}
							<input id="am-section-overflow" type="checkbox" ${
								style.overflowHidden == true ? 'checked' : ''
							}>
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
									<input id="am-section-shadow" type="checkbox" ${
										style.shadow == true ? 'checked' : ''
									}>
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
									<input id="am-section-match-row-height" type="checkbox" ${
										style.matchRowHeight == true
											? 'checked'
											: ''
									}>
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
							${
								create.colorPicker(
									'am-section-background-color',
									style.backgroundColor
								).outerHTML
							}
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
					${
						create.select(
							[
								'am-section-background-blend-mode',
								'uk-button',
								'uk-width-1-1',
								'uk-text-left',
							],
							[
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
								'luminosity',
							],
							style.backgroundBlendMode
						).outerHTML
					}
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
							${
								create.numberUnit(
									'am-section-padding-bottom-',
									style.paddingBottom
								).outerHTML
							}
						</div>
					</div>
					${create.label('Class (CSS)').outerHTML}
					${create.editable(['cdx-input', 'am-section-class'], '', style.class).outerHTML}
				</div>
			</div>
		`;
	}

	saveStyleSettings() {
		let inputs = {},
			wrapper = this.modalWrapper;

		inputs.card = wrapper.querySelector('#am-section-card');
		inputs.overflowHidden = wrapper.querySelector('#am-section-overflow');
		inputs.shadow = wrapper.querySelector('#am-section-shadow');
		inputs.matchRowHeight = wrapper.querySelector(
			'#am-section-match-row-height'
		);
		inputs.color = wrapper.querySelector('.am-section-color');
		inputs.backgroundColor = wrapper.querySelector(
			'.am-section-background-color'
		);
		inputs.borderColor = wrapper.querySelector('.am-section-border-color');
		inputs.borderWidthNumber = wrapper.querySelector(
			'.am-section-border-width-number'
		);
		inputs.borderWidthUnit = wrapper.querySelector(
			'.am-section-border-width-unit'
		);
		inputs.borderRadiusNumber = wrapper.querySelector(
			'.am-section-border-radius-number'
		);
		inputs.borderRadiusUnit = wrapper.querySelector(
			'.am-section-border-radius-unit'
		);
		inputs.backgroundImage = wrapper.querySelector(
			'.am-section-background-image'
		);
		inputs.backgroundBlendMode = wrapper.querySelector(
			'.am-section-background-blend-mode'
		);
		inputs.paddingTopNumber = wrapper.querySelector(
			'.am-section-padding-top-number'
		);
		inputs.paddingTopUnit = wrapper.querySelector(
			'.am-section-padding-top-unit'
		);
		inputs.paddingBottomNumber = wrapper.querySelector(
			'.am-section-padding-bottom-number'
		);
		inputs.paddingBottomUnit = wrapper.querySelector(
			'.am-section-padding-bottom-unit'
		);
		inputs.class = wrapper.querySelector('.am-section-class');

		this.data.style = {
			card: inputs.card.checked,
			overflowHidden: inputs.overflowHidden.checked,
			shadow: inputs.shadow.checked,
			matchRowHeight: inputs.matchRowHeight.checked,
			color: inputs.color.value,
			backgroundColor: inputs.backgroundColor.value,
			borderColor: inputs.borderColor.value,
			borderWidth: Automad.Util.getNumberUnitAsString(
				inputs.borderWidthNumber,
				inputs.borderWidthUnit
			),
			borderRadius: Automad.Util.getNumberUnitAsString(
				inputs.borderRadiusNumber,
				inputs.borderRadiusUnit
			),
			backgroundImage: inputs.backgroundImage.value,
			backgroundBlendMode: inputs.backgroundBlendMode.value,
			paddingTop: Automad.Util.getNumberUnitAsString(
				inputs.paddingTopNumber,
				inputs.paddingTopUnit
			),
			paddingBottom: Automad.Util.getNumberUnitAsString(
				inputs.paddingBottomNumber,
				inputs.paddingBottomUnit
			),
			class: inputs.class.innerHTML,
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
				element.style.backgroundImage = `url('${Automad.Util.resolvePath(
					style.backgroundImage
				)}')`;
				element.style.backgroundPosition = '50% 50%';
				element.style.backgroundSize = 'cover';
			}

			if (
				style.backgroundImage ||
				style.borderColor ||
				style.backgroundColor ||
				style.shadow ||
				style.borderWidth
			) {
				element.style.padding = '1rem';
			}

			if (style.borderWidth && !style.borderWidth.startsWith('0')) {
				element.style.borderStyle = 'solid';
			}

			if (style.shadow) {
				element.style.boxShadow = '0 0.2rem 2rem rgba(0,0,0,0.15)';
			}

			if (style.matchRowHeight) {
				element.style.height = '100%';
			}

			[
				'color',
				'backgroundColor',
				'backgroundBlendMode',
				'paddingTop',
				'paddingBottom',
				'borderColor',
				'borderWidth',
				'borderRadius',
			].forEach((item) => {
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
			Automad.Toggle.update(Automad.sectionEditor.$(item));
		});
	}

	showModal() {
		const create = Automad.Util.create,
			se = Automad.sectionEditor;

		this.destroyModal();

		this.modalWrapper = create.element('div', [
			this.modalContainerCls,
			AutomadBlockSection.cls.modalContainer,
		]);
		this.modalWrapper.innerHTML = `
			<div id="${this.modalId}" class="uk-modal ${AutomadBlockSection.cls.modal}">
				<div class="uk-modal-dialog am-block-editor uk-form am-text">
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
		Automad.SelectImage.preview(
			this.modalWrapper.querySelector('.am-section-background-image')
		);

		const styleSettings = this.modalWrapper.querySelector(
			`#${this.modalDropdownId}`
		);

		const styleInputs = styleSettings.querySelectorAll(
			'input, select, [contenteditable]'
		);

		Array.from(styleInputs).forEach((element) => {
			const update = () => {
				setTimeout(() => {
					this.saveStyleSettings();
					this.applyStyleSettings(
						document.getElementById(this.modalEditorId)
					);
				}, 100);
			};

			element.addEventListener('input', update);
			element.addEventListener('keydown', update);
			element.addEventListener('change', update);
		});

		const flexSettings = this.modalWrapper.querySelector(
			`.${AutomadBlockSection.cls.flexSettings}`
		);

		flexSettings.appendChild(
			this.renderFlexSettings(this.justifySettings, 'justify')
		);
		flexSettings.appendChild(
			this.renderSizeSettings(this.sizeSettings.gap)
		);
		flexSettings.appendChild(
			this.renderSizeSettings(this.sizeSettings.minBlockWidth)
		);

		this.initToggles();

		const modal = se.UIkit.modal(`#${this.modalId}`, {
			modal: false,
			bgclose: true,
			keyboard: false,
		});

		this.modalEditor = Automad.BlockEditor.createEditor({
			holder: this.modalEditorId,
			input: this.input,
			autofocus: true,
			flex: true,
			onReady: () => {
				this.applyStyleSettings(
					document.getElementById(this.modalEditorId)
				);

				modal.on('hide.uk.modal', () => {
					this.saveStyleSettings();
					se.$(this.input).trigger('change');
					this.destroyModal();
					this.renderSection();
				});
			},
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

		const LegacyData = new AutomadLegacyData(data);
		return LegacyData.convert(data);
	}

	save() {
		return Object.assign(this.data, {
			content: this.getInputData(),
		});
	}

	renderFlexSettings(settings, key) {
		const create = Automad.Util.create,
			wrapper = create.element('div', []);

		settings.forEach((obj) => {
			const button = create.element('div', [
				this.api.styles.settingsButton,
			]);

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

		settings.forEach((obj) => {
			obj.button.classList.toggle(
				this.api.styles.settingsButtonActive,
				obj.value == this.data[key]
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
				value == this.data[key]
			);
		} else {
			element.classList.toggle(key, this.data[key]);
		}
	}

	renderSizeSettings(option) {
		const create = Automad.Util.create,
			wrapper = create.element('div', []),
			inner = create.element('span', ['am-section-flex-size-settings']);

		inner.innerHTML = option.icon;
		inner.appendChild(create.numberUnit('', this.data[option.name]));
		wrapper.appendChild(inner);

		const input = inner.querySelector('.number'),
			unit = inner.querySelector('.unit');

		[input, unit].forEach((field) => {
			field.addEventListener('input', () => {
				this.data[option.name] = Automad.Util.getNumberUnitAsString(
					input,
					unit
				);
			});
		});

		this.api.tooltip.onHover(inner, option.title, { placement: 'top' });

		return wrapper;
	}
}
