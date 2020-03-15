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
						image: Image,
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
								services: {
									youtube: true
								}
							}
						}
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