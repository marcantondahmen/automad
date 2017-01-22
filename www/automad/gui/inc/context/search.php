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
 *	Copyright (c) 2016-2017 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	The GUI search results page.
 */


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('search_title') . ' > "' . Core\Parse::queryKey('query') . '"';
$this->element('header');


$results = $this->Content->getSearchResults();


?>
	
		<ul class="uk-subnav uk-subnav-pill uk-margin-large-top">
			<li class="uk-disabled"><i class="uk-icon-search"></i></li>
			<li><a href=""><?php Text::e('search_title'); ?></a></li>
		</ul>
		<h3 class="uk-margin-top">
			<i class="uk-icon-angle-double-left"></i>&nbsp;
			<i><?php echo Core\Parse::queryKey('query'); ?></i>&nbsp;
			<i class="uk-icon-angle-double-right"></i>
			<span class="uk-badge uk-float-right_"><?php echo count($results); ?></span>
		</h3>
		
<?php

if ($results) {
	
	echo $this->Html->pageGrid($results);
	
} else {
	
?>
		
		<div class="uk-alert uk-alert-danger uk-margin-remove"><?php Text::e('search_no_results'); ?></div>
			
<?php
	
}


$this->element('footer');


?>