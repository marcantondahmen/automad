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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	Move a page to the trash directory and redirect to its parent page afterwards.
 */


$output = array();


// Validate $_POST.
if (isset($_POST['url']) && array_key_exists($_POST['url'], $this->collection) && $_POST['url'] != '/' && isset($_POST['title']) && $_POST['title']) {
	
	$P = $this->collection[$_POST['url']];
	$this->movePage($P->path, '..' . AM_DIR_TRASH . dirname($P->path), $this->extractPrefixFromPath($P->path), $_POST['title']);
	$output['redirect'] = '?context=edit_page&url=' . urlencode($P->parentUrl);
	
} else {
	
	$output['error'] = 'This page can not be deleted!';
	
}


echo json_encode($output);


?>