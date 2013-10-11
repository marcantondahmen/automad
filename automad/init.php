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
 *	(c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 */
 

// Auto load classes
spl_autoload_register(function ($class) {
		
	$class = strtolower($class);
	include BASE . '/automad/core/' . $class . '.php';
	
});


// Constants
include BASE . '/automad/const.php';

/*
// Initialize Page
if (isset($_SERVER["PATH_INFO"])) {
	$page = new Page($_SERVER["PATH_INFO"]);	
} else {
	$page = new Page('/');
}


// Render Page
$page->render();
*/

$s = new Site();
$sel = new Selection($s->getCollection());

$sel->filterByTag('Culture');


echo "<pre>";
print_r($sel);
echo "</pre>";


echo "<br><br><br>";
echo "Automad " . VERSION;


?>