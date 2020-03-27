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

		embedServices: {
			youtube: true,
			codepen: true,
			vimeo: true,
			mixcloud: {
				regex: /https?:\/\/www\.mixcloud\.com\/(.+)\/$/,
				embedUrl: 'https://www.mixcloud.com/widget/iframe/?hide_cover=1&light=1&feed=/<%= remote_id %>/',
				html: '<iframe height="180" scrolling="no" frameborder="no" allowtransparency="true" allowfullscreen="true" style="width: 100%;"></iframe>',
				height: 180,
				width: 600
			},
			soundcloud: {
				regex: /(https:\/\/soundcloud\.com\/.+)/,
				embedUrl: 'https://w.soundcloud.com/player/?url=<%= remote_id %>&color=%234a4a4a',
				html: '<iframe height="166" scrolling="no" frameborder="no" allowtransparency="true" allowfullscreen="true" style="width: 100%;"></iframe>',
				height: 166,
				width: 600
			}
		},

		init: function() {
			
			var be = Automad.blockEditor,
				selector = '[' + be.dataAttr + ']';

			$(selector).each(function() {

				var $wrapper = $(this),
					holder = $wrapper.data(Automad.util.dataCamelCase(be.dataAttr)),
					$input = $wrapper.find('input'),
					ready = false,
					data,
					editor;
						
				try {
					data = JSON.parse($input.val());
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
						header: {
							class: Header,
							shortcut: 'CMD+SHIFT+H',
							inlineToolbar: true,
							config: {
								levels: [1, 2, 3, 4, 5, 6],
								defaultLevel: 2
							}
						},
						list: {
							class: List,
							inlineToolbar: true,
						},
						image: AutomadImage,
						quote: {
							class: Quote,
							inlineToolbar: true
						},
						table: {
							class: Table,
						},
						raw: RawTool,
						code: CodeTool,
						delimiter: Delimiter,
						embed: {
							class: Embed,
							config: {
								services: Automad.blockEditor.embedServices
							}
						},
						inlineCode: {
							class: InlineCode
						},
						marker: {
							class: Marker
						}
					},

					onChange: function() { 

						// Catch the initial change event by testing the ready variable.
						if (ready) {
							editor.save().then(function(data) {
								$input.val(JSON.stringify(data)).trigger('change');
							});
						}
					
					},

					onReady: function() {
						
						$wrapper.find('.codex-editor__redactor').removeAttr('style');

						// Delay setting ready to be true to catch the initial change event.
						setTimeout(function () {
							ready = true;
						}, 2000);

					}

				});

			});

		}
		
	};

	$(document).on('ajaxComplete', Automad.blockEditor.init);
	
}(window.Automad = window.Automad || {}, jQuery);