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

				var $container = $(this),
					holder = $container.data(Automad.util.dataCamelCase(be.dataAttr)),
					$input = $container.find('input'),
					data,
					editor;
					
				try {
					data = JSON.parse($input.val());
				} catch (e) {
					data = {};
				}
				
				// Remove data attribute to prevent multiple initializations.
				$container.removeAttr(be.dataAttr);

				editor = new EditorJS({

					holder: holder,
					logLevel: 'ERROR',
					data: data,
					tools: {
						header: {
							class: Header,
							shortcut: 'CMD+SHIFT+H',
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
						quote: Quote,
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
						link2: AutomadLink
					},

					onChange: function() { 
						editor.save().then(function(data) {
							$input.val(JSON.stringify(data)).trigger('change');
						});
					},

					onReady: function() {
						$container.find('.codex-editor__redactor').removeAttr('style');
					}

				});

			});

		}
		
	};

	$(document).on('ajaxComplete', Automad.blockEditor.init);
	
}(window.Automad = window.Automad || {}, jQuery);