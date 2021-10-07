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

class AutomadBlockGallery {
	static get enableLineBreaks() {
		return true;
	}

	static get isReadOnlySupported() {
		return true;
	}

	static get sanitize() {
		return {
			globs: false,
			width: false,
		};
	}

	static get toolbox() {
		return {
			title: AutomadEditorTranslation.get('gallery_toolbox'),
			icon: '<svg width="18px" height="15px" viewBox="0 0 18 15"><path d="M14,0H4C1.791,0,0,1.791,0,4v7c0,2.209,1.791,4,4,4h10c2.209,0,4-1.791,4-4V4C18,1.791,16.209,0,14,0z M4,2h4v6H2V4 C2,2.897,2.897,2,4,2z M4,13c-1.103,0-2-0.897-2-2v-1h6v3H4z M16,11c0,1.103-0.897,2-2,2h-4V7h6V11z M16,5h-6V2h4 c1.103,0,2,0.897,2,2V5z"/></svg>',
		};
	}

	constructor({ data, api }) {
		this.api = api;

		this.data = {
			globs: data.globs || '*.jpg, *.png',
			layout: data.layout || 'vertical',
			width: data.width || '250px',
			height: data.height || '10rem',
			gap: data.gap || '5px',
			cleanBottom:
				data.cleanBottom !== undefined ? data.cleanBottom : true,
		};

		this.inputs = {};
		this.wrapper = this.drawView();
	}

	drawView() {
		const create = Automad.Util.create,
			t = AutomadEditorTranslation.get,
			wrapper = create.element('div', [
				'am-block-gallery',
				'uk-panel',
				'uk-panel-box',
			]),
			selectionWrapper = create.element('div', []),
			optionWrapper = create.element('div', []);

		this.imageSelection = new AutomadEditorImageSelection(
			this.data.globs,
			selectionWrapper
		);

		wrapper.innerHTML = `
			<div class="am-block-icon">${AutomadBlockGallery.toolbox.icon}</div>
			<div class="am-block-title">${AutomadBlockGallery.toolbox.title}</div>
			<hr>
			${create.label(t('gallery_files')).outerHTML}
		`;

		optionWrapper.innerHTML = `
			${
				create.label(t('gallery_layout'), [
					'am-block-label',
					'uk-margin-top-remove',
				]).outerHTML
			}
			<div class="am-block-gallery-layout">
				<div class="am-block-gallery-layout-tabs">
					<div class="vertical">${t('gallery_layout_vertical')}</div>
					<div class="horizontal">${t('gallery_layout_horizontal')}</div>
				</div>
				<div class="am-block-gallery-layout-options">
					<div class="vertical">
						<div>
							${create.label(t('gallery_width')).outerHTML}
							${create.numberUnit('width-', this.data.width).outerHTML}
						</div>
						<div class="uk-form-row">
							${create.label('Masonry').outerHTML}
							<label
							class="${
								this.data.cleanBottom == true ? 'uk-active' : ''
							} am-toggle-switch uk-text-truncate uk-button uk-text-left uk-width-1-1"
							data-am-toggle
							>
								${t('gallery_clean_bottom')}
								<input type="checkbox" class="clean-bottom" ${
									this.data.cleanBottom == true
										? 'checked'
										: ''
								}>
							</label>
						</div>
					</div>
					<div class="horizontal">
						<div>
							${create.label(t('gallery_height')).outerHTML}
							${create.numberUnit('height-', this.data.height).outerHTML}
						</div>
					</div>
				</div>
			</div>
			${create.label(t('gallery_gap')).outerHTML}
			${create.numberUnit('gap-', this.data.gap).outerHTML}
		`;

		wrapper.appendChild(selectionWrapper);
		wrapper.appendChild(create.element('hr', []));
		wrapper.appendChild(optionWrapper);

		['vertical', 'horizontal'].forEach((layout) => {
			const tab = optionWrapper.querySelector(
					`.am-block-gallery-layout-tabs .${layout}`
				),
				form = optionWrapper.querySelector(
					`.am-block-gallery-layout-options .${layout}`
				);

			tab.classList.toggle('active', layout == this.data.layout);
			form.classList.toggle('active', layout == this.data.layout);

			tab.addEventListener('click', () => {
				const active = optionWrapper.querySelectorAll('.active');

				Array.from(active).forEach((item) => {
					item.classList.remove('active');
				});

				tab.classList.add('active');
				form.classList.add('active');

				this.data.layout = layout;
			});
		});

		this.inputs.gapNumber = wrapper.querySelector('.gap-number');
		this.inputs.gapUnit = wrapper.querySelector('.gap-unit');
		this.inputs.widthNumber = wrapper.querySelector('.width-number');
		this.inputs.widthUnit = wrapper.querySelector('.width-unit');
		this.inputs.heightNumber = wrapper.querySelector('.height-number');
		this.inputs.heightUnit = wrapper.querySelector('.height-unit');
		this.inputs.cleanBottom = wrapper.querySelector('.clean-bottom');

		return wrapper;
	}

	render() {
		return this.wrapper;
	}

	save() {
		var getNumberUnitAsString = Automad.Util.getNumberUnitAsString;

		return Object.assign(this.data, {
			globs: this.imageSelection.save(),
			gap: getNumberUnitAsString(
				this.inputs.gapNumber,
				this.inputs.gapUnit
			),
			width: getNumberUnitAsString(
				this.inputs.widthNumber,
				this.inputs.widthUnit
			),
			height: getNumberUnitAsString(
				this.inputs.heightNumber,
				this.inputs.heightUnit
			),
			cleanBottom: this.inputs.cleanBottom.checked,
		});
	}
}
