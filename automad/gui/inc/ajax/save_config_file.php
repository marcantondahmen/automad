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
 *	Copyright (c) 2014-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/*
 *	Saves the config/config.php file.
 */


$output = array();

if ($json = Core\Request::post('json')) {

	$config = json_decode($json, true);
	
	if (json_last_error() === JSON_ERROR_NONE) {

		// Make sure 'php' and other PHP extensions like 'php5' are removed 
		// from the list of allowed file types.
		if (!empty($config['AM_ALLOWED_FILE_TYPES'])) {
			$config['AM_ALLOWED_FILE_TYPES'] = trim(preg_replace('/,?\s*php\w?/is', '', $config['AM_ALLOWED_FILE_TYPES']), ', ');
		}

		if (Core\Config::write($config)) {
			$output['reload'] = true;
		} else {
			$output['error'] = Text::get('error_permission') . '<br>' . AM_CONFIG;
		}

	} else {

		$output['error'] = Text::get('error_json');

	}

} else {
	
	$config = Core\Config::read();
	$json = json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
	
	$output['html'] = <<< HTML
					<div class="uk-overflow-container">
						<textarea 
						class="uk-form-controls uk-width-1-1"
						name="json"
						>$json</textarea>
					</div>
HTML;

}

$this->jsonOutput($output);


?>