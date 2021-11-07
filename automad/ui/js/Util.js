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
 * Copyright (c) 2016-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

/*
 * Helper tools.
 */

+(function (Automad, $) {
	Automad.Util = {
		create: {
			element: function (tag, cls) {
				var element = document.createElement(tag);

				for (var i = 0; i < cls.length; i++) {
					element.classList.add(cls[i]);
				}

				return element;
			},

			colorPicker: function (cls, value) {
				const wrapper = Automad.Util.create.element('div', ['uk-flex']);

				wrapper.dataset.amColorpicker = true;
				wrapper.innerHTML = `
					<input type="color" value="${value}">
					<input type="text" class="${cls} am-u-form-controls am-u-width-1-1" value="${value}">
				`;

				return wrapper;
			},

			editable: function (cls, placeholder, value) {
				var span = Automad.Util.create.element('span', cls);

				span.contentEditable = true;
				span.dataset.placeholder = placeholder;
				span.innerHTML = value;

				return span;
			},

			label: function (text, cls = ['am-block-label']) {
				var label = Automad.Util.create.element('label', cls);

				label.textContent = text;

				return label;
			},

			numberUnit: function (clsPrefix, value) {
				const create = Automad.Util.create,
					wrapper = create.element('div', ['am-form-input-group']),
					units = ['px', 'em', 'rem', '%', 'vw', 'vh'];

				value = String(value);

				var number = value.replace(/([^\d\.]+)/g, ''),
					unit =
						value.replace(/.+?(px|em|rem|%|vh|vw)/g, '$1') || 'px';

				wrapper.innerHTML = `
					${
						create.editable(
							[
								'cdx-input',
								'uk-text-right',
								`${clsPrefix}number`,
							],
							'',
							number
						).outerHTML
					}
					${create.select(['cdx-input', `${clsPrefix}unit`], units, unit).outerHTML}
				`;

				return wrapper;
			},

			select: function (cls, options, selected) {
				var select = Automad.Util.create.element('select', cls),
					optionMarkup = [];

				options.forEach(function (value) {
					let html,
						text,
						selectedAttr = '';

					if (value == selected) {
						selectedAttr = ' selected';
					}

					text = value
						.replace(/^[\/\\\\]/g, '')
						.replace(/[\/\\\\]/g, ' / ')
						.replace(/_/g, ' ')
						.replace('.php', '');

					html = `<option value="${value}"${selectedAttr}>${text}</option>`;
					optionMarkup.push(html);
				});

				select.innerHTML = optionMarkup.join('');

				return select;
			},
		},

		// Convert data attribute string in dataAPI string.
		// For example "data-am-controller" gets converted into "amController".
		dataCamelCase: function (str) {
			str = str.replace(/data-/g, '');
			str = str.replace(/\-[a-z]/g, function (s) {
				return s.charAt(1).toUpperCase();
			});

			return str;
		},

		// Format bytes.
		formatBytes: function (bytes) {
			if (typeof bytes !== 'number') {
				return '';
			}

			if (bytes >= 1000000000) {
				return (bytes / 1000000000).toFixed(2) + ' gb';
			}

			if (bytes >= 1000000) {
				return (bytes / 1000000).toFixed(2) + ' mb';
			}

			return (bytes / 1000).toFixed(2) + ' kb';
		},

		getNumberUnitAsString: function (numberInput, unitSelect) {
			const number = Automad.Util.stripNbsp(
					numberInput.textContent
				).trim(),
				unit = unitSelect.value;

			if (number.length) {
				return `${number}${unit}`;
			}

			return '';
		},

		resolvePath: function (path) {
			var pagePath = $('[data-am-path]').data('amPath'),
				$inPage = $('[data-am-base-url]'),
				baseUrl = '.';

			if ($inPage.length) {
				baseUrl = $inPage.data('amBaseUrl');
			}

			if (pagePath === undefined) {
				pagePath = '';
			}

			if (path.includes('://')) {
				return path;
			}

			if (path.startsWith('/')) {
				return baseUrl + path;
			} else {
				return baseUrl + '/pages' + pagePath + path;
			}
		},

		resolveUrl: function (url) {
			var pageUrl = $('[data-am-url]').data('amUrl');

			if (pageUrl === undefined) {
				pageUrl = '';
			}

			if (url.includes('://')) {
				return url;
			}

			if (url.startsWith('/')) {
				return '.' + url;
			} else {
				return '.' + pageUrl + '/' + url;
			}
		},

		stripNbsp: function (str) {
			return str.replace(/\&nbsp;/g, ' ').trim();
		},
	};
})((window.Automad = window.Automad || {}), jQuery);
