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


/*
 *	A wrapper for editor.js. 
 */
 
+function(Automad, $) {
	
	Automad.blockEditor = {
		
		dataAttr: 'data-am-block-editor',

		makeReadOnly: function(editor) {

			const holder = editor.configuration.holder,
				  inputs = holder.querySelectorAll('input, select'),
				  editables = holder.querySelectorAll('[contenteditable]');
			
			Array.from(inputs).forEach((element) => {
				element.setAttribute('readonly', true);
				element.setAttribute('disabled', true);
			});

			Array.from(editables).forEach((element) => {
				element.removeAttribute('contenteditable');
			});

		},

		createEditor: function(options) {

			const t = AutomadEditorTranslation.get;

			options = Object.assign({
				holder: false,
				input: false,
				autofocus: false,
				readOnly: false,
				placeholder: '',
				flex: false,
				onReady: function() {}
			}, options);

			var $input = $(options.input);

			try {
				// Unescape &amp; to make embed URLs with parameters work. 
				var data = JSON.parse($input.val().replace(/&amp;/g, '&'));
			} catch (e) {
				var data = {};
			}

			// In order to avoid infinite loops due to initializing section editors,
			// the initialization of those readOnly preview editors has to be prevented as soon as 
			// there is no section data anymore.
			if (typeof data.blocks === 'undefined' && options.readOnly) {
				return;
			}

			var editor = new EditorJS({

				holder: options.holder,
				logLevel: 'ERROR',
				data: data,
				tools: AutomadEditorConfig.tools(options.readOnly, options.flex),
				readOnly: options.readOnly,
				minHeight: false,
				autofocus: options.autofocus,
				placeholder: options.placeholder,
				i18n: {
					messages: {
						ui: {
							"blockTunes": {
								"toggler": {
									"Click to tune": t('ui_settings')
								}
							},
							"inlineToolbar": {
								"converter": {
									"Convert to": t('ui_convert')
								}
							},
							"toolbar": {
								"toolbox": {
									"Add": t('ui_add')
								}
							}
						},
						blockTunes: {
							"delete": {
								"Delete": t('ui_delete')
							},
							"moveUp": {
								"Move up": t('ui_move_up')
							},
							"moveDown": {
								"Move down": t('ui_move_down')
							}
						}
					}
				},

				onChange: function () {

					if (!options.readOnly) {
					
						try {

							editor.save().then(function (data) {

								// Purge layout data.
								data.blocks.forEach(block => {
									['stretched', 'widthFraction'].forEach(key => {
										if (!block.data[key]) {
											delete block.data[key];
										}
									});
								});

								// Only trigger change in case blocks actually have changed.
								var blocksNew = JSON.stringify(data.blocks);

								try {
									var blocksCurrent = JSON.stringify(JSON.parse($input.val()).blocks);
								} catch (e) {
									var blocksCurrent = '';
								}

								if (blocksCurrent != blocksNew) {
									$input.val(JSON.stringify(data, null, 2)).trigger('change');
								}

							});

						} catch (e) {}

					}

				},

				onReady: function () {

					const layout = new AutomadLayout(editor);

					layout.applyLayout(data, function() {

						if (!options.readOnly) {

							new AutomadEditorUndo({ editor, data });
							new DragDrop(editor);

							layout.settingsButtonObserver();
							layout.initPasteHandler();

						} else {

							Automad.blockEditor.makeReadOnly(editor, data);

						}

						options.onReady();

					});

				}

			});

			return editor;

		},

		initErrorHandler: function() {

			$(window).on('error', function (event) {

				let msg = event.originalEvent.message;

				if (msg.includes('closest') || 
					msg.includes('updateCurrentInput') ||
					msg.includes('DOMException')) {
					event.preventDefault();
				}

			});

		},

		init: function() {
			
			var be = Automad.blockEditor,
				selector = '[' + be.dataAttr + ']',
				triggerChange = function() {

					var block = $(this)
								.closest(`.${AutomadEditorConfig.cls.editor}`)
								.find(`.cdx-block, .${AutomadEditorConfig.cls.blockContent}, .ce-block`)
								.first()
								.get(0),
						temp = document.createElement('div');

					// Trigger a fake block changed event by adding and removing a temporary div.
					try {
						block.appendChild(temp);
						block.removeChild(temp);
					} catch(e) {}

				};

			be.initErrorHandler();

			$(selector).each(function() {

				var $wrapper = $(this),
					id = $wrapper.data(Automad.util.dataCamelCase(be.dataAttr)),
					input = this.querySelector('input');

				// Remove data attribute to prevent multiple initializations.
				$wrapper.removeAttr(be.dataAttr);

				be.createEditor({
					holder: id,
					input: input,
					placeholder: AutomadEditorTranslation.get('ui_placeholder'),
					flex: false,
					onReady: () => {
						// Add init class to all editor tooltips in order to keep them 
						// during clean-up after destroying section modal editor tooltips.
						$('.ct').toggleClass('init', true);
					}
				});

			});

			// Trigger changes when clicking a settings button or changing an input field.
			$(document).on('click', `.${AutomadEditorConfig.cls.settingsButton}`, triggerChange);
			$(document).on('change keyup keydown', `.${AutomadEditorConfig.cls.input}, .${AutomadEditorConfig.cls.block} input, .${AutomadEditorConfig.cls.block} select`, triggerChange);
			
		}

	};

	$(document).on('ajaxComplete', function (e, xhr, settings) {

		if (settings.url.includes('page_data') || settings.url.includes('shared_data') || settings.url.includes('inpage_edit')) {
			Automad.blockEditor.init();
		}

	});
	
}(window.Automad = window.Automad || {}, jQuery);