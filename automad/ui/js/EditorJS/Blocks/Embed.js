/*
 * This EditorJS block is based on the original embed block by CodeX and
 * is extended to support custom services that are currently
 * not supported as of version 2.4.6.
 * https://github.com/editor-js/embed
 *
 * Copyright (c) 2018 CodeX (team@ifmo.su)
 * Copyright (c) 2021 Marc Anton Dahmen
 * MIT License
 *
 * ------------------------------------------------------------------------
 *
 * The debounce function is part of Underscore.js
 * https://underscorejs.org
 *
 * Copyright (c) 2009-2020 Jeremy Ashkenas, DocumentCloud and Investigative
 * MIT License
 */

+(function (Automad) {
	Automad.embedUtils = {
		/**
		 * Returns a function, that, as long as it continues to be invoked, will not
		 * be triggered. The function will be called after it stops being called for
		 * N milliseconds. If `immediate` is passed, trigger the function on the
		 * leading edge, instead of the trailing. The function also has a property 'clear'
		 * that is a function which will clear the timer to prevent previously scheduled executions.
		 *
		 * @source underscore.js
		 * @see http://unscriptable.com/2009/03/20/debouncing-javascript-methods/
		 * @param {Function} function to wrap
		 * @param {Number} timeout in ms (`100`)
		 * @param {Boolean} whether to execute at the beginning (`false`)
		 * @api public
		 */

		debounce: function (func, wait, immediate) {
			var timeout, args, context, timestamp, result;
			if (null == wait) wait = 100;

			function later() {
				var last = Date.now() - timestamp;

				if (last < wait && last >= 0) {
					timeout = setTimeout(later, wait - last);
				} else {
					timeout = null;
					if (!immediate) {
						result = func.apply(context, args);
						context = args = null;
					}
				}
			}

			var debounced = function () {
				context = this;
				args = arguments;
				timestamp = Date.now();
				var callNow = immediate && !timeout;
				if (!timeout) timeout = setTimeout(later, wait);
				if (callNow) {
					result = func.apply(context, args);
					context = args = null;
				}

				return result;
			};

			debounced.clear = function () {
				if (timeout) {
					clearTimeout(timeout);
					timeout = null;
				}
			};

			debounced.flush = function () {
				if (timeout) {
					result = func.apply(context, args);
					context = args = null;

					clearTimeout(timeout);
					timeout = null;
				}
			};

			return debounced;
		},
	};
})((window.Automad = window.Automad || {}));

class AutomadBlockEmbed {
	static get isReadOnlySupported() {
		return true;
	}

	static get pasteConfig() {
		return {
			patterns: AutomadBlockEmbed.patterns,
		};
	}

	static prepare({ config = {} }) {
		const { services = {} } = config;

		let entries = Object.entries(Automad.editorJS.embedServices);

		const enabledServices = Object.entries(services)
			.filter(([key, value]) => {
				return typeof value === 'boolean' && value === true;
			})
			.map(([key]) => key);

		const userServices = Object.entries(services)
			.filter(([key, value]) => {
				return typeof value === 'object';
			})
			.filter(([key, service]) =>
				AutomadBlockEmbed.checkServiceConfig(service)
			)
			.map(([key, service]) => {
				const { regex, embedUrl, html, height, width, id } = service;

				return [
					key,
					{
						regex,
						embedUrl,
						html,
						height,
						width,
						id,
					},
				];
			});

		if (enabledServices.length) {
			entries = entries.filter(([key]) => enabledServices.includes(key));
		}

		entries = entries.concat(userServices);

		AutomadBlockEmbed.services = entries.reduce(
			(result, [key, service]) => {
				if (!(key in result)) {
					result[key] = service;

					return result;
				}

				result[key] = Object.assign({}, result[key], service);

				return result;
			},
			{}
		);

		AutomadBlockEmbed.patterns = entries.reduce((result, [key, item]) => {
			result[key] = item.regex;

			return result;
		}, {});
	}

	static checkServiceConfig(config) {
		const { regex, embedUrl, html, height, width, id } = config;

		let isValid =
			regex &&
			regex instanceof RegExp &&
			embedUrl &&
			typeof embedUrl === 'string' &&
			html &&
			typeof html === 'string';

		isValid = isValid && (id !== undefined ? id instanceof Function : true);
		isValid =
			isValid && (height !== undefined ? Number.isFinite(height) : true);
		isValid =
			isValid && (width !== undefined ? Number.isFinite(width) : true);

		return isValid;
	}

	constructor({ data, api, readOnly }) {
		this.api = api;
		this._data = {};
		this.element = null;
		this.readOnly = readOnly;

		this.data = data;
	}

	set data(data) {
		if (!(data instanceof Object)) {
			throw Error('Embed Tool data should be object');
		}

		const { service, source, embed, width, height, caption = '' } = data;

		this._data = {
			service: service || this.data.service,
			source: source || this.data.source,
			embed: embed || this.data.embed,
			width: width || this.data.width,
			height: height || this.data.height,
			caption: caption || this.data.caption || '',
		};

		const oldView = this.element;

		if (oldView) {
			oldView.parentNode.replaceChild(this.render(), oldView);
		}
	}

	get data() {
		if (this.element) {
			const caption = this.element.querySelector(
				`.${this.api.styles.input}`
			);

			this._data.caption = caption ? caption.innerHTML : '';
		}

		return this._data;
	}

	get CSS() {
		return {
			baseClass: this.api.styles.block,
			input: this.api.styles.input,
			container: 'embed-tool',
			containerLoading: 'embed-tool--loading',
			preloader: 'embed-tool__preloader',
			caption: 'embed-tool__caption',
			url: 'embed-tool__url',
			content: 'embed-tool__content',
		};
	}

	render() {
		if (!this.data.service) {
			const container = document.createElement('div');

			this.element = container;

			return container;
		}

		const { html } = AutomadBlockEmbed.services[this.data.service];
		const container = document.createElement('div');
		const caption = document.createElement('div');
		const template = document.createElement('template');
		const preloader = this.createPreloader();

		container.classList.add(
			this.CSS.baseClass,
			this.CSS.container,
			this.CSS.containerLoading
		);
		caption.classList.add(this.CSS.input, this.CSS.caption);

		container.appendChild(preloader);

		caption.contentEditable = !this.readOnly;
		caption.dataset.placeholder = 'Enter a caption';
		caption.innerHTML = this.data.caption || '';

		template.innerHTML = html;
		template.content.firstChild.setAttribute('src', this.data.embed);
		template.content.firstChild.classList.add(this.CSS.content);

		const embedIsReady = this.embedIsReady(container);

		container.appendChild(template.content.firstChild);
		container.appendChild(caption);

		embedIsReady.then(() => {
			container.classList.remove(this.CSS.containerLoading);
		});

		this.element = container;

		return container;
	}

	createPreloader() {
		const preloader = document.createElement('preloader');
		const url = document.createElement('div');

		url.textContent = this.data.source;

		preloader.classList.add(this.CSS.preloader);
		url.classList.add(this.CSS.url);

		preloader.appendChild(url);

		return preloader;
	}

	save() {
		return this.data;
	}

	onPaste(event) {
		const { key: service, data: url } = event.detail;

		const {
			regex,
			embedUrl,
			width,
			height,
			id = (ids) => ids.shift(),
		} = AutomadBlockEmbed.services[service];
		const result = regex.exec(url).slice(1);
		const embed = embedUrl.replace(/<\%\= remote\_id \%\>/g, id(result));

		this.data = {
			service,
			source: url,
			embed,
			width,
			height,
		};
	}

	embedIsReady(targetNode) {
		const PRELOADER_DELAY = 450;

		let observer = null;

		return new Promise((resolve, reject) => {
			observer = new MutationObserver(
				Automad.embedUtils.debounce(resolve, PRELOADER_DELAY)
			);
			observer.observe(targetNode, {
				childList: true,
				subtree: true,
			});
		}).then(() => {
			observer.disconnect();
		});
	}
}
