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
 *	Copyright (c) 2014-2018 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Dashboard class loads the required dashboard elements for the requested context. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2014-2018 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Dashboard {
	
	
	/**
	 *	The Automad object.
	 */
	
	private $Automad;
	
	
	/**
	 *	The Content object.
	 */
	
	private $Content;
	
	
	/**
	 *	The Html object.
	 */
	
	private $Html;
	
	
	/**
	 *	The Keys object.
	 */
	
	private $Keys;
	
	
	/**
	 * 	The Themelist object.
	 */
	
	private $Themelist;
	
	
	/**
	 *	Automad's collection.
	 */	
		
	private $collection;


	/**
	 * 	The site name.
	 */
	
	private $sitename;
	
	
	/**
	 *	The GUI page's title.
	 */
	
	private $guiTitle = 'Automad';
	
	
	/**
	 *	The GUI's buffered HTML.
	 */
	
	public $output;
	

	/**
	 *	Load the site's global data and include modules according to the current context.
	 *
	 *	When a the GUI gets initialized, at first it gets verified, if an user is logged in or not. That has the highest priority.
	 *	If no user is logged in, the existance of "config/accounts.txt" gets checked and either the login or the install module gets loaded.
	 *	In case an ajax request is sent while a user got already logged out (in another browser tab or window), the response will be a 'redirect' to the GUI 
	 *	login page, to log in there.
	 *
	 *	If an user is logged in, the Automad object gets created and the current "context" gets determined from the GET parameters.
	 *	According to that context, the matching module gets loaded after verifying its exsistence. 
	 *	When there is no context passed via get, it gets checked for "ajax", to possibly call a matching ajax module.
	 *	If both is negative, the dashboard module gets included.
	 *
	 *	Example Context: 	http://domain.com/gui?context=edit_page will include 	automad/gui/context/edit_page.php
	 *	Example Ajax:		http://domain.com/gui?ajax=page_data will include 	automad/gui/ajax/page_data.php
	 *
	 *	Since every request for the gui (pages and ajax) gets still routed over "/index.php" > "/automad/init.php" > new GUI\Dashboard(), 
	 *	all the session/login checking needs only to be done here once, simply because all modules get includede here.   
	 */
	
	public function __construct() {
		
		// Load text modules.
		Text::parseModules();
		
		// Create temporary Shared object before a user gets logged in and 
		// the Automad object didn't get created to always have a value for sitename. 
		$Shared = new Core\Shared();
		$this->sitename = $Shared->get(AM_KEY_SITENAME);
		
		// Check if an user is logged in.
		if (User::get()) {
			
			// Create Automad object.
			$this->Automad = new Core\Automad();
	
			// If user is logged in, continue with getting the Automad object and the collection.
			$this->collection = $this->Automad->getCollection();
			
			// Create objects.
			$this->Content = new Content($this->Automad);
			$this->Html = new Html($this->Automad);
			$this->Keys = new Keys($this->Automad);
			$this->Themelist = new Themelist();
					
			// Check if context/ajax matches an existing .php file.
			// If there is no (or no matching context), load the dashboard page.
			if (in_array(AM_BASE_DIR . AM_DIR_GUI_INC . '/context/' . Core\Parse::query('context') . '.php', glob(AM_BASE_DIR . AM_DIR_GUI_INC . '/context/*.php'))) {		
				$inc = 'context/' . Core\Parse::query('context');
			} else if (in_array(AM_BASE_DIR . AM_DIR_GUI_INC . '/ajax/' . Core\Parse::query('ajax') . '.php', glob(AM_BASE_DIR . AM_DIR_GUI_INC . '/ajax/*.php'))) {		
				$inc = 'ajax/' . Core\Parse::query('ajax');
			} else {
				$inc = 'start';
			}
	
		} else if (Core\Parse::query('ajax')) {
			
			// Send a redirect URL as answer to an Ajax request when nobody is logged in.
			die(json_encode(array('redirect' => AM_BASE_INDEX . AM_PAGE_DASHBOARD)));
			
		} else {
	
			// If no user is logged in and there is no Ajax request, check if accounts.txt exists. If yes, set $inc to the login page, else to the installer.
			if (file_exists(AM_FILE_ACCOUNTS)) {
				$inc = 'login';
			} else {
				$inc = 'install';
			}

		}
		
		// Buffer the HTML to merge the output with the debug log in init.php.
		ob_start();
		
		// Load page according to the current context.
		require AM_BASE_DIR . AM_DIR_GUI_INC . '/' . $inc . '.php';	
		
		$this->output = Prefix::tags(ob_get_contents());
		ob_end_clean();
		
	}
	

	/**
	 *	Load GUI element from automad/gui/elements.
	 *
	 *	@param string $element
	 */
	
	private function element($element) {
		
		require AM_BASE_DIR . AM_DIR_GUI_INC . '/elements/' . $element . '.php';
		
	}


}
