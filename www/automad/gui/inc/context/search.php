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


/*
 *	The GUI search results page.
 */


$this->guiTitle = $this->guiTitle . ' / ' . Text::get('search_title') . ' > "' . Core\Parse::queryKey('query') . '"';
$this->element('header');
$this->element('title');
$results = $this->Content->getSearchResults();


?>

		<div class="automad-navbar" data-uk-sticky="{showup:true,animation:'uk-animation-slide-top'}">
			<?php $this->element('searchbar'); ?>
		</div>
		<div class="uk-block">
			<div class="uk-panel uk-panel-box uk-panel-box-primary">
				<?php echo Text::get('search_title'); ?>&nbsp;&nbsp;
				<i class="uk-icon-angle-right"></i>&nbsp;&nbsp;
				"<?php echo Core\Parse::queryKey('query'); ?>" (<?php echo count($results); ?>)
			</div>
		</div>

<?php

if ($results) {
	
	echo $this->Html->pageGrid($results);
	
} else {
	
	?>
		
		<div class="uk-alert uk-alert-danger uk-margin-remove"><?php echo Text::get('search_no_results'); ?></div>
			
	<?php
	
}


$this->element('footer');


?>