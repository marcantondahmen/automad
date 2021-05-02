/*
 *	This EditorJS block is based on the original quote block by CodeX and
 *	is extended to support the Automad block grid layout.
 *	https://github.com/editor-js/quote
 *
 *	Copyright (c) 2018 CodeX (team@ifmo.su)
 *	Copyright (c) 2021 Marc Anton Dahmen
 *	MIT License
 */


class AutomadBlockQuote {
	
	static get isReadOnlySupported() {
		return true;
	}

	static get toolbox() {
		return {
			icon: '<svg width="15" height="14" viewBox="0 0 15 14" xmlns="http://www.w3.org/2000/svg"><path d="M13.53 6.185l.027.025a1.109 1.109 0 0 1 0 1.568l-5.644 5.644a1.109 1.109 0 1 1-1.569-1.568l4.838-4.837L6.396 2.23A1.125 1.125 0 1 1 7.986.64l5.52 5.518.025.027zm-5.815 0l.026.025a1.109 1.109 0 0 1 0 1.568l-5.644 5.644a1.109 1.109 0 1 1-1.568-1.568l4.837-4.837L.58 2.23A1.125 1.125 0 0 1 2.171.64L7.69 6.158l.025.027z" /></svg>',
			title: AutomadEditorTranslation.get('quote_toolbox'),
		};
	}

	static get contentless() {
		return true;
	}

	static get enableLineBreaks() {
		return true;
	}

	static get DEFAULT_QUOTE_PLACEHOLDER() {
		return AutomadEditorTranslation.get('quote_placeholder');
	}

	static get DEFAULT_CAPTION_PLACEHOLDER() {
		return AutomadEditorTranslation.get('quote_placeholder_caption');
	}

	static get ALIGNMENTS() {
		return {
			left: 'left',
			center: 'center',
		};
	}

	static get DEFAULT_ALIGNMENT() {
		return AutomadBlockQuote.ALIGNMENTS.left;
	}

	static get conversionConfig() {
		return {
			/**
			 * To create Quote data from string, simple fill 'text' property
			 */
			import: 'text',
			/**
			 * To create string from Quote data, concatenate text and caption
			 */
			export: function (quoteData) {
				return quoteData.caption ? `${quoteData.text} — ${quoteData.caption}` : quoteData.text;
			},
		};
	}

	static get sanitize() {
		return {
			text: {
				br: true,
			},
			caption: {
				br: true,
			},
			alignment: {},
		};
	}

	constructor({ data, config, api, readOnly }) {
		const { ALIGNMENTS, DEFAULT_ALIGNMENT } = AutomadBlockQuote;

		this.api = api;
		this.readOnly = readOnly;

		this.quotePlaceholder = config.quotePlaceholder || AutomadBlockQuote.DEFAULT_QUOTE_PLACEHOLDER;
		this.captionPlaceholder = config.captionPlaceholder || AutomadBlockQuote.DEFAULT_CAPTION_PLACEHOLDER;

		this.data = {
			text: data.text || '',
			caption: data.caption || '',
			alignment: Object.values(ALIGNMENTS).includes(data.alignment) && data.alignment ||
				config.defaultAlignment ||
				DEFAULT_ALIGNMENT,
		};

		this.layoutSettings = AutomadLayout.renderSettings(this.data, data, api, config);

	}

	get CSS() {
		return {
			baseClass: this.api.styles.block,
			wrapper: 'cdx-quote',
			text: 'cdx-quote__text',
			input: this.api.styles.input,
			caption: 'cdx-quote__caption',
			settingsWrapper: 'cdx-settings-1-2',
			settingsButton: this.api.styles.settingsButton,
			settingsButtonActive: this.api.styles.settingsButtonActive,
		};
	}

	get settings() {
		return [
			{
				name: 'left',
				icon: AutomadEditorIcons.get.alignLeft,
				title: AutomadEditorTranslation.get('left')
			},
			{
				name: 'center',
				icon: AutomadEditorIcons.get.alignCenter,
				title: AutomadEditorTranslation.get('center')
			},
		];
	}

	render() {
		const container = this._make('blockquote', [this.CSS.baseClass, this.CSS.wrapper]);
		const quote = this._make('div', [this.CSS.input, this.CSS.text], {
			contentEditable: !this.readOnly,
			innerHTML: this.data.text,
		});
		const caption = this._make('div', [this.CSS.input, this.CSS.caption], {
			contentEditable: !this.readOnly,
			innerHTML: this.data.caption,
		});

		quote.dataset.placeholder = this.quotePlaceholder;
		caption.dataset.placeholder = this.captionPlaceholder;

		container.appendChild(quote);
		container.appendChild(caption);

		return container;
	}

	save(quoteElement) {
		const text = quoteElement.querySelector(`.${this.CSS.text}`);
		const caption = quoteElement.querySelector(`.${this.CSS.caption}`);

		return Object.assign(this.data, {
			text: text.innerHTML,
			caption: caption.innerHTML,
		});
	}

	renderSettings() {

		const wrapper = document.createElement('div');
		const inner = this._make('div', [this.CSS.settingsWrapper], {});
		const capitalize = str => str[0].toUpperCase() + str.substr(1);

		this.settings
			.map(tune => {
				const el = this._make('div', this.CSS.settingsButton, {
					innerHTML: tune.icon,
					title: `${capitalize(tune.name)} alignment`,
				});

				el.classList.toggle(this.CSS.settingsButtonActive, tune.name === this.data.alignment);
				this.api.tooltip.onHover(el, tune.title, { placement: 'top' });
				inner.appendChild(el);

				return el;
			})
			.forEach((element, index, elements) => {
				element.addEventListener('click', () => {
					this._toggleTune(this.settings[index].name);

					elements.forEach((el, i) => {
						const { name } = this.settings[i];

						el.classList.toggle(this.CSS.settingsButtonActive, name === this.data.alignment);
					});
				});
			});

		wrapper.appendChild(inner);
		wrapper.appendChild(this.layoutSettings);
		
		return wrapper;
	};

	_toggleTune(tune) {
		this.data.alignment = tune;
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

}