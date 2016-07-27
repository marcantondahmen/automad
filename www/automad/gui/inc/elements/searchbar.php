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
 *	Copyright (c) 2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


?>
		
			<div class="uk-width-1-1">
				<a class="automad-searchbar-toggle-sidebar uk-hidden-large uk-button uk-button-large" data-uk-offcanvas="{target:'#automad-sidebar'}"><i class="uk-icon-navicon"></i></a>
				<form class="automad-searchbar uk-form" action="" method="get">
					<input type="hidden" name="context" value="search">	
					<div class="uk-form uk-autocomplete uk-width-1-1" data-uk-autocomplete='{source: <?php echo $this->Content->getAutoCompleteJSON(); ?>, minLength: 2}'>
				    		<input class="uk-form-large uk-width-1-1" name="query" type="text" placeholder="<?php echo Text::get('search_placeholder'); ?>" required>	
					</div>
					<button type="submit" class="uk-button uk-button-large"><i class="uk-icon-search"></i></button>
				</form>
			</div>
