<?php 
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
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Move a page to an existing parent page.
 */


$output = array();


// Validation of $_POST.
// To avoid all kinds of unexpected trouble, the URL and the destination must exist in the Automad's collection and a title must be present.
if (isset($_POST['url']) && isset($_POST['title']) && isset($_POST['destination']) && array_key_exists($_POST['url'], $this->collection) && array_key_exists($_POST['destination'], $this->collection) && $_POST['title']) {
	
	// The home page can't be moved!	
	if ($_POST['url'] != '/') {
		
		$Page = $this->collection[$_POST['url']];
		$dest = $this->collection[$_POST['destination']];
		
		// Check if new parent directory is writable.
		if (is_writable(AM_BASE_DIR . AM_DIR_PAGES . $dest->path)) {
	
			// Check if the current page's directory and parent directory is writable.
			if (is_writable(dirname($this->pageFile($Page))) && is_writable(dirname(dirname($this->pageFile($Page))))) {
	
				// Move page
				$newPagePath = $this->movePage($Page->path, $dest->path, $this->extractPrefixFromPath($Page->path), $_POST['title']);
	
				// Clear the cache to make sure, the changes get reflected on the website directly.
				$Cache = new Cache();
				$Cache->clear();
	
				// Rebuild Automad object, since the file structure has changed.
				$Automad = new Automad(false);
				$collection = $Automad->getCollection();

				// Find new URL and return redirect query string.
				foreach ($collection as $key => $page) {

					if ($page->path == $newPagePath) {
		
						$output['redirect'] = '?context=edit_page&url=' . urlencode($key);
						break;
		
					}

				}
		
			} else {
				
				$output['error'] = $this->tb['error_permission'] . '<p>' . dirname(dirname($this->pageFile($Page))) . '</p>';
				
			}
		
		} else {
			
			$output['error'] = $this->tb['error_permission'] . '<p>' . AM_BASE_DIR . AM_DIR_PAGES . rtrim($dest->path, '/') . '</p>';
			
		}
			
	} 	

} else {
	
	$output['error'] = $this->tb['error_page_not_found']; 
	
}


// Echo JSON
echo json_encode($output);


?>