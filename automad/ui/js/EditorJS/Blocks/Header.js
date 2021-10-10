/*
 * This EditorJS block is based on the original header block by CodeX and
 * is extended to support text align center and left tunes.
 * https://github.com/editor-js/header
 *
 * Copyright (c) 2018 CodeX (team@ifmo.su)
 * Copyright (c) 2021 Marc Anton Dahmen
 * MIT License
 */

class AutomadBlockHeader {
	static get conversionConfig() {
		return {
			export: 'text', // use 'text' property for other blocks
			import: 'text', // fill 'text' property from other block's export string
		};
	}

	static get isReadOnlySupported() {
		return true;
	}

	static get pasteConfig() {
		return {
			tags: ['H1', 'H2', 'H3', 'H4', 'H5', 'H6'],
		};
	}

	static get sanitize() {
		return {
			level: false,
			text: {},
		};
	}

	static get toolbox() {
		return {
			icon: '<svg width="10" height="14" viewBox="0 0 10 14"><path d="M7.6 8.15H2.25v4.525a1.125 1.125 0 0 1-2.25 0V1.125a1.125 1.125 0 1 1 2.25 0V5.9H7.6V1.125a1.125 1.125 0 0 1 2.25 0v11.55a1.125 1.125 0 0 1-2.25 0V8.15z"/></svg>',
			title: AutomadEditorTranslation.get('heading_toolbox'),
		};
	}

	constructor({ data, config, api, readOnly }) {
		this.api = api;
		this.readOnly = readOnly;

		this._CSS = {
			block: this.api.styles.block,
			settingsButton: this.api.styles.settingsButton,
			settingsButtonActive: this.api.styles.settingsButtonActive,
			wrapper: 'ce-header',
		};

		this._settings = config;
		this._data = this.normalizeData(data);
		this.settingsButtons = [];
		this._element = this.getTag();
	}

	normalizeData(data) {
		const newData = {};

		if (typeof data !== 'object') {
			data = {};
		}

		newData.text = data.text || '';
		newData.level = parseInt(data.level) || this.defaultLevel.number;
		newData.alignment = data.alignment || 'left';

		return newData;
	}

	render() {
		return this._element;
	}

	renderSettings() {
		const wrapper = document.createElement('DIV'),
			wrapperLevels = document.createElement('DIV'),
			wrapperAlign = document.createElement('DIV'),
			alignments = [
				{
					title: AutomadEditorTranslation.get('left'),
					icon: AutomadEditorIcons.get.alignLeft,
					value: 'left',
				},
				{
					title: AutomadEditorTranslation.get('center'),
					icon: AutomadEditorIcons.get.alignCenter,
					value: 'center',
				},
			];

		wrapperAlign.classList.add('cdx-settings-1-2');
		wrapperLevels.classList.add('cdx-settings');

		alignments.forEach((alignment) => {
			const button = document.createElement('SPAN');

			button.classList.add(this._CSS.settingsButton);
			button.classList.toggle(
				this._CSS.settingsButtonActive,
				this.data.alignment == alignment.value
			);
			button.innerHTML = alignment.icon;

			button.addEventListener('click', () => {
				const _buttons = wrapperAlign.querySelectorAll(
					`.${this._CSS.settingsButton}`
				);

				Array.from(_buttons).forEach((_button) => {
					_button.classList.toggle(
						this._CSS.settingsButtonActive,
						false
					);
				});

				this._data.alignment = alignment.value;
				button.classList.toggle(this._CSS.settingsButtonActive, true);
				this.setAlignment(this._element);
			});

			this.api.tooltip.onHover(button, alignment.title, {
				placement: 'top',
			});
			wrapperAlign.appendChild(button);
		});

		this.levels.forEach((level) => {
			const selectTypeButton = document.createElement('SPAN');

			selectTypeButton.classList.add(this._CSS.settingsButton);

			if (this.currentLevel.number === level.number) {
				selectTypeButton.classList.add(this._CSS.settingsButtonActive);
			}

			selectTypeButton.innerHTML = `<span style="font-weight: 560;">${level.tag}</span>`;

			selectTypeButton.dataset.level = level.number;

			selectTypeButton.addEventListener('click', () => {
				this.setLevel(level.number);
			});

			wrapperLevels.appendChild(selectTypeButton);

			this.settingsButtons.push(selectTypeButton);
		});

		wrapper.appendChild(wrapperAlign);
		wrapper.appendChild(wrapperLevels);

		return wrapper;
	}

	setAlignment(element) {
		element.classList.toggle(
			'uk-text-center',
			this._data.alignment == 'center'
		);
	}

	setLevel(level) {
		this.data = {
			level: level,
			text: this.data.text,
			alignment: this.data.alignment,
		};

		this.settingsButtons.forEach((button) => {
			button.classList.toggle(
				this._CSS.settingsButtonActive,
				parseInt(button.dataset.level) === level
			);
		});
	}

	merge(data) {
		const newData = {
			text: this.data.text + data.text,
			level: this.data.level,
			alignment: this.data.alignment,
		};

		this.data = newData;
	}

	validate(blockData) {
		return blockData.text.trim() !== '';
	}

	save(toolsContent) {
		return Object.assign(this.data, {
			text: toolsContent.innerHTML,
			level: this.currentLevel.number,
		});
	}

	get data() {
		this._data.text = this._element.innerHTML;
		this._data.level = this.currentLevel.number;

		return this._data;
	}

	set data(data) {
		this._data = this.normalizeData(data);

		/**
		 * If level is set and block in DOM
		 * then replace it to a new block
		 */
		if (data.level !== undefined && this._element.parentNode) {
			const newHeader = this.getTag();

			newHeader.innerHTML = this._element.innerHTML;

			this._element.parentNode.replaceChild(newHeader, this._element);

			this._element = newHeader;
		}

		/**
		 * If data.text was passed then update block's content
		 */
		if (data.text !== undefined) {
			this._element.innerHTML = this._data.text || '';
		}
	}

	getTag() {
		/**
		 * Create element for current Block's level
		 */
		const tag = document.createElement(this.currentLevel.tag);

		/**
		 * Add text to block
		 */
		tag.innerHTML = this._data.text || '';

		/**
		 * Add styles class
		 */
		tag.classList.add(this._CSS.wrapper);
		this.setAlignment(tag);

		/**
		 * Make tag editable
		 */
		tag.contentEditable = this.readOnly ? 'false' : 'true';

		/**
		 * Add Placeholder
		 */
		tag.dataset.placeholder = this.api.i18n.t(
			this._settings.placeholder || ''
		);

		return tag;
	}

	get currentLevel() {
		let level = this.levels.find(
			(levelItem) => levelItem.number === this._data.level
		);

		if (!level) {
			level = this.defaultLevel;
		}

		return level;
	}

	get defaultLevel() {
		/**
		 * User can specify own default level value
		 */
		if (this._settings.defaultLevel) {
			const userSpecified = this.levels.find((levelItem) => {
				return levelItem.number === this._settings.defaultLevel;
			});

			if (userSpecified) {
				return userSpecified;
			} else {
				console.warn(
					"(ง'̀-'́)ง Heading Tool: the default level specified was not found in available levels"
				);
			}
		}

		return this.levels[1];
	}

	get levels() {
		const availableLevels = [
			{
				number: 1,
				tag: 'H1',
			},
			{
				number: 2,
				tag: 'H2',
			},
			{
				number: 3,
				tag: 'H3',
			},
			{
				number: 4,
				tag: 'H4',
			},
			{
				number: 5,
				tag: 'H5',
			},
			{
				number: 6,
				tag: 'H6',
			},
		];

		return this._settings.levels
			? availableLevels.filter((l) =>
					this._settings.levels.includes(l.number)
			  )
			: availableLevels;
	}

	onPaste(event) {
		const content = event.detail.data;

		let level = this.defaultLevel.number;

		switch (content.tagName) {
			case 'H1':
				level = 1;
				break;
			case 'H2':
				level = 2;
				break;
			case 'H3':
				level = 3;
				break;
			case 'H4':
				level = 4;
				break;
			case 'H5':
				level = 5;
				break;
			case 'H6':
				level = 6;
				break;
		}

		if (this._settings.levels) {
			// Fallback to nearest level when specified not available
			level = this._settings.levels.reduce((prevLevel, currLevel) => {
				return Math.abs(currLevel - level) < Math.abs(prevLevel - level)
					? currLevel
					: prevLevel;
			});
		}

		this.data = {
			level,
			text: content.innerHTML,
		};
	}
}
