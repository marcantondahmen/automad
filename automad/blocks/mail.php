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
	 *	Send mail.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the sendig status
	 */

	public static function send($data, $Automad) {

		// Define field names.
		$honeypot = 'human';
		$from = 'from';
		$subject = 'subject';
		$message = 'message';
		
		// Basic checks.
		if (empty($_POST) || empty($data->to)) {
			return false;
		}
	
		// Check optional honeypot to verify human.
		if (isset($_POST[$honeypot]) && $_POST[$honeypot] != false) {
			return false;
		}
	
		// Check if form fields are not empty.
		if (empty($_POST[$from]) || empty($_POST[$subject]) || empty($_POST[$message])) {
			return "<h3>$data->error</h3>";
		}
	
		// Prepare mail.
		$subject = $Automad->Shared->get(AM_KEY_SITENAME) . ': ' . strip_tags($_POST[$subject]);
		$message = strip_tags($_POST[$message]);
		$header = 'From: ' . preg_replace('/[^\w\d\.@\-]/', '', $_POST[$from]);
	
		if (mail($data->to, $subject, $message, $header)) {
			return "<h3>$data->success</h3>";
		}

	}


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

			$status = self::send($data, $Automad);

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