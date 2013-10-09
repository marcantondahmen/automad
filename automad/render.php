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


echo "<br><pre>";

$site->sortSiteByPath(SORT_DESC);
$site->sortSiteByTitle();

//print_r ($site->getCollection());
print_r ($site->filterSiteByParentRelUrl(''));
//print_r ($site->filterSiteByLevel(1));
//print_r ($site->filterSiteByTag('Culture'));
//print_r ($site->filterSiteByKeywords('neotax modules urban'));
echo "</pre>";

echo "<br>";
echo "Automad " . VERSION;

?>
