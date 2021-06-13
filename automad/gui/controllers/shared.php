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
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\GUI\Controllers;

use Automad\Core\Cache;
use Automad\Core\Request;
use Automad\GUI\Components\Layout\SharedData;
use Automad\GUI\Utils\FileSystem;
use Automad\GUI\Utils\Text;
use Automad\GUI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Shared data controller.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Shared {


	/**
	 *	Send form when there is no posted data in the request or save data if there is.
	 *
	 *	@return array the $output array
	 */

	public static function data() {

		$Automad = UICache::get();
		$output = array();

		if ($data = Request::post('data')) {
			// Save changes.
			$output = self::save($Automad, $data);
		} else {
			// If there is no data, just get the form ready.
			$SharedData = new SharedData($Automad);
			$output['html'] = $SharedData->render();
		}

		return $output;

	}


	/**
	 *	Save shared data.
	 *
	 *	@param object $Automad
	 *	@param array $data
	 *	@return array the $output array
	 */

	private static function save($Automad, $data) {

		$output = array();

		if (is_writable(AM_FILE_SHARED_DATA)) {

			FileSystem::writeData($data, AM_FILE_SHARED_DATA);
			Cache::clear();

			if (!empty($data[AM_KEY_THEME]) && $data[AM_KEY_THEME] != $Automad->Shared->get(AM_KEY_THEME)) {
				$output['reload'] = true;
			} else {
				$output['success'] = Text::get('success_saved');
			}
		} else {
			$output['error'] = Text::get('error_permission') . '<br /><small>' . AM_FILE_SHARED_DATA . '</small>';
		}

		return $output;
	}
	

}