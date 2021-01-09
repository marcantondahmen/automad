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
			
		},

		renderLayoutSettings: function (block) {

			var element = Automad.util.create.element,
				cls = block.api.styles.settingsButton,
				clsActive = block.api.styles.settingsButtonActive,
				wrapper = element('div', ['am-block-settings-layout']),
				keys = {
					stretch: 'stretched',
					span: 'span'
				},
				stretchOption = {
					title: 'Stretch',
					icon: '<svg width="17" height="10" viewBox="0 0 17 10"><path d="M13.568 5.925H4.056l1.703 1.703a1.125 1.125 0 0 1-1.59 1.591L.962 6.014A1.069 1.069 0 0 1 .588 4.26L4.38.469a1.069 1.069 0 0 1 1.512 1.511L4.084 3.787h9.606l-1.85-1.85a1.069 1.069 0 1 1 1.512-1.51l3.792 3.791a1.069 1.069 0 0 1-.475 1.788L13.514 9.16a1.125 1.125 0 0 1-1.59-1.591l1.644-1.644z"/></svg>'
				},
				stretchWrapper = element('div', ['cdx-settings-1-1']),
				stretchButton = element('div', [cls]),
				spanWrapper = element('div', ['cdx-settings-5']),
				spanOptions = [
					{
						title: 'Span 1⁄4',
						icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H5V2h11 c1.1,0,2,0.9,2,2V16z"/>',
						value: '3'
					},
					{
						title: 'Span 1⁄3',
						icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2H7V2h9 c1.1,0,2,0.9,2,2V16z"/>',
						value: '4'
					},
					{
						title: 'Span 1⁄2',
						icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2h-6V2h6 c1.1,0,2,0.9,2,2V16z"/>',
						value: '6'
					},
					{
						title: 'Span 2⁄3',
						icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2h-3V2h3 c1.1,0,2,0.9,2,2V16z"/>',
						value: '8'
					},
					{
						title: 'Span 3⁄4',
						icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z M18,16c0,1.1-0.9,2-2,2h-1V2h1 c1.1,0,2,0.9,2,2V16z"/>',
						value: '9'
					}
				],
				clearSpanSettings = function() {

					const spanButtons = spanWrapper.querySelectorAll('.' + cls);

					Array.from(spanButtons).forEach((button) => {
						button.classList.remove(clsActive);
					});

					block.data[keys.span] = '';

				};

			// Stretch button.
			stretchButton.innerHTML = stretchOption.icon;
			stretchButton.classList.toggle(clsActive, block.data[keys.stretch]);
			stretchWrapper.appendChild(stretchButton);
			block.api.tooltip.onHover(stretchButton, stretchOption.title, { placement: 'top' });

			Promise.resolve().then(() => {
				block.api.blocks.stretchBlock(block.api.blocks.getCurrentBlockIndex(), block.data[keys.stretch]);
			});

			stretchButton.addEventListener('click', function () {
				clearSpanSettings();
				stretchButton.classList.toggle(clsActive);
				block.data[keys.stretch] = !block.data[keys.stretch];
				block.api.blocks.stretchBlock(block.api.blocks.getCurrentBlockIndex(), block.data[keys.stretch]);
			});

			// Span buttons.
			spanOptions.forEach(function (option) {

				var button = element('div', [cls]);

				button.innerHTML = `<svg width="20px" height="20px" viewBox="0 0 20 20">${option.icon}</svg>`;
				button.classList.toggle(clsActive, (block.data[keys.span] == option.value));

				button.addEventListener('click', function () {

					var span = block.data[keys.span];

					stretchButton.classList.toggle(clsActive, false);
					block.data[keys.stretch] = false;
					block.api.blocks.stretchBlock(block.api.blocks.getCurrentBlockIndex(), block.data[keys.stretch]);
					clearSpanSettings();

					if (span == option.value) {
						block.data[keys.span] = '';
					} else {
						button.classList.toggle(clsActive, true);
						block.data[keys.span] = option.value;
					}

				});
				
				block.api.tooltip.onHover(button, option.title, { placement: 'top' });
				spanWrapper.appendChild(button);

			});

			wrapper.appendChild(stretchWrapper);
			wrapper.appendChild(spanWrapper);

			return wrapper;

		}
		
	};

	$(document).on('ajaxComplete', function (e, xhr, settings) {

		if (settings.url.includes('page_data') || settings.url.includes('shared_data') || settings.url.includes('inpage_edit')) {
			Automad.blockEditor.init();
		}

	});
	
}(window.Automad = window.Automad || {}, jQuery);