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
 *	Copyright (c) 2017-2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	CodeMirror defaults. 
 */
	
+function($, UIkit) {
	
	UIkit.on('beforeready.uk.dom', function() {
		
		$.extend(UIkit.components.htmleditor.prototype.defaults, {
			markdown: true,
			codemirror: { 
				mode: 'gfm', 
				lineWrapping: true, 
				dragDrop: false, 
				autoCloseTags: false, 
				matchTags: false, 
				autoCloseBrackets: true, 
				matchBrackets: true, 
				indentUnit: 4, 
				indentWithTabs: false, 
				tabSize: 4, 
				hintOptions: {
					completionSingle: false
				},
				extraKeys: {
					"Enter": "newlineAndIndentContinueMarkdownList"
				}
			},
			toolbar: [ 'bold', 'italic', 'link', 'image', 'blockquote', 'listUl', 'listOl' ]
		});
		
	});
	
}(jQuery, UIkit);