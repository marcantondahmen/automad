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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


/*
 *	Initialize the site tree having a 'data-am-tree' attribute by creating toggle buttons 
 *	for all nodes and collapsing all inactive ones.
 *
 * 	The site tree can also be used to select pages and inject their URL in to an input field 
 * 	instead of just navigating to that page. 
 * 	To use a site tree for selection only, the 'data-am-tree' attribute must be 
 *  set to the selector of the input field.
 *
 *  Leaving the attribute empty will disable selection and keep the default link behavior instead.
 *
 *	<div data-am-tree="#am-move-page-input">
 *		...
 *	</div>
 *      
 */

+function(Automad, $) {

	Automad.siteTree = {
		
		class: {
			active: 'uk-active',
			iconOpen: 'am-tree-icon-open',
			iconClose: 'am-tree-icon-close',
			toggle: 'am-tree-toggle'	
		},
		
		dataAttr: 'data-am-tree',
		
		// The site tree can also be used to select an URL to be used as an input field value.
		urlAsValue: function($tree, $input) {
	
			var	t = Automad.siteTree,
				$links = $tree.find('a[href^="?"]'),
				$currentPageLink = $tree.find('.' + t.class.active + ' > a[href^="?"]');
				
			// If editing a page, initially set the URL of that page as default.
			// Note the '>' within the selector to only get the actual page and not its children.
			if ($currentPageLink.length > 0) {
				$input.val(decodeURIComponent($currentPageLink.attr('href').split('=')[1]));
			}
			
			// Register click event for all other links within the tree.
			$links.click(function(e) {
			
				e.preventDefault();
				
				var	$link = $(this),
					$parent = $link.parent(),
					url = decodeURIComponent($link.attr('href').split('=')[1]);
					
				// Remove active class from all elements.
				$tree.find('.' + t.class.active).removeClass(t.class.active);
				// Add active class to current parent.
				$parent.addClass(t.class.active);
				// If the toggle of the selected item has the icon to open that node, 
				// trigger click event. 
				// Filtering for the open icon prevents closing an already open node.
				$parent.find('> a').has('.' + t.class.iconOpen).click();
				// Update input value.
				$input.val(url).trigger('change');
				
			});
			
		},
		
		// Initialize the site trees.
		init:	function() {
			
			var	t = Automad.siteTree,
				$trees = $('[' + t.dataAttr + ']');
			
			$trees.each(function() {
				
				var	$tree = $(this),
					inputSelector = $tree.data(Automad.util.dataCamelCase(t.dataAttr));
				
				$tree.find('li li').has('li').each(function() {

					var $node = $(this),
						$children = $node.children('ul'),
						$button = $('<a></a>', { class: t.class.toggle, href: '#' }).prependTo($node),
						$icon =	$('<i></i>').prependTo($button);
				
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
				
				// Use the URL as input value if data-am-tree is not empty.
				if (inputSelector.length > 0) {
					t.urlAsValue($tree, $(inputSelector));	
				}
				
			});
			
		}
		
	}

	$(document).ready(Automad.siteTree.init);
	
}(window.Automad = window.Automad || {}, jQuery);