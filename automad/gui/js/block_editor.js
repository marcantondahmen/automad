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
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	A wrapper for editor.js. 
 */
	
+function(Automad, $) {
	
	Automad.blockEditor = {
		
		dataAttr: 'data-am-block-editor',

		init: function() {
			
			var be = Automad.blockEditor,
				selector = '[' + be.dataAttr + ']',
				triggerChange = function() {

					var block = $(this).closest('.codex-editor').find('.cdx-block, .ce-block__content').first().get(0),
						temp = document.createElement('div');

					// Trigger a fake block changed event by adding and removing a temporary div.
					block.appendChild(temp);
					block.removeChild(temp);

				};

			$(selector).each(function() {

				var $wrapper = $(this),
					holder = $wrapper.data(Automad.util.dataCamelCase(be.dataAttr)),
					$input = $wrapper.find('input'),
					key = $input.attr('name').replace(/(data\[|\])/g, ''),
					data,
					editor;
						
				try {
					// Unescape &amp; to make embed URLs with parameters work. 
					data = JSON.parse($input.val().replace(/&amp;/g, '&'));
				} catch (e) {
					data = {};
				}
				
				// Remove data attribute to prevent multiple initializations.
				$wrapper.removeAttr(be.dataAttr);

				editor = new EditorJS({

					holder: holder,
					logLevel: 'ERROR',
					data: data,
					tools: {
						paragraph: {
							class: AutomadParagraph,
							inlineToolbar: true
						},
						header: {
							class: Header,
							shortcut: 'CMD+SHIFT+H',
							inlineToolbar: true,
							config: {
								levels: [1, 2, 3, 4, 5, 6],
								defaultLevel: 2
							}
						},
						lists: {
							class: List,
							inlineToolbar: true,
						},
						table: {
							class: Table,
							inlineToolbar: true
						},
						quote: {
							class: Quote,
							inlineToolbar: true
						},
						delimiter: AutomadDelimiter,
						image: AutomadImage,
						gallery: AutomadGallery,
						slider: AutomadSlider,
						buttons: {
							class: AutomadButtons,
							inlineToolbar: ['italic', 'bold', 'underline', 'editorJSStyle'] 
						},
						pagelist: AutomadPagelist,
						filelist: AutomadFilelist,
						toc: {
							class: AutomadToc,
							config: { key: key }
						},
						code: AutomadTextareaCode,
						raw: AutomadTextareaRaw,
						mail: AutomadMail,
						snippet: AutomadSnippet,
						embed: {
							class: Embed,
							config: {
								services: Automad.editorJS.embedServices
							}
						},
						underline: Underline,
						inlineCode: {
							class: InlineCode,
							shortcut: 'CMD+SHIFT+M'
						},
						marker: {
							class: Marker
						},
						editorJSStyle: {
							class: EditorJSStyle,
							shortcut: 'CMD+SHIFT+S'
						},
						editorJSInspector: EditorJSInspector
					},

					onChange: function() { 

						editor.save().then(function(data) {

							// Only trigger change in case blocks actually have changed.
							var blocksNew = JSON.stringify(data.blocks);

							try {
								var blocksCurrent = JSON.stringify(JSON.parse($input.val()).blocks);
							} catch(e) {
								var blocksCurrent = '';
							}

							if (blocksCurrent != blocksNew) {
								$input.val(JSON.stringify(data)).trigger('change');
							}

						});
					
					},

					onReady: function() {
						
						$wrapper.find('.codex-editor__redactor').removeAttr('style');
						new DragDrop(editor);
						new Undo({ editor });

					}

				});

			});

			// Trigger changes when clicking a settings button or changing an input field.
			$(document).on('click', '.ce-settings__plugin-zone .cdx-settings-button', triggerChange);
			$(document).on('change keyup', '.cdx-input, .ce-block input, .ce-block select', triggerChange);
			
			// Blur focus on block when clicking outside.
			$(window).click(function () {
				$('.ce-block--focused').removeClass('ce-block--focused');
			});
			
			$(document).on('blur', '.ce-block--focused [contenteditable]', function(event) {
				event.stopPropagation();
			});
			
		}
		
	};

	$(document).on('ajaxComplete', function (e, xhr, settings) {

		if (settings.url.includes('page_data') || settings.url.includes('shared_data') || settings.url.includes('inpage_edit')) {
			Automad.blockEditor.init();
		}

	});
	
}(window.Automad = window.Automad || {}, jQuery);