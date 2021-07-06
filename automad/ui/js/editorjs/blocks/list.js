/*
 *	This EditorJS block is based on the original list block by CodeX and
 *	is extended to support the Automad block grid layout.
 *	https://github.com/editor-js/list
 *
 *	Copyright (c) 2018 CodeX (team@ifmo.su)
 *	Copyright (c) 2021 Marc Anton Dahmen
 *	MIT License
 */


class AutomadBlockList {

	static get conversionConfig() {

		return {

			/**
			 * To create exported string from list, concatenate items by dot-symbol.
			 */
			export: (data) => {
				return data.items.join('. ');
			},

			/**
			 * To create a list from other block's string, just put it at the first item.
			 */
			import: (string) => {
				return {
					items: [string],
					style: 'unordered',
				};
			}

		};

	}

	static get enableLineBreaks() {
		return true;
	}

	static get isReadOnlySupported() {
		return true;
	}

	static get pasteConfig() {
		return {
			tags: ['OL', 'UL', 'LI'],
		};
	}

	static get sanitize() {
		return {
			style: {},
			items: {
				br: true,
			},
		};
	}

	static get toolbox() {
		return {
			icon: '<svg width="17" height="13" viewBox="0 0 17 13" xmlns="http://www.w3.org/2000/svg"> <path d="M5.625 4.85h9.25a1.125 1.125 0 0 1 0 2.25h-9.25a1.125 1.125 0 0 1 0-2.25zm0-4.85h9.25a1.125 1.125 0 0 1 0 2.25h-9.25a1.125 1.125 0 0 1 0-2.25zm0 9.85h9.25a1.125 1.125 0 0 1 0 2.25h-9.25a1.125 1.125 0 0 1 0-2.25zm-4.5-5a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25zm0-4.85a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25zm0 9.85a1.125 1.125 0 1 1 0 2.25 1.125 1.125 0 0 1 0-2.25z"/></svg>',
			title: AutomadEditorTranslation.get('list_toolbox')
		};
	}

	constructor({ data, config, api, readOnly }) {
		
		this._elements = {
			wrapper: null,
		};

		this.api = api;
		this.readOnly = readOnly;

		this.settings = [
			{
				name: 'unordered',
				title: AutomadEditorTranslation.get('list_unordered'),
				icon: AutomadEditorIcons.get.listUnordered,
				default: false,
			},
			{
				name: 'ordered',
				title: AutomadEditorTranslation.get('list_ordered'),
				icon: AutomadEditorIcons.get.listOrdered,
				default: true,
			},
		];

		this._data = {
			style: this.settings.find((tune) => tune.default === true).name,
			items: [],
		};

		this.data = data;

		this.layoutSettings = AutomadLayout.renderSettings(this._data, data, api, config);

	}

	render() {

		this._elements.wrapper = this.makeMainTag(this._data.style);

		// fill with data
		if (this._data.items.length) {
			this._data.items.forEach((item) => {
				this._elements.wrapper.appendChild(this._make('li', this.CSS.item, {
					innerHTML: item,
				}));
			});
		} else {
			this._elements.wrapper.appendChild(this._make('li', this.CSS.item));
		}

		if (!this.readOnly) {
			// detect keydown on the last item to escape List
			this._elements.wrapper.addEventListener('keydown', (event) => {
				const [ENTER, BACKSPACE] = [13, 8]; // key codes

				switch (event.keyCode) {
					case ENTER:
						this.getOutofList(event);
						break;
					case BACKSPACE:
						this.backspace(event);
						break;
				}
			}, false);
		}

		return this._elements.wrapper;

	}

	save() {
		return this.data;
	}

	renderSettings() {

		const wrapper = document.createElement('div'),
			  inner = this._make('div', [this.CSS.settingsWrapper], {});

		this.settings.forEach((item) => {

			const itemEl = this._make('div', this.CSS.settingsButton, {
				innerHTML: item.icon,
			});

			itemEl.addEventListener('click', () => {
				this.toggleTune(item.name);

				// clear other buttons
				const buttons = itemEl.parentNode.querySelectorAll('.' + this.CSS.settingsButton);

				Array.from(buttons).forEach((button) =>
					button.classList.remove(this.CSS.settingsButtonActive)
				);

				// mark active
				itemEl.classList.toggle(this.CSS.settingsButtonActive);
			});

			this.api.tooltip.onHover(itemEl, item.title, {
				placement: 'top',
				hidingDelay: 500,
			});

			if (this._data.style === item.name) {
				itemEl.classList.add(this.CSS.settingsButtonActive);
			}

			inner.appendChild(itemEl);

		});

		wrapper.appendChild(inner);
		wrapper.appendChild(this.layoutSettings);

		return wrapper;

	}

	onPaste(event) {
		const list = event.detail.data;

		this.data = this.pasteHandler(list);
	}

	makeMainTag(style) {
		const styleClass = style === 'ordered' ? this.CSS.wrapperOrdered : this.CSS.wrapperUnordered;
		const tag = style === 'ordered' ? 'ol' : 'ul';

		return this._make(tag, [this.CSS.baseBlock, this.CSS.wrapper, styleClass], {
			contentEditable: !this.readOnly,
		});
	}

	toggleTune(style) {
		const newTag = this.makeMainTag(style);

		while (this._elements.wrapper.hasChildNodes()) {
			newTag.appendChild(this._elements.wrapper.firstChild);
		}

		this._elements.wrapper.replaceWith(newTag);
		this._elements.wrapper = newTag;
		this._data.style = style;
	}

	get CSS() {
		return {
			baseBlock: this.api.styles.block,
			wrapper: 'cdx-list',
			wrapperOrdered: 'cdx-list--ordered',
			wrapperUnordered: 'cdx-list--unordered',
			item: 'cdx-list__item',
			settingsWrapper: 'cdx-settings-1-2',
			settingsButton: this.api.styles.settingsButton,
			settingsButtonActive: this.api.styles.settingsButtonActive,
		};
	}

	set data(listData) {

		if (!listData) {
			listData = {};
		}

		this._data.style = listData.style || this.settings.find((tune) => tune.default === true).name;
		this._data.items = listData.items || [];

		const oldView = this._elements.wrapper;

		if (oldView) {
			oldView.parentNode.replaceChild(this.render(), oldView);
		}
		
	}

	get data() {

		this._data.items = [];

		const items = this._elements.wrapper.querySelectorAll(`.${this.CSS.item}`);

		for (let i = 0; i < items.length; i++) {
			const value = items[i].innerHTML.replace('<br>', ' ').trim();

			if (value) {
				this._data.items.push(items[i].innerHTML);
			}
		}

		return this._data;
	}

	_make(tagName, classNames = null, attributes = {}) {
		const el = document.createElement(tagName);

		if (Array.isArray(classNames)) {
			el.classList.add(...classNames);
		} else if (classNames) {
			el.classList.add(classNames);
		}

		for (const attrName in attributes) {
			el[attrName] = attributes[attrName];
		}

		return el;
	}

	get currentItem() {
		let currentNode = window.getSelection().anchorNode;

		if (currentNode.nodeType !== Node.ELEMENT_NODE) {
			currentNode = currentNode.parentNode;
		}

		return currentNode.closest(`.${this.CSS.item}`);
	}

	getOutofList(event) {
		const items = this._elements.wrapper.querySelectorAll('.' + this.CSS.item);

		/**
		 * Save the last one.
		 */
		if (items.length < 2) {
			return;
		}

		const lastItem = items[items.length - 1];
		const currentItem = this.currentItem;

		/** Prevent Default li generation if item is empty */
		if (currentItem === lastItem && !lastItem.textContent.trim().length) {
			/** Insert New Block and set caret */
			currentItem.parentElement.removeChild(currentItem);
			this.api.blocks.insert(undefined, undefined, undefined, undefined, true);
			event.preventDefault();
			event.stopPropagation();
		}
	}

	backspace(event) {
		const items = this._elements.wrapper.querySelectorAll('.' + this.CSS.item),
			firstItem = items[0];

		if (!firstItem) {
			return;
		}

		/**
		 * Save the last one.
		 */
		if (items.length < 2 && !firstItem.innerHTML.replace('<br>', ' ').trim()) {
			event.preventDefault();
		}
	}

	selectItem(event) {
		event.preventDefault();

		const selection = window.getSelection(),
			currentNode = selection.anchorNode.parentNode,
			currentItem = currentNode.closest('.' + this.CSS.item),
			range = new Range();

		range.selectNodeContents(currentItem);

		selection.removeAllRanges();
		selection.addRange(range);
	}

	pasteHandler(element) {
		const { tagName: tag } = element;
		let style;

		switch (tag) {
			case 'OL':
				style = 'ordered';
				break;
			case 'UL':
			case 'LI':
				style = 'unordered';
		}

		const data = {
			style,
			items: [],
		};

		if (tag === 'LI') {
			data.items = [element.innerHTML];
		} else {
			const items = Array.from(element.querySelectorAll('LI'));

			data.items = items
				.map((li) => li.innerHTML)
				.filter((item) => !!item.trim());
		}

		return data;
	}

}