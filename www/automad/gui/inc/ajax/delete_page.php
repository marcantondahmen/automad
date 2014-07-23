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
 *	Move a page to the trash directory and redirect to its parent page afterwards.
 */


$output = array();


// Validate $_POST.
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->collection) && $_POST['url'] != '/' && isset($_POST['title']) && $_POST['title']) {

	$Page = $this->collection[$_POST['url']];

	// Check if the page's directory and parent directory are wirtable.
	if (is_writable(dirname($this->pageFile($Page))) && is_writable(dirname(dirname($this->pageFile($Page))))) {

		$this->movePage($Page->path, '..' . AM_DIR_TRASH . dirname($Page->path), $this->extractPrefixFromPath($Page->path), $_POST['title']);
		$output['redirect'] = '?context=edit_page&url=' . urlencode($Page->parentUrl);

		$Cache = new Cache();
		$Cache->clear();

	} else {
		
		$output['error'] = $this->tb['error_permission'] . '<p>' . dirname(dirname($this->pageFile($Page))) . '</p>';
		
	}
	
} 


echo json_encode($output);


?>