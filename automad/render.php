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
 

spl_autoload_register(function ($class) {
		
	$class = strtolower($class);
	include BASE . '/automad/core/' . $class . '.php';
	
});


include BASE . '/automad/const.php';







// render page

$site = new Site();

echo $site->getSiteName();
echo "<br>";
echo $site->getSiteData('credits');
echo "<br>";
echo $site->getSiteData('theme');

$selection = new Selection($site->getCollection());

$selection->filterByParentRelUrl('publications');
//$selection->filterByLevel(1);
//$selection->filterByTag('Education');
//$selection->filterByKeywords('utopia');
$selection->sortByPath();
$selection->sortByTitle();


echo "<br><pre>";
print_r ($selection->getSelection());
echo "</pre>";

echo "<br>";
echo "Automad " . VERSION;

?>
