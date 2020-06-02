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
 *	Copyright (c) 2016-2020 by Marc Anton Dahmen
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


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('search_title') . ' > "' . Core\Request::query('query') . '"';
$this->element('header');


$results = $this->getContent()->getSearchResults();


?>
		
		<ul class="uk-subnav uk-subnav-pill uk-margin-top">
			<li class="uk-disabled"><i class="uk-icon-search"></i></li>
			<li><a href=""><?php Text::e('search_title'); ?></a></li>
		</ul>
		<h2 class="uk-margin-top-remove">
			<i class="uk-icon-angle-double-left"></i>
			<?php echo Core\Request::query('query'); ?>
			<i class="uk-icon-angle-double-right"></i>&nbsp;
			<span class="uk-badge"><?php echo count($results); ?></span>
		</h2>
		
<?php

if ($results) {
	
	echo Components\Grid\Pages::render($results);
	
} else {
	
?>
		
		<div class="uk-alert uk-alert-danger uk-margin-top">
			<?php Text::e('search_no_results'); ?>
		</div>
			
<?php
	
}


$this->element('footer');


?>