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
 *	Add a new page below the current page.
 */


$output = array();


// Validation of $_POST. URL, title and template must exist and != false.
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->collection)) {
	
	if (isset($_POST['subpage']) && isset($_POST['subpage']['title']) && $_POST['subpage']['title'] && isset($_POST['subpage']['theme_template']) && $_POST['subpage']['theme_template']) {
		
		// The current page, where the subpage has to be added to, becomes the parent page for the new page.
		$Page = $this->collection[$_POST['url']];
	
		// Check if the current page's directory is writable.
		if (is_writable(dirname($this->pageFile($Page)))) {
	
	
			// The new page's properties
			$title = $_POST['subpage']['title'];
			$theme_template = $_POST['subpage']['theme_template'];


			// Build initial content for data file.
			$content = AM_KEY_TITLE . AM_PARSE_PAIR_SEPARATOR . ' ' . $title;
	

			// The new page's theme.
			if (dirname($theme_template) != '.') {
				$content .= "\r\n\r\n" . AM_PARSE_BLOCK_SEPARATOR . "\r\n\r\n" . AM_KEY_THEME . AM_PARSE_PAIR_SEPARATOR . ' ' . dirname($theme_template);
			} 


			// Save new subpage below the current page's path.		
			$subdir = Parse::sanitize($title, true) . '/';
			$newPagePath = $Page->path . $subdir;


			$i = 1;

			// In case page exists already...
			while (file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newPagePath)) {
				$newPagePath = $Page->path . $i . '.' . $subdir;		
				$i++;	
			}


			// Build the file name. 
			$file = AM_BASE_DIR . AM_DIR_PAGES . $newPagePath . str_replace('.php', '', basename($theme_template)) . '.' . AM_FILE_EXT_DATA;


			// Save content.
			$old = umask(0);

			if (!file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newPagePath)) {
				mkdir(AM_BASE_DIR . AM_DIR_PAGES . $newPagePath, 0777, true);
			}

			file_put_contents($file, $content);

			umask($old);


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
			
			$output['error'] = $this->tb['error_permission'] . '<p>' . dirname($this->pageFile($Page)) . '</p>';
			
		}
		
	} else {
	
		$output['error'] = $this->tb['error_page_title'];
	
	}	
	
} else {
	
	$output['error'] = $this->tb['error_page_not_found'];
	
}	


echo json_encode($output);


?>