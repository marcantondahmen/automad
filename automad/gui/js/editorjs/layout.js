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
 *	Copyright (c) 2020-2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


+function (Automad, $, UIkit) {

	Automad.layout = {

		$: $,
		UIkit: UIkit

	}

}(window.Automad = window.Automad || {}, jQuery, UIkit);


class AutomadLayoutButton {

	constructor(api, data, wrapper, options) {

		var _this = this;

		this.options = Object.assign({
			title: '',
			icon: '',
			name: '',
			value: '',
			buttonsClearRegex: '',
			blockClearRegex: '',
			clearDataKeys: [],
			onClick: function () { }
		}, options);

		this.data = data;
		this.api = api;
		this.wrapper = wrapper;
		this.cls = api.styles.settingsButton;
		this.clsActive = api.styles.settingsButtonActive;
		this.button = Automad.util.create.element('div', [this.cls, this.options.name]);
		this.button.innerHTML = this.options.icon;
		
		api.tooltip.onHover(this.button, this.options.title, { placement: 'top' });

		this.button.addEventListener('click', function () {
			_this.onClickHandler();
		});

		this.onInit();

	}

	onInit() {

		this.button.classList.toggle(this.clsActive, (this.data[this.options.name] == this.options.value));

	}

	onClickHandler() {

		var _this = this;

		const buttons = this.wrapper.querySelectorAll(`.${this.cls}`),
			  block = this.api.blocks.getBlockByIndex(this.api.blocks.getCurrentBlockIndex()).holder;

		Array.from(buttons).forEach((_button) => {
			if (_button.className.match(_this.options.buttonsClearRegex)) {
				_button.classList.remove(_this.clsActive);
			}
		});

		this.options.clearDataKeys.forEach((key) => {
			_this.data[key] = '';
		});

		this.button.classList.add(this.clsActive);
		this.data[this.options.name] = this.options.value;
		block.className = block.className.replace(this.options.blockClearRegex, '');
		block.classList.add(AutomadLayout.getClassName(this.options.name, this.options.value));
		this.options.onClick();

	}

	get() {

		return this.button;

	}

}


class AutomadLayoutResetButton extends AutomadLayoutButton {

	onInit() {

		this.button.classList.toggle(this.clsActive, (!this.data.span && !this.data.stretched));

	}

	onClickHandler() {

		var _this = this;

		const buttons = this.wrapper.querySelectorAll(`.${this.cls}`),
			  block = this.api.blocks.getBlockByIndex(this.api.blocks.getCurrentBlockIndex()).holder;

		Array.from(buttons).forEach((_button) => {
			if (_button.className.match(_this.options.buttonsClearRegex)) {
				_button.classList.remove(_this.clsActive);
			}
		});

		this.options.clearDataKeys.forEach((key) => {
			_this.data[key] = '';
		});

		this.button.classList.add(this.clsActive);
		block.className = block.className.replace(this.options.blockClearRegex, '');
		this.options.onClick();

	}

}

class AutomadLayout {

	constructor(editor) {

		this.editor = editor;

	}

	alignButton() {

		let editor = this.editor;

		try {

			var editorId = editor.configuration.holder,
				container = document.getElementById(editorId),
				button = container.querySelector(`.${AutomadEditorConfig.cls.actionsButton}`),
				blockId = editor.blocks.getCurrentBlockIndex(),
				block = editor.blocks.getBlockByIndex(blockId).holder,
				blockContent = block.querySelector(`.${AutomadEditorConfig.cls.blockContent}`);

			button.style.transform = 'translate3d(0,0,0)';

			var blockRight = blockContent.getBoundingClientRect().right,
				buttonRight = button.getBoundingClientRect().right,
				blockTop = blockContent.getBoundingClientRect().top,
				buttonTop = button.getBoundingClientRect().top,
				right = buttonRight - blockRight,
				top = blockTop - buttonTop;

			button.style.transform = `translate3d(-${right}px,${top}px,0)`;

		} catch (e) { }

	}

	applyLayout() {

		var editor = this.editor,
			apply = function (data) {

			for (var i = 0; i < editor.blocks.getBlocksCount(); i++) {

				var block = editor.blocks.getBlockByIndex(i).holder;

				if (data.blocks[i] !== undefined) {

					['span', 'start'].forEach((key) => {

						let value = data.blocks[i].data[key],
							regex = new RegExp(`${key}\-\d+`, 'g');

						block.className = block.className.replace(regex, '');
						block.classList.toggle(`${key}-${value}`, (value !== undefined && value !== ''));

					});

					block.classList.toggle('stretched', data.blocks[i].data.stretched);
					
				}

			}

		}

		if (!editor.configuration.readOnly) {

			editor.save().then((data) => {
				apply(data);
			});

		} else {

			editor.readOnly.toggle(false).then(() => {
				editor.save().then((data) => {
					editor.readOnly.toggle(true).then(() => {
						apply(data);
					});
				});
			});

		}

	}

	settingsButtonObserver() {

		var editor = this.editor,
			editorId = editor.configuration.holder,
			layout = this,
			alignButton = function () { layout.alignButton() };

		Automad.layout.$(document).on(
			'mousedown click',
			`#${editorId} .${AutomadEditorConfig.cls.block}, #${editorId} .${AutomadEditorConfig.cls.redactor}`,
			function () {
				setTimeout(alignButton, 50);
			}
		);

		Automad.layout.$(document).on(
			'mousedown click',
			`#${editorId} .${AutomadEditorConfig.cls.settingsLayout} div`,
			alignButton
		);

		Automad.layout.$(document).on(
			'dragend mouseup',
			`#${editorId} .${AutomadEditorConfig.cls.actionsButton} div`,
			alignButton
		);

	}

	initUndoHandler() {

		let layout = this;

		Automad.layout.$(window).bind('keydown', function (e) {

			if (e.ctrlKey || e.metaKey) {

				let key = String.fromCharCode(e.which).toLowerCase();

				if (key == 'z' || key == 'y') {
					setTimeout(function () {
						layout.applyLayout();
						layout.alignButton();
					}, 200);
				}

			}

		});

	}

	static getClassName(key, value) {

		if (typeof value == 'string') {
			return `${key}-${value}`;
		} else {
			if (value) {
				return `${key}`;
			}
		}

		return '';

	}

	static renderSettings(data, saved, api, config) {

		var element = Automad.util.create.element,
			wrapper = element('div', [AutomadEditorConfig.cls.settingsLayout]),
			mainWrapper = element('div', ['cdx-settings-1-2']),
			resetOption = {
				title: 'Default',
				name: 'reset',
				icon: '<svg width="24px" height="16px" viewBox="0 0 30 20"><path d="M27,0H3C1.3,0,0,1.3,0,3v14c0,1.7,1.3,3,3,3h24c1.7,0,3-1.3,3-3V3C30,1.3,28.7,0,27,0z M2,17V3c0-0.6,0.4-1,1-1h5v16H3 C2.4,18,2,17.6,2,17z M28,17c0,0.6-0.4,1-1,1h-5V2h5c0.6,0,1,0.4,1,1V17z"/></svg>'
			},
			stretchOption = {
				title: 'Stretch',
				name: 'stretched',
				icon: '<svg width="24px" height="16px" viewBox="0 0 30 20"><path d="M27,0H3C1.3,0,0,1.3,0,3v14c0,1.7,1.3,3,3,3h24c1.7,0,3-1.3,3-3V3C30,1.3,28.7,0,27,0z M25.9,10.9l-5,5 c-0.2,0.2-0.6,0.4-0.9,0.4s-0.6-0.1-0.9-0.4c-0.5-0.5-0.5-1.3,0-1.8l2.9-2.9H8l2.9,2.9c0.5,0.5,0.5,1.3,0,1.8 c-0.2,0.2-0.6,0.4-0.9,0.4s-0.6-0.1-0.9-0.4l-5-5c-0.5-0.5-0.5-1.3,0-1.8l5-5c0.5-0.5,1.3-0.5,1.8,0s0.5,1.3,0,1.8L8,8.8h14 l-2.9-2.9c-0.5-0.5-0.5-1.3,0-1.8s1.3-0.5,1.8,0l5,5C26.4,9.6,26.4,10.4,25.9,10.9z"/></svg>',
				value: true
			},
			spanWrapper = element('div', ['cdx-settings']),
			spanOptions = [
				{
					title: 'Span 1⁄4',
					name: 'span',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H5V2h11 c1.1,0,2,0.9,2,2V16z"/>',
					value: '3'
				},
				{
					title: 'Span 1⁄3',
					name: 'span',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H7V2h9 c1.1,0,2,0.9,2,2V16z"/>',
					value: '4'
				},
				{
					title: 'Span 1⁄2',
					name: 'span',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2h-6V2h6 c1.1,0,2,0.9,2,2V16z"/>',
					value: '6'
				},
				{
					title: 'Span 2⁄3',
					name: 'span',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2h-3V2h3 c1.1,0,2,0.9,2,2V16z"/>',
					value: '8'
				},
				{
					title: 'Span 3⁄4',
					name: 'span',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2h-1V2h1 c1.1,0,2,0.9,2,2V16z"/>',
					value: '9'
				},
				{
					title: 'Span 1⁄1',
					name: 'span',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z"/>',
					value: '12'
				}
			],
			startWrapper = element('div', ['cdx-settings']),
			startOptions = [
				{
					title: 'Auto',
					name: 'start',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H4 c-1.1,0-2-0.9-2-2V4c0-1.1,0.9-2,2-2h12c1.1,0,2,0.9,2,2V16z"/><path d="M15,11.4L12.5,10L15,8.6c0.6-0.3,0.8-1.1,0.5-1.7c-0.3-0.6-1.1-0.8-1.7-0.5l-2.5,1.4V5c0-0.7-0.6-1.2-1.2-1.2S8.8,4.3,8.8,5 v2.8L6.3,6.4C5.7,6.1,4.9,6.3,4.6,6.9C4.2,7.5,4.4,8.2,5,8.6L7.5,10L5,11.4c-0.6,0.3-0.8,1.1-0.5,1.7c0.3,0.6,1.1,0.8,1.7,0.5 l2.5-1.4V15c0,0.7,0.6,1.2,1.2,1.2s1.2-0.6,1.2-1.2v-2.8l2.5,1.4c0.6,0.3,1.4,0.1,1.7-0.5C15.8,12.5,15.6,11.8,15,11.4z"/>',
					value: ''
				},
				{
					title: 'Push 0',
					name: 'start',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H4 c-1.1,0-2-0.9-2-2V4c0-1.1,0.9-2,2-2h12c1.1,0,2,0.9,2,2V16z"/><rect x="2" y="2" width="2" height="16"/>',
					value: '1'
				},
				{
					title: 'Push 1⁄6',
					name: 'start',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H4 c-1.1,0-2-0.9-2-2V4c0-1.1,0.9-2,2-2h12c1.1,0,2,0.9,2,2V16z"/><rect x="4" y="2" width="2" height="16"/>',
					value: '3'
				},
				{
					title: 'Push 1⁄4',
					name: 'start',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H4 c-1.1,0-2-0.9-2-2V4c0-1.1,0.9-2,2-2h12c1.1,0,2,0.9,2,2V16z"/><rect x="5" y="2" width="2" height="16"/>',
					value: '4'
				},
				{
					title: 'Push 1⁄3',
					name: 'start',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H4 c-1.1,0-2-0.9-2-2V4c0-1.1,0.9-2,2-2h12c1.1,0,2,0.9,2,2V16z"/><rect x="7" y="2" width="2" height="16"/>',
					value: '5'
				},
				{
					title: 'Push 1⁄2',
					name: 'start',
					icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H4 c-1.1,0-2-0.9-2-2V4c0-1.1,0.9-2,2-2h12c1.1,0,2,0.9,2,2V16z"/><rect x="9" y="2" width="2" height="16"/>',
					value: '7'
				}
			];

		data.stretched = saved.stretched || '';
		data.span = saved.span || '';
		data.start = saved.start || '';

		const isNested = config.isNested || false,
			  allowStretching = config.allowStretching || false;

		const resetButton = new AutomadLayoutResetButton(api, data, wrapper, Object.assign(resetOption, {
			icon: resetOption.icon,
			buttonsClearRegex: /(span|stretched|start)/g,
			blockClearRegex: /(stretched|(span|start)\-\d+)/g,
			clearDataKeys: ['stretched', 'span', 'start'],
			onClick: function () {
				startWrapper.classList.add('uk-hidden');
			}
		}));

		mainWrapper.appendChild(resetButton.get());

		if (!isNested && allowStretching) {

			const stretchButton = new AutomadLayoutButton(api, data, wrapper, Object.assign(stretchOption, {
				icon: stretchOption.icon,
				buttonsClearRegex: /(span|start|reset)/g,
				blockClearRegex: /(stretched|(span|start)\-\d+)/g,
				clearDataKeys: ['span', 'start'],
				onClick: function () {
					startWrapper.classList.add('uk-hidden');
				}
			}));

			mainWrapper.appendChild(stretchButton.get());

		} else {

			let stretchButton = element('div', [api.styles.settingsButton, 'disabled']);

			stretchButton.innerHTML = stretchOption.icon;

			stretchButton.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				return false;
			});

			mainWrapper.appendChild(stretchButton);

		}

		wrapper.appendChild(mainWrapper);
	
		spanOptions.forEach(function (option) {

			const button = new AutomadLayoutButton(api, data, wrapper, Object.assign(option, {
				icon: AutomadLayout.icon(option.icon),
				buttonsClearRegex: /(span|stretched|reset)/g,
				blockClearRegex: /(stretched|span\-\d+)/g,
				clearDataKeys: ['stretched'],
				onClick: function () {
					startWrapper.classList.remove('uk-hidden');
					if (!data.start && !isNested) {
						startWrapper.querySelector('.start').click();
					}
				}
			}));

			spanWrapper.appendChild(button.get());

		});

		wrapper.appendChild(spanWrapper);

		if (!isNested) {

			startOptions.forEach(function (option) {

				const button = new AutomadLayoutButton(api, data, wrapper, Object.assign(option, {
					icon: AutomadLayout.icon(option.icon),
					buttonsClearRegex: /(start|stretched|reset)/g,
					blockClearRegex: /(stretched|start\-\d+)/g,
					clearDataKeys: ['stretched'],
					onClick: function() {
						if (!data.span) {
							spanWrapper.querySelector('.span').click();
						}
					}
				}));

				startWrapper.appendChild(button.get());

				if (!data.span) {
					startWrapper.classList.add('uk-hidden');
				}

			});

			wrapper.appendChild(startWrapper);

		}
	
		return wrapper;

	}

	static icon(innerSvg) {

		return `<svg width="16px" height="16px" viewBox="0 0 20 20">${innerSvg}</svg>`;

	}

}