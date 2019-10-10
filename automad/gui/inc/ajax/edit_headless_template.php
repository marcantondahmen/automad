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
 *	Copyright (c) 2019 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Headless template editor.
 */

$output = array();

if (!empty($_POST['template'])) {

	if (FileSystem::write(AM_BASE_DIR . AM_HEADLESS_TEMPLATE_CUSTOM, $_POST['template'])) {
		$output['success'] = Text::get('success_saved');
	}

} else {

	// Start buffering the HTML.
	ob_start();

	?>
		
		<textarea class="am-form-code uk-form-controls uk-width-1-1" name="template" rows="10"><?php 
			echo htmlspecialchars(Headless::loadTemplate()); 
		?></textarea>

	<?php

	// Save buffer to JSON array.
	$output['html'] = ob_get_contents();
	ob_end_clean();	

}

$this->jsonOutput($output);

?>