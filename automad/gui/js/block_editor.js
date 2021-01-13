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

		cls: {
			editor: 'codex-editor',
			actionsButton: 'ce-toolbar__actions',
			actionsOpened: 'ce-toolbar__actions--opened',
			block: 'ce-block',
			blockFocused: 'ce-block--focused',
			blockContent: 'ce-block__content',
			input: 'cdx-input',
			settingsButton: 'cdx-settings-button',
			settingsLayout: 'am-block-settings-layout'
		},

		applyLayout: function(editor) {

			editor.save().then((data) => {

				for (var i = 0; i < editor.blocks.getBlocksCount(); i++) {

					var block = editor.blocks.getBlockByIndex(i).holder,
						span;

					if (data.blocks[i] !== undefined) {
						span = data.blocks[i].data.span;
						block.className = block.className.replace(/span\-\d+/g, '');
						block.classList.toggle(`span-${span}`, (span !== undefined && span != ''));
					}

				}

			});

		},

		alignButton: function(editor) {

			var editorId = editor.configuration.holder,
				container = document.getElementById(editorId),
				button = container.querySelector(`.${Automad.blockEditor.cls.actionsButton}`),
				blockId = editor.blocks.getCurrentBlockIndex(),
				block = editor.blocks.getBlockByIndex(blockId).holder,
				blockContent = block.querySelector(`.${Automad.blockEditor.cls.blockContent}`);

			button.style.transform = 'translate3d(0,0,0)';

			var blockRight = blockContent.getBoundingClientRect().right,
				buttonRight = button.getBoundingClientRect().right,
				blockTop = blockContent.getBoundingClientRect().top,
				buttonTop = button.getBoundingClientRect().top,
				right = buttonRight - blockRight,
				top = blockTop - buttonTop;

			button.style.transform = `translate3d(-${right}px,${top}px,0)`;

		},

		settingsButtonObserver: function(editor) {

			var editorId = editor.configuration.holder,
				alignButton = function () { Automad.blockEditor.alignButton(editor) };

			$(document).on(
				'mousedown click',
				`#${editorId} .${Automad.blockEditor.cls.block}`,
				function () {
					setTimeout(alignButton, 50);
				}
			);

			$(document).on(
				'mousedown click',
				`#${editorId} .${Automad.blockEditor.cls.settingsLayout} div`,
				alignButton
			);

			$(document).on(
				'dragend mouseup',
				`#${editorId} .${Automad.blockEditor.cls.actionsButton} div`,
				alignButton
			);

		},

		renderLayoutSettings: function(data, savedData, api, withStretch) {

			var element = Automad.util.create.element,
				cls = api.styles.settingsButton,
				clsActive = api.styles.settingsButtonActive,
				wrapper = element('div', [Automad.blockEditor.cls.settingsLayout]),
				keys = {
					stretch: 'stretched',
					span: 'span'
				},
				stretchOption = {
					title: 'Stretch',
					icon: '<svg height="1.25em" width="3.75em" viewBox="0 0 60 20"><path d="M41,0H19c-1.7,0-3,1.3-3,3v14c0,1.7,1.3,3,3,3h22c1.7,0,3-1.3,3-3V3C44,1.3,42.7,0,41,0z M42.9,10.4 c-0.1,0.1-0.1,0.2-0.2,0.3l-5,5C37.5,15.9,37.3,16,37,16s-0.5-0.1-0.7-0.3c-0.4-0.4-0.4-1,0-1.4l3.3-3.3H20.4l3.3,3.3 c0.4,0.4,0.4,1,0,1.4C23.5,15.9,23.3,16,23,16s-0.5-0.1-0.7-0.3l-5-5c-0.1-0.1-0.2-0.2-0.2-0.3c-0.1-0.2-0.1-0.5,0-0.8 c0.1-0.1,0.1-0.2,0.2-0.3l5-5c0.4-0.4,1-0.4,1.4,0s0.4,1,0,1.4L20.4,9h19.2l-3.3-3.3c-0.4-0.4-0.4-1,0-1.4s1-0.4,1.4,0l5,5 c0.1,0.1,0.2,0.2,0.2,0.3C43,9.9,43,10.1,42.9,10.4z"/><path d="M11.5,20L11.5,20c-0.8,0-1.5-0.7-1.5-1.5v-17C10,0.7,10.7,0,11.5,0h0C12.3,0,13,0.7,13,1.5v17C13,19.3,12.3,20,11.5,20z"/><path d="M48.5,20L48.5,20c-0.8,0-1.5-0.7-1.5-1.5v-17C47,0.7,47.7,0,48.5,0l0,0C49.3,0,50,0.7,50,1.5v17C50,19.3,49.3,20,48.5,20z"/></svg>'
				},
				stretchWrapper = element('div', ['cdx-settings-1-1']),
				stretchButton = element('div', [cls]),
				spanWrapper = element('div', ['cdx-settings-6']),
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
					},
					{
						title: 'Span 1⁄1',
						icon: '<path d="M16,0H4C1.8,0,0,1.8,0,4v12c0,2.2,1.8,4,4,4h12c2.2,0,4-1.8,4-4V4C20,1.8,18.2,0,16,0z"/>',
						value: '12'
					}
				],
				clearSpanSettings = function () {

					const spanButtons = spanWrapper.querySelectorAll('.' + cls),
						block = api.blocks.getBlockByIndex(api.blocks.getCurrentBlockIndex()).holder;

					Array.from(spanButtons).forEach((button) => {
						button.classList.remove(clsActive);
					});

					block.className = block.className.replace(/span\-\d+/g, '');
					data[keys.span] = '';

				};

			// Stretch button.
			if (withStretch) {

				data[keys.stretch] = savedData[keys.stretch] !== undefined ? savedData[keys.stretch] : false;

				stretchButton.innerHTML = stretchOption.icon;
				stretchButton.classList.toggle(clsActive, data[keys.stretch]);
				stretchWrapper.appendChild(stretchButton);
				api.tooltip.onHover(stretchButton, stretchOption.title, { placement: 'top' });

				Promise.resolve().then(() => {
					api.blocks.stretchBlock(api.blocks.getCurrentBlockIndex(), data[keys.stretch]);
				});

				stretchButton.addEventListener('click', function () {
					clearSpanSettings();
					stretchButton.classList.toggle(clsActive);
					data[keys.stretch] = !data[keys.stretch];
					api.blocks.stretchBlock(api.blocks.getCurrentBlockIndex(), data[keys.stretch]);
				});

				wrapper.appendChild(stretchWrapper);

			}

			// Span buttons.
			data[keys.span] = savedData[keys.span] || '';

			spanOptions.forEach(function (option) {

				var button = element('div', [cls]);

				button.innerHTML = `<svg width="16px" height="16px" viewBox="0 0 20 20">${option.icon}</svg>`;
				button.classList.toggle(clsActive, (data[keys.span] == option.value));

				button.addEventListener('click', function () {

					var span = data[keys.span],
						block = api.blocks.getBlockByIndex(api.blocks.getCurrentBlockIndex()).holder;

					stretchButton.classList.toggle(clsActive, false);
					data[keys.stretch] = false;
					api.blocks.stretchBlock(api.blocks.getCurrentBlockIndex(), data[keys.stretch]);
					clearSpanSettings();

					if (span == option.value) {
						data[keys.span] = '';
					} else {
						button.classList.toggle(clsActive, true);
						block.classList.toggle(`span-${option.value}`, true);
						data[keys.span] = option.value;
					}

				});

				api.tooltip.onHover(button, option.title, { placement: 'top' });
				spanWrapper.appendChild(button);

			});

			wrapper.appendChild(spanWrapper);

			return wrapper;

		},

		init: function() {
			
			var be = Automad.blockEditor,
				selector = '[' + be.dataAttr + ']',
				triggerChange = function() {

					var block = $(this)
								.closest(`.${be.cls.editor}`)
								.find(`.cdx-block, .${be.cls.blockContent}`)
								.first()
								.get(0),
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
							class: AutomadHeader,
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
							class: AutomadTable,
							inlineToolbar: true
						},
						quote: {
							class: AutomadQuote,
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
						embed: AutomadEmbed,
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
						
						var undo = new Undo({ editor });

						undo.initialize(data);

						new DragDrop(editor);
						
						Automad.blockEditor.applyLayout(editor);
						Automad.blockEditor.settingsButtonObserver(editor);

						$(window).bind('keydown', function (e) {

							if (e.ctrlKey || e.metaKey) {

								let key = String.fromCharCode(e.which).toLowerCase();

								if (key == 'z' || key == 'y') {
									setTimeout(function () {
										Automad.blockEditor.applyLayout(editor);
										Automad.blockEditor.alignButton(editor);
									}, 50);
								}
								
							}

						});

						$wrapper.find('.codex-editor__redactor').removeAttr('style');
						
					}

				});

			});

			// Trigger changes when clicking a settings button or changing an input field.
			$(document).on('click', `.${Automad.blockEditor.cls.settingsButton}`, triggerChange);
			$(document).on('change keyup', `.${Automad.blockEditor.cls.input}, .${Automad.blockEditor.cls.block} input, .${Automad.blockEditor.cls.block} select`, triggerChange);
			
			// Blur focus on block when clicking outside.
			$(window).on('click', function() {
				$(`.${Automad.blockEditor.cls.blockFocused}`).removeClass(Automad.blockEditor.cls.blockFocused);
			});
			
			$(document).on(
				'blur click', 
				`.${Automad.blockEditor.cls.blockFocused} [contenteditable], .${Automad.blockEditor.cls.actionsButton}`, 
				function(event) {
					event.stopPropagation();
				}
			);
			
		}

	};

	$(document).on('ajaxComplete', function (e, xhr, settings) {

		if (settings.url.includes('page_data') || settings.url.includes('shared_data') || settings.url.includes('inpage_edit')) {
			Automad.blockEditor.init();
		}

	});
	
}(window.Automad = window.Automad || {}, jQuery);