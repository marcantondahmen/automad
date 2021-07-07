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
 *	Copyright (c) 2014-2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\UI;

use Automad\Core\Debug;
use Automad\Core\Request;
use Automad\UI\Controllers\User;
use Automad\UI\Utils\Prefix;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The dashboard class handles all user interactions using the dashboard. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2014-2021 Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Dashboard {
	
	
	/**
	 *	The dashboard output.
	 */
	
	private $output;
	

	/**
	 *	The dashboard constructor.
	 */
	
	public function __construct() {

		// Load text modules.
		Text::parseModules();

		$namespaceViews = __NAMESPACE__ . '\\Views\\';
		$namespaceControllers = __NAMESPACE__ . '\\Controllers\\';
		
		if (User::get()) {

			if ($controller = Request::query('controller')) {

				// Controllers.
				header('Content-Type: application/json');
				$output = call_user_func("{$namespaceControllers}{$controller}");
				$output['debug'] = Debug::getLog();
				$this->output = json_encode($output, JSON_UNESCAPED_SLASHES);

			} else {

				// Views.
				$view = Request::query('view');
				
				if (!$view) {
					$view = 'Home';
				}

				$class = "{$namespaceViews}{$view}";
				$object = new $class;
				$this->output = $object->render();

			}
	
		} else {

			// In case a controller is requested without being authenticated, redirect page to login page.
			if (Request::query('controller')) {
				header('Content-Type: application/json');
				die(json_encode(array('redirect' => AM_BASE_INDEX . AM_PAGE_DASHBOARD)));
			}

			$view = 'CreateUser';

			if (file_exists(AM_FILE_ACCOUNTS)) {
				$view = 'Login';
			}

			$class = "{$namespaceViews}{$view}";
			$object = new $class;
			$this->output = $object->render();

		}
		
	}
	

	/**
	 *	Get the rendered output.
	 *
	 *	@return string the rendered output.
	 */

	public function get() {

		$this->output = preg_replace('/^\t{0,3}/m', '', $this->output);

		return Prefix::tags($this->output);

	}


}
