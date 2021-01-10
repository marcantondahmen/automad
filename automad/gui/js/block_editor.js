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
 

class AutomadBlockUtils {

	static get cls() {

		return {
			editor: 'codex-editor',
			actionsButton: 'ce-toolbar__actions',
			actionsOpened: 'ce-toolbar__actions--opened',
			block: 'ce-block',
			blockFocused: 'ce-block--focused',
			blockContent: 'ce-block__content',
			input: 'cdx-input',
			settingsButton: 'cdx-settings-button',
			settingsLayout: 'am-block-settings-layout'
		}

	}

	static applyLayout(editor, data) {

		for (var i = 0; i < editor.blocks.getBlocksCount(); i++) {

			var block = editor.blocks.getBlockByIndex(i).holder,
				span = data.blocks[i].data.span;

			block.className = block.className.replace(/span\-\d+/g, '');
			block.classList.toggle(`span-${span}`, (span !== undefined && span != ''));

		}

	}

	static alignButton(editor) {

		var editorId = editor.configuration.holder,
			container = document.getElementById(editorId),
			button = container.querySelector(`.${AutomadBlockUtils.cls.actionsButton}`),
			blockId = editor.blocks.getCurrentBlockIndex(),
			block = editor.blocks.getBlockByIndex(blockId).holder,
			blockContent = block.querySelector(`.${AutomadBlockUtils.cls.blockContent}`);

		button.style.transform = 'translate3d(0,0,0)';

		var blockRight = blockContent.getBoundingClientRect().right,
			buttonRight = button.getBoundingClientRect().right,
			blockTop = blockContent.getBoundingClientRect().top,
			buttonTop = button.getBoundingClientRect().top,
			right = buttonRight - blockRight,
			top = blockTop - buttonTop;

		button.style.transform = `translate3d(-${right}px,${top}px,0)`;
		
	}

	static settingsButtonObserver(editor) {

		var editorId = editor.configuration.holder,
			alignButton = function () { AutomadBlockUtils.alignButton(editor) };

		$(document).on(
			'mousedown click',
			`#${editorId} .${AutomadBlockUtils.cls.block}`,
			function () {
				setTimeout(alignButton, 50);
			}
		);

		$(document).on(
			'mousedown click',
			`#${editorId} .${AutomadBlockUtils.cls.settingsLayout} div`,
			alignButton
		);

		$(document).on(
			'dragend mouseup',
			`#${editorId} .${AutomadBlockUtils.cls.actionsButton} div`,
			alignButton
		);

	}

	static renderLayoutSettings (data, savedData, api, withStretch) {

		var element = Automad.util.create.element,
			cls = api.styles.settingsButton,
			clsActive = api.styles.settingsButtonActive,
			wrapper = element('div', [AutomadBlockUtils.cls.settingsLayout]),
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

			button.innerHTML = `<svg width="20px" height="20px" viewBox="0 0 20 20">${option.icon}</svg>`;
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

	}

}
	
+function(Automad, $) {
	
	Automad.blockEditor = {
		
		dataAttr: 'data-am-block-editor',

		init: function() {
			
			var be = Automad.blockEditor,
				selector = '[' + be.dataAttr + ']',
				triggerChange = function() {

					var block = $(this)
								.closest(`.${AutomadBlockUtils.cls.editor}`)
								.find(`.cdx-block, .${AutomadBlockUtils.cls.blockContent}`)
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

							AutomadBlockUtils.applyLayout(editor, data);
							AutomadBlockUtils.alignButton(editor);

						});
					
					},

					onReady: function() {
						
						var undo = new Undo({ editor });

						undo.initialize(data);

						new DragDrop(editor);
						
						AutomadBlockUtils.applyLayout(editor, data);
						AutomadBlockUtils.settingsButtonObserver(editor);

						$wrapper.find('.codex-editor__redactor').removeAttr('style');
						
					}

				});

			});

			// Trigger changes when clicking a settings button or changing an input field.
			$(document).on('click', `.${AutomadBlockUtils.cls.settingsButton}`, triggerChange);
			$(document).on('change keyup', `.${AutomadBlockUtils.cls.input}, .${AutomadBlockUtils.cls.block} input, .${AutomadBlockUtils.cls.block} select`, triggerChange);
			
			// Blur focus on block when clicking outside.
			$(window).on('click', function() {
				$(`.${AutomadBlockUtils.cls.blockFocused}`).removeClass(AutomadBlockUtils.cls.blockFocused);
				$(`.${AutomadBlockUtils.cls.actionsOpened}`).removeClass(AutomadBlockUtils.cls.actionsOpened);
			});
			
			$(document).on(
				'blur click', 
				`.${AutomadBlockUtils.cls.blockFocused} [contenteditable], .${AutomadBlockUtils.cls.actionsButton}`, 
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