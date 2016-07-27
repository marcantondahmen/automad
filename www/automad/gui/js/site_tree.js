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
 *	Copyright (c) 2014-2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


 /*
  *	Initialize the site tree having a 'data-automad-tree' attribute by creating toggle buttons 
  *	for all nodes and collapsing all inactive ones.
  *
  *     The site tree can also be used to select pages and inject their URL in to an input field 
  *     instead of just navigating to that page. 
  *     To use a site tree for selection only, the 'data-automad-tree' attribute must be 
  *     set to the selector of the input field.
  *
  *     Leaving the attribute empty will disable selection and keep the default link behavior instead.
  *
  *	<div data-automad-tree="#automad-move-page-input">
  *		...
  *	</div>
  *      
  */

+function(Automad, $) {

	Automad.siteTree = {
		
		class: {
			
			active: 'uk-active',
			iconOpen: 'automad-tree-icon-open',
			iconClose: 'automad-tree-icon-close',
			toggle: 'automad-tree-toggle'
			
		},
		
		dataAttr: 'data-automad-tree',
		
		// The site tree can also be used to select an URL to be used as an input field value.
		urlAsValue: function($tree, $input) {
	
			var	t = Automad.siteTree,
				$links = $tree.find('a[href^="?"]');
			
			$links.click(function(e) {
			
				e.preventDefault();
				
				var	$link = $(this),
					url = decodeURIComponent($link.attr('href').split('=')[1]);
			
				$tree.find('.' + t.class.active).removeClass(t.class.active);
				$link.parent().addClass(t.class.active);
				$input.val(url).trigger('change');
				
			});
		
		},
		
		// Initialize the site tree.
		init:	function() {
			
			var	t = Automad.siteTree,
				$trees = $('[' + t.dataAttr + ']');
			
			$trees.each(function() {
				
				var	$tree = $(this),
					inputSelector = $tree.data(Automad.util.dataCamelCase(t.dataAttr));
				
				$tree.find('li li').has('ul').each(function() {

					var 	$node = 	$(this),
						$children = 	$node.children('ul'),
						$button = 	$('<a></a>', { class: t.class.toggle, href: '#' }).prependTo($node),
						$icon =		$('<i></i>').prependTo($button);
				
					// Collapse the tree only for the non-active pages/path.
					if ($node.hasClass(t.class.active) || $node.find('.' + t.class.active).length > 0) {
						$icon.addClass(t.class.iconClose);
					} else {
						$icon.addClass(t.class.iconOpen);
						$children.hide();
					}
			
					// Toggle class and visibility.
					$button.click(function(e) {
						e.preventDefault();
						$children.toggle();
						$icon.toggleClass(t.class.iconOpen + ' ' + t.class.iconClose);
					});
			
				});
				
				// Use the URL as input value if data-automad-tree is not empty.
				if (inputSelector) {
					t.urlAsValue($tree, $(inputSelector));
				}
				
			});
			
		}
		
	}

	$(document).ready(Automad.siteTree.init);
	
}(window.Automad = window.Automad || {}, jQuery);