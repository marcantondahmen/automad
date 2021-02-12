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

		createEditor: function(options) {

			options = Object.assign({
				holder: false,
				input: false,
				isNested: false,
				readOnly: false,
				autofocus: false,
				onReady: function() {}
			}, options);

			var $input = $(options.input);

			try {
				// Unescape &amp; to make embed URLs with parameters work. 
				var data = JSON.parse($input.val().replace(/&amp;/g, '&'));
			} catch (e) {
				var data = {};
			}

			var editor = new EditorJS({

				holder: options.holder,
				logLevel: 'ERROR',
				data: data,
				tools: AutomadEditorConfig.tools(options.isNested),
				readOnly: options.readOnly,
				minHeight: false,
				autofocus: options.autofocus,

				onChange: function () {

					if (!editor.configuration.readOnly) {

						editor.save().then(function (data) {
			
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

					}

				},

				onReady: function () {

					const layout = new AutomadLayout(editor);

					layout.applyLayout();

					if (!editor.configuration.readOnly) {

						var undo = new Undo({ editor });

						undo.initialize(data);
						new DragDrop(editor);
						layout.settingsButtonObserver();
						layout.initUndoHandler();
						
					}

					options.onReady();

				}

			});

			return editor;

		},

		initErrorHandler: function() {

			$(window).on('error', function (event) {

				if (event.originalEvent.message.includes('updateCurrentInput')) {
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
								.find(`.cdx-block, .${AutomadEditorConfig.cls.blockContent}`)
								.first()
								.get(0),
						temp = document.createElement('div');

					// Trigger a fake block changed event by adding and removing a temporary div.
					block.appendChild(temp);
					block.removeChild(temp);

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
					input: input
				});

			});

			// Trigger changes when clicking a settings button or changing an input field.
			$(document).on('click', `.${AutomadEditorConfig.cls.settingsButton}`, triggerChange);
			$(document).on('change keyup', `.${AutomadEditorConfig.cls.input}, .${AutomadEditorConfig.cls.block} input, .${AutomadEditorConfig.cls.block} select`, triggerChange);
			
		}

	};

	$(document).on('ajaxComplete', function (e, xhr, settings) {

		if (settings.url.includes('page_data') || settings.url.includes('shared_data') || settings.url.includes('inpage_edit')) {
			Automad.blockEditor.init();
		}

	});
	
}(window.Automad = window.Automad || {}, jQuery);