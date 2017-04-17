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


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('search_title') . ' > "' . Core\Parse::query('query') . '"';
$this->element('header');


$results = $this->Content->getSearchResults();


?>
	
		<div class="uk-position-relative uk-margin-large-top">
			<ul class="uk-subnav uk-subnav-pill">
				<li class="uk-disabled"><i class="uk-icon-search"></i></li>
				<li><a href=""><?php Text::e('search_title'); ?></a></li>
			</ul>
			<span class="uk-badge uk-position-top-right"><?php echo count($results); ?></span>	
		</div>
		<h2 class="uk-margin-large-top">
			<i class="uk-icon-angle-double-left"></i>
			<?php echo Core\Parse::query('query'); ?>
			<i class="uk-icon-angle-double-right"></i>
		</h2>
		
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