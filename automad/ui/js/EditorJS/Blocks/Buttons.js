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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

class AutomadBlockButtons {
	static get isReadOnlySupported() {
		return true;
	}

	static get sanitize() {
		return {
			primaryText: {},
			primaryLink: false,
			primaryStyle: false,
			secondaryText: {},
			secondaryLink: false,
			secondaryStyle: false,
		};
	}

	static get toolbox() {
		return {
			title: AutomadEditorTranslation.get('buttons_toolbox'),
			icon: '<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="15px" viewBox="0 0 18 15"><path d="M16,2.359c0,0,0,0.001,0,0.002C15,0.972,13.623,0,12,0H4C1.791,0,0,1.791,0,4v5c0,1.624,0.972,3,2.362,4 c-0.001,0-0.001,0-0.002,0C2.987,14,4.377,15,6,15h8c2.209,0,4-1.791,4-4V6C18,4.377,17,2.987,16,2.359z M2,4c0-1.103,0.897-2,2-2h8 c1.103,0,2,0.897,2,2v5c0,1.103-0.897,2-2,2H4c-1.103,0-2-0.897-2-2V4z"/><path d="M6,8H5C4.171,8,3.5,7.329,3.5,6.5S4.171,5,5,5h1c0.828,0,1.5,0.671,1.5,1.5S6.828,8,6,8z"/><path d="M11,8h-1C9.172,8,8.5,7.329,8.5,6.5S9.172,5,10,5h1c0.828,0,1.5,0.671,1.5,1.5S11.828,8,11,8z"/></svg>',
		};
	}

	constructor({ data, api }) {
		var create = Automad.Util.create,
			t = AutomadEditorTranslation.get;

		this.api = api;

		this.data = {
			primaryText: data.primaryText || '',
			primaryLink: data.primaryLink || '',
			primaryStyle: data.primaryStyle || {},
			secondaryText: data.secondaryText || '',
			secondaryLink: data.secondaryLink || '',
			secondaryStyle: data.secondaryStyle || {},
			alignment: data.alignment || 'left',
		};

		this.wrapper = document.createElement('div');
		this.wrapper.classList.add(
			'uk-panel',
			'uk-panel-box',
			'am-block-buttons'
		);
		this.wrapper.innerHTML = `
			<ul class="uk-grid uk-grid-width-medium-1-2 uk-form" data-uk-grid-margin>
				<div>
					<span class="am-preview-primary uk-display-inline-block">
						${this.data.primaryText != '' ? this.data.primaryText : 'Button'}
					</span>
				</div>
				<div>
					<span class="am-preview-secondary uk-display-inline-block">
						${this.data.secondaryText != '' ? this.data.secondaryText : 'Button'}
					</span>
				</div>
			</ul>
			<hr>
			<ul class="uk-grid uk-grid-width-medium-1-2 uk-form" data-uk-grid-margin>
				<li class="primary">
					<div class="am-block-title uk-text-truncate">${t('button_primary')}</div>
					<div class="style"></div>
					${create.label(t('button_label')).outerHTML}
					${
						create.editable(
							['cdx-input', 'am-block-primary-text'],
							'',
							this.data.primaryText
						).outerHTML
					}
					${create.label(t('button_link')).outerHTML}
					<div class="am-form-icon-button-input uk-flex">
						<button type="button" class="uk-button uk-button-large">
							<i class="uk-icon-link"></i>
						</button>
						<input type="text" class="am-block-primary-link uk-form-controls uk-width-1-1" value="${
							this.data.primaryLink
						}" />
					</div>
				</li>
				<li class="secondary">
					<div class="am-block-title uk-text-truncate">${t('button_secondary')}</div>
					<div class="style"></div>
					${create.label(t('button_label')).outerHTML}
					${
						create.editable(
							['cdx-input', 'am-block-secondary-text'],
							'',
							this.data.secondaryText
						).outerHTML
					}
					${create.label(t('button_link')).outerHTML}
					<div class="am-form-icon-button-input uk-flex">
						<button type="button" class="uk-button uk-button-large">
							<i class="uk-icon-link"></i>
						</button>
						<input type="text" class="am-block-secondary-link uk-form-controls uk-width-1-1" value="${
							this.data.secondaryLink
						}" />
					</div>
				</li>
			</ul>`;

		var linkButtons = this.wrapper.querySelectorAll('button'),
			columnPrimary = this.wrapper.querySelector('li.primary .style'),
			columnSecondary = this.wrapper.querySelector('li.secondary .style');

		for (let i = 0; i < linkButtons.length; ++i) {
			api.listeners.on(linkButtons[i], 'click', function () {
				Automad.Link.click(linkButtons[i]);
			});
		}

		this.inputs = {
			primaryText: this.wrapper.querySelector('.am-block-primary-text'),
			primaryLink: this.wrapper.querySelector('.am-block-primary-link'),
			secondaryText: this.wrapper.querySelector(
				'.am-block-secondary-text'
			),
			secondaryLink: this.wrapper.querySelector(
				'.am-block-secondary-link'
			),
		};

		this.renderStyleSettings(columnPrimary, this.data.primaryStyle);
		this.renderStyleSettings(columnSecondary, this.data.secondaryStyle);
		this.renderPreview();

		this.settings = [
			{
				title: t('left'),
				name: 'left',
				icon: AutomadEditorIcons.get.alignLeft,
			},
			{
				title: t('center'),
				name: 'center',
				icon: AutomadEditorIcons.get.alignCenter,
			},
		];
	}

	render() {
		return this.wrapper;
	}

	save() {
		this.renderPreview();

		return Object.assign(this.data, {
			primaryText: this.inputs.primaryText.innerHTML,
			primaryLink: this.inputs.primaryLink.value.trim(),
			secondaryText: this.inputs.secondaryText.innerHTML,
			secondaryLink: this.inputs.secondaryLink.value.trim(),
		});
	}

	renderSettings() {
		var wrapper = document.createElement('div'),
			inner = document.createElement('div'),
			block = this;

		inner.classList.add('cdx-settings-1-2');

		this.settings
			.map(function (tune) {
				var el = document.createElement('div');

				el.innerHTML = tune.icon;
				el.classList.add(block.api.styles.settingsButton);
				el.classList.toggle(
					block.api.styles.settingsButtonActive,
					tune.name === block.data.alignment
				);

				block.api.tooltip.onHover(el, tune.title, { placement: 'top' });
				inner.appendChild(el);

				return el;
			})
			.forEach(function (element, index, elements) {
				element.addEventListener('click', function () {
					block.toggleTune(block.settings[index].name);

					elements.forEach((el, i) => {
						var name = block.settings[i].name;

						el.classList.toggle(
							block.api.styles.settingsButtonActive,
							name === block.data.alignment
						);
					});
				});
			});

		wrapper.appendChild(inner);

		return wrapper;
	}

	toggleTune(tune) {
		this.data.alignment = tune;
	}

	renderStyleSettings(parent, obj) {
		const create = Automad.Util.create,
			t = AutomadEditorTranslation.get,
			wrapper = create.element('div', [
				'uk-position-relative',
				'uk-margin-small-top',
			]),
			settings = [
				[
					{
						label: t('button_border_width'),
						name: 'borderWidth',
						type: 'numberUnit',
					},
				],
				[
					{
						label: t('button_border_radius'),
						name: 'borderRadius',
						type: 'numberUnit',
					},
				],
				[
					{
						label: t('button_padding_horizontal'),
						name: 'paddingHorizontal',
						type: 'numberUnit',
					},
				],
				[
					{
						label: t('button_padding_vertical'),
						name: 'paddingVertical',
						type: 'numberUnit',
					},
				],
				[
					{
						label: t('button_color'),
						name: 'color',
						type: 'colorPicker',
					},
					{
						label: t('button_background_color'),
						name: 'background',
						type: 'colorPicker',
					},
					{
						label: t('button_border_color'),
						name: 'borderColor',
						type: 'colorPicker',
					},
				],
				[
					{
						label: `${t('button_color')} (hover)`,
						name: 'hoverColor',
						type: 'colorPicker',
					},
					{
						label: `${t('button_background_color')} (hover)`,
						name: 'hoverBackground',
						type: 'colorPicker',
					},
					{
						label: `${t('button_border_color')} (hover)`,
						name: 'hoverBorderColor',
						type: 'colorPicker',
					},
				],
			];

		wrapper.innerHTML = `
			<div class="uk-form" data-uk-dropdown="{mode:'click',pos:'bottom-center'}">
				<div class="uk-button uk-width-1-1 uk-text-left uk-text-truncate">
					<i class="uk-icon-sliders"></i>&nbsp;
					${t('edit_style')}
				</div>
				<div class="am-style-dropdown uk-dropdown">
					<div class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin></div>
				</div>
			</div>
		`;

		const grid = wrapper.querySelector('.uk-grid');
		const dropdown = wrapper.querySelector('.am-style-dropdown');

		settings.forEach((group) => {
			const div = create.element('div', []);

			group.forEach((item) => {
				const label = create.label(item.label),
					combo = create[item.type]('', obj[item.name] || '');

				div.appendChild(label);
				div.appendChild(combo);

				Array.from(
					combo.querySelectorAll('input, select, [contenteditable]')
				).forEach((field) => {
					field.addEventListener('input', (event) => {
						var value;

						if (item.type == 'numberUnit') {
							const number =
									combo.querySelector('[contenteditable]'),
								unit = combo.querySelector('select');

							value = Automad.Util.getNumberUnitAsString(
								number,
								unit
							);
						} else {
							value = event.currentTarget.value;
						}

						if (value) {
							obj[item.name] = value;
						} else {
							delete obj[item.name];
						}
					});
				});
			});

			grid.appendChild(div);
		});

		const classLabel = create.label('Class (CSS)');
		const classInput = create.editable(['cdx-input'], '', obj.class || '');

		classInput.addEventListener('input', () => {
			obj.class = classInput.innerHTML;
		});

		dropdown.appendChild(classLabel);
		dropdown.appendChild(classInput);

		parent.appendChild(wrapper);
	}

	renderPreview() {
		['primary', 'secondary'].forEach((item) => {
			const button = this.wrapper.querySelector(`.am-preview-${item}`),
				obj = this.data[`${item}Style`],
				style = {
					borderWidth: obj.borderWidth || '1px',
					borderRadius: obj.borderRadius || '4px',
					paddingHorizontal: obj.paddingHorizontal || '2em',
					paddingVertical: obj.paddingVertical || '0.5em',
					color: obj.color || '#121212',
					background: obj.background || '#FFFFFF',
					borderColor: obj.borderColor || '#CCCCCC',
				};

			button.removeAttribute('style');

			[
				'borderWidth',
				'borderRadius',
				'color',
				'background',
				'borderColor',
			].forEach((_item) => {
				button.style[_item] = style[_item];
			});

			button.style.borderStyle = 'solid';
			button.style.padding = `${style.paddingVertical} ${style.paddingHorizontal}`;
			button.innerHTML = this.data[`${item}Text`] || 'Button';

			if (!this.data[`${item}Text`]) {
				button.style.opacity = '0.3';
			}
		});
	}
}
