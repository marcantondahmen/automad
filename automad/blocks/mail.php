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
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Blocks;
use Automad\System as System;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The mail block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Mail {


	/**	
	 *	Render a mail form block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {

		if (!empty($data->to)) {

			$defaults = array(
				'error' => '',
				'success' => '',
				'placeholderEmail' => '',
				'placeholderSubject' => '',
				'placeholderMessage' => '',
				'textButton' => ''
			);

			$options = array_merge($defaults, (array) $data);
			$data = (object) $options;

			$status = System\Mail::send($data, $Automad);

			if ($status) {
				$status = "<h3>$status</h3>";
			}

			return <<< HTML
					$status
					<form action="" method="post" class="am-mail-form">	
						<input type="text" name="human" value="">	
						<input class="am-mail-input" type="text" name="from" value="" placeholder="$data->placeholderEmail">
						<input class="am-mail-input" type="text" name="subject" value="" placeholder="$data->placeholderSubject">
						<textarea class="am-mail-input" name="message" placeholder="$data->placeholderMessage"></textarea>
						<button class="am-mail-button" type="submit">$data->textButton</button>	
					</form>
HTML;

		}

	}


}