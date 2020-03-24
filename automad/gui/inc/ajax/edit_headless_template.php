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
 *	Copyright (c) 2019-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Headless template editor.
 */

$output = array();

if ($template = Core\Request::post('template')) {

	if (FileSystem::write(AM_BASE_DIR . AM_HEADLESS_TEMPLATE_CUSTOM, $template)) {
		$Cache = new Core\Cache();
		$Cache->clear();
		$output['success'] = Text::get('success_saved');
	}

} else {

	// Start buffering the HTML.
	ob_start();

	?>
		<div class="uk-overflow-container">
			<textarea class="uk-form-controls uk-width-1-1" name="template" rows="10"><?php 
				echo htmlspecialchars(Headless::loadTemplate()); 
			?></textarea>			
		</div>
	<?php

	// Save buffer to JSON array.
	$output['html'] = ob_get_contents();
	ob_end_clean();	

}

$this->jsonOutput($output);

?>