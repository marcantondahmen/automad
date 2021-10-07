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

+(function (Automad, $, UIkit) {
	Automad.layout = {
		$: $,
		UIkit: UIkit,
	};
})((window.Automad = window.Automad || {}), jQuery, UIkit);

class AutomadLayoutButton {
	constructor(api, data, wrapper, options) {
		this.options = Object.assign(
			{
				title: '',
				icon: '',
				name: '',
				value: '',
				buttonsClearRegex: '',
				clearDataKeys: [],
			},
			options
		);

		this.data = data;
		this.api = api;
		this.wrapper = wrapper;
		this.cls = api.styles.settingsButton;
		this.clsActive = api.styles.settingsButtonActive;
		this.button = Automad.Util.create.element('div', [
			this.cls,
			this.options.name,
		]);
		this.button.innerHTML = this.options.icon;

		api.tooltip.onHover(this.button, this.options.title, {
			placement: 'top',
		});

		this.button.addEventListener('click', () => {
			this.onClickHandler();
		});

		this.onInit();
	}

	onInit() {
		this.button.classList.toggle(
			this.clsActive,
			this.data[this.options.name] == this.options.value
		);
	}

	onClickHandler() {
		const buttons = this.wrapper.querySelectorAll(`.${this.cls}`),
			block = this.api.blocks.getBlockByIndex(
				this.api.blocks.getCurrentBlockIndex()
			).holder;

		Array.from(buttons).forEach((_button) => {
			if (_button.className.match(this.options.buttonsClearRegex)) {
				_button.classList.remove(this.clsActive);
			}
		});

		this.options.clearDataKeys.forEach((key) => {
			this.data[key] = false;
		});

		this.button.classList.add(this.clsActive);
		this.data[this.options.name] = this.options.value;

		AutomadLayout.toggleBlockClasses(block, this.data);
	}

	get() {
		return this.button;
	}
}

class AutomadLayoutResetButton extends AutomadLayoutButton {
	onInit() {
		this.button.classList.toggle(
			this.clsActive,
			!this.data.width && !this.data.stretched
		);
	}

	onClickHandler() {
		const buttons = this.wrapper.querySelectorAll(`.${this.cls}`),
			block = this.api.blocks.getBlockByIndex(
				this.api.blocks.getCurrentBlockIndex()
			).holder;

		Array.from(buttons).forEach((_button) => {
			if (_button.className.match(this.options.buttonsClearRegex)) {
				_button.classList.remove(this.clsActive);
			}
		});

		this.options.clearDataKeys.forEach((key) => {
			this.data[key] = false;
		});

		this.button.classList.add(this.clsActive);
		AutomadLayout.toggleBlockClasses(block, this.data);
	}
}

class AutomadLayout {
	static get options() {
		const t = AutomadEditorTranslation.get;

		return {
			reset: {
				title: t('layout_default'),
				name: 'reset',
				icon: '<svg width="24px" height="16px" viewBox="0 0 30 20"><path d="M27,0H3C1.3,0,0,1.3,0,3v14c0,1.7,1.3,3,3,3h24c1.7,0,3-1.3,3-3V3C30,1.3,28.7,0,27,0z M2,17V3c0-0.6,0.4-1,1-1h5v16H3 C2.4,18,2,17.6,2,17z M28,17c0,0.6-0.4,1-1,1h-5V2h5c0.6,0,1,0.4,1,1V17z"/></svg>',
			},
			stretch: {
				title: t('layout_stretch'),
				name: 'stretched',
				icon: '<svg width="24px" height="16px" viewBox="0 0 30 20"><path d="M27,0H3C1.3,0,0,1.3,0,3v14c0,1.7,1.3,3,3,3h24c1.7,0,3-1.3,3-3V3C30,1.3,28.7,0,27,0z M25.9,10.9l-5,5 c-0.2,0.2-0.6,0.4-0.9,0.4s-0.6-0.1-0.9-0.4c-0.5-0.5-0.5-1.3,0-1.8l2.9-2.9H8l2.9,2.9c0.5,0.5,0.5,1.3,0,1.8 c-0.2,0.2-0.6,0.4-0.9,0.4s-0.6-0.1-0.9-0.4l-5-5c-0.5-0.5-0.5-1.3,0-1.8l5-5c0.5-0.5,1.3-0.5,1.8,0s0.5,1.3,0,1.8L8,8.8h14 l-2.9-2.9c-0.5-0.5-0.5-1.3,0-1.8s1.3-0.5,1.8,0l5,5C26.4,9.6,26.4,10.4,25.9,10.9z"/></svg>',
				value: true,
			},
			width: [
				{
					title: t('layout_width') + ': 1⁄4',
					name: 'width',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H5V2h11 c1.1,0,2,0.9,2,2V16z"/>',
					value: '1/4',
				},
				{
					title: t('layout_width') + ': 1⁄3',
					name: 'width',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H7V2h9 c1.1,0,2,0.9,2,2V16z"/>',
					value: '1/3',
				},
				{
					title: t('layout_width') + ': 1⁄2',
					name: 'width',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2h-6V2h6 c1.1,0,2,0.9,2,2V16z"/>',
					value: '1/2',
				},
				{
					title: t('layout_width') + ': 2⁄3',
					name: 'width',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2h-3V2h3 c1.1,0,2,0.9,2,2V16z"/>',
					value: '2/3',
				},
				{
					title: t('layout_width') + ': 3⁄4',
					name: 'width',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2h-1V2h1 c1.1,0,2,0.9,2,2V16z"/>',
					value: '3/4',
				},
				{
					title: t('layout_width') + ': 1⁄1',
					name: 'width',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z"/>',
					value: '1/1',
				},
			],
		};
	}

	constructor(editor) {
		const holder = editor.configuration.holder;

		this.editor = editor;
		this.holder =
			typeof holder === 'string'
				? document.getElementById(holder)
				: holder;

		this.initUndoHandler();
	}

	alignButton() {
		let editor = this.editor;

		try {
			var editorId = editor.configuration.holder,
				container = document.getElementById(editorId),
				button = container.querySelector(
					`.${AutomadEditorConfig.cls.actionsButton}`
				),
				blockId = editor.blocks.getCurrentBlockIndex(),
				block = editor.blocks.getBlockByIndex(blockId).holder,
				blockContent = block.querySelector(
					`.${AutomadEditorConfig.cls.blockContent}`
				);

			button.style.transform = 'translate3d(0,0,0)';

			var blockRight = blockContent.getBoundingClientRect().right,
				buttonRight = button.getBoundingClientRect().right,
				blockTop = blockContent.getBoundingClientRect().top,
				buttonTop = button.getBoundingClientRect().top,
				right = buttonRight - blockRight,
				top = blockTop - buttonTop;

			button.style.transform = `translate3d(${right * -1}px,${top}px,0)`;
		} catch (e) {}
	}

	applyLayout(data = false, callback = false) {
		var editor = this.editor,
			apply = (data) => {
				for (var i = 0; i < editor.blocks.getBlocksCount(); i++) {
					var block = editor.blocks.getBlockByIndex(i).holder;

					if (data.blocks !== undefined) {
						if (data.blocks[i] !== undefined) {
							if (data.blocks[i].tunes !== undefined) {
								if (data.blocks[i].tunes.layout !== undefined) {
									AutomadLayout.toggleBlockClasses(
										block,
										data.blocks[i].tunes.layout
									);
								}
							}
						}
					}
				}

				if (typeof callback == 'function') {
					callback();
				}
			};

		if (data) {
			apply(data);
		} else {
			try {
				editor.save().then((data) => {
					apply(data);
				});
			} catch (e) {}
		}
	}

	settingsButtonObserver() {
		var editor = this.editor,
			editorId = editor.configuration.holder,
			layout = this,
			alignButton = function () {
				layout.alignButton();
			};

		Automad.layout
			.$(document)
			.on(
				'mousedown',
				`#${editorId} .${AutomadEditorConfig.cls.block}, #${editorId} .${AutomadEditorConfig.cls.redactor}`,
				function () {
					setTimeout(alignButton, 50);
				}
			);

		Automad.layout
			.$(document)
			.on(
				'mousedown click',
				`#${editorId} .${AutomadEditorConfig.cls.settingsLayout} div, .${AutomadEditorConfig.cls.settingsButton}`,
				alignButton
			);

		Automad.layout
			.$(document)
			.on(
				'dragend mouseup',
				`#${editorId} .${AutomadEditorConfig.cls.actionsButton} div`,
				alignButton
			);
	}

	initPasteHandler() {
		const redactor = document.querySelector(
			`#${this.editor.configuration.holder} > div > div`
		);

		redactor.addEventListener('paste', () => {
			setTimeout(() => {
				this.applyLayout();
			}, 50);
		});
	}

	initUndoHandler() {
		this.holder.addEventListener('undo', () => {
			this.applyLayout();
			this.alignButton();
		});
	}

	static toggleBlockClasses(element, data) {
		element.className = element.className.replace(/width[\d\-]+/g, '');
		element.classList.toggle(
			'stretched',
			data.stretched !== undefined && data.stretched
		);

		if (data.width !== undefined && data.width) {
			element.classList.add(`width-${data.width.replace('/', '-')}`);
		}
	}

	static renderSettings(data, saved, api, config) {
		const allowStretching = config.allowStretching || false;
		const flex = config.flex || false;
		const element = Automad.Util.create.element;
		const options = AutomadLayout.options;

		const wrapper = element('div', [
			AutomadEditorConfig.cls.settingsLayout,
		]);

		if (flex || allowStretching) {
			const mainWrapper = element('div', ['cdx-settings-1-2']);
			const widthWrapper = element('div', ['cdx-settings']);

			if (allowStretching) {
				data.stretched = saved.stretched || false;
			}

			if (flex) {
				data.width = saved.width || false;
			}

			const resetButton = new AutomadLayoutResetButton(
				api,
				data,
				wrapper,
				Object.assign(options.reset, {
					icon: options.reset.icon,
					buttonsClearRegex: /(width|stretched)/g,
					clearDataKeys: ['stretched', 'width'],
				})
			);

			mainWrapper.appendChild(resetButton.get());

			if (allowStretching) {
				const stretchButton = new AutomadLayoutButton(
					api,
					data,
					wrapper,
					Object.assign(options.stretch, {
						icon: options.stretch.icon,
						buttonsClearRegex: /(width|reset)/g,
						clearDataKeys: ['width'],
					})
				);

				mainWrapper.appendChild(stretchButton.get());
			} else {
				let stretchButton = element('div', [
					api.styles.settingsButton,
					'disabled',
				]);

				stretchButton.innerHTML = options.stretch.icon;

				stretchButton.addEventListener('click', function (e) {
					e.preventDefault();
					e.stopPropagation();
					return false;
				});

				mainWrapper.appendChild(stretchButton);
			}

			wrapper.appendChild(mainWrapper);

			if (flex) {
				options.width.forEach(function (option) {
					const button = new AutomadLayoutButton(
						api,
						data,
						wrapper,
						Object.assign(option, {
							icon: AutomadLayout.icon(option.icon),
							buttonsClearRegex: /(width|stretched|reset)/g,
							clearDataKeys: ['stretched'],
						})
					);

					widthWrapper.appendChild(button.get());
				});

				wrapper.appendChild(widthWrapper);
			}
		}

		return wrapper;
	}

	static icon(innerSvg) {
		return `<svg width="16px" height="16px" viewBox="0 0 20 20">${innerSvg}</svg>`;
	}
}
