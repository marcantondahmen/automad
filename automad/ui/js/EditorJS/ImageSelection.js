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

class AutomadEditorImageSelection {
	constructor(csv, container) {
		const images = csv.replace(/\s/g, '').split(',');

		this.container = container;
		this.render(images);
	}

	render(images) {
		const fields = document.createElement('div'),
			button = document.createElement('div');

		images.forEach((item) => {
			fields.appendChild(this.renderItem(item));
		});

		fields.classList.add('am-sortable');

		button.classList.add('uk-button');
		button.innerHTML = `<i class="uk-icon-plus"></i>&nbsp; ${AutomadEditorTranslation.get(
			'ui_add'
		)}`;

		button.addEventListener('click', () => {
			const item = this.renderItem(''),
				input = item.querySelector('input');

			fields.appendChild(item);

			Automad.SelectImage.dialog(false, false, (value) => {
				input.value = value;
				input.dispatchEvent(new Event('keydown', { bubbles: true }));
			});
		});

		this.container.appendChild(fields);
		this.container.appendChild(button);

		Sortable.create(fields, {
			group: 'automad',
			animation: 200,
			draggable: '.am-item',
			handle: '.am-drag, figure',
			forceFallback: true,
			ghostClass: 'sortable-ghost',
			chosenClass: 'sortable-chosen',
			dragClass: 'sortable-drag',
		});
	}

	renderItem(value) {
		const wrapper = Automad.Util.create.element('div', ['am-item']);

		wrapper.setAttribute('data-am-select-image-field', '');
		wrapper.innerHTML = `
			<figure></figure>
			<div>
				<input type="text" class="uk-form-controls uk-width-1-1" value="${this.sanitize(
					value
				)}" />
				<button type="button" class="uk-button">
					<i class="uk-icon-folder-open-o"></i>&nbsp;
					${AutomadEditorTranslation.get('ui_browse')}
				</button>
			</div>
			<span class="am-delete uk-button"><i class="uk-icon-trash-o"></i></span>
			<span class="am-drag"><i class="uk-icon-arrows-v"></i></span>
		`;

		wrapper.querySelector('.am-delete').addEventListener('click', () => {
			wrapper.remove();
		});

		Automad.SelectImage.preview(wrapper.querySelector('input'));

		return wrapper;
	}

	save() {
		const inputs = this.container.querySelectorAll('input'),
			images = [];

		Array.from(inputs).forEach((input) => {
			const value = this.sanitize(input.value);

			if (value.length > 0) {
				images.push(value);
			}
		});

		return images.join(', ');
	}

	sanitize(str) {
		return str.replace(/[^\w\-\/\:\?\=\&\+\*\.]/g, '');
	}
}
