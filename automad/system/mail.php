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


namespace Automad\System;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The mail class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Mail {


	/**	
	 * 	Save status to avoid a second trigger for example in pagelists or teaser snippets.
	 */

	private static $sent = false;


	/**	
	 *	Send mail.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the sendig status
	 */

	public static function send($data, $Automad) {

		// Prevent a second call.
		if (self::$sent) {
			return $data->success;
		}

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
			return $data->error;
		}
	
		// Prepare mail.
		$subject = $Automad->Shared->get(AM_KEY_SITENAME) . ': ' . strip_tags($_POST[$subject]);
		$message = strip_tags($_POST[$message]);
		$header = 'From: ' . preg_replace('/[^\w\d\.@\-]/', '', $_POST[$from]);
		
		if (mail($data->to, $subject, $message, $header)) {
			self::$sent = true;
			return $data->success;
		}

	}


}