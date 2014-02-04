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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	Class GUI. 
 */


class GUI {


	/**
	 *	Page title (used within elements/header.php).
	 */
	
	public $guiTitle = '';
	
	
	/**
	 *	Content for the Jquery UI dialog, called in elements/footer.php. 
	 */
	
	public $modalDialogContent = '';
	
	
	/**
	 *	The Site's data and settings.
	 */
	
	public $siteData = array();
	
	
	/**
	 *	Get global site's data.
	 */
	
	public function __construct() {
		
		$defaults = 	array(	
					AM_KEY_SITENAME => $_SERVER['SERVER_NAME']  
				);
		
		$this->siteData = array_merge($defaults, Parse::textFile(AM_FILE_SITE_SETTINGS));
		
	}
	
	
	/**
	 *	Load GUI element from automad/gui/elements.
	 *
	 *	@param string $element
	 */
	
	public function element($element) {
		
		require AM_BASE_DIR . '/automad/gui/elements/' . $element . '.php';
		
	}


	/**
	 *	Create hash from password to store in accounts.txt.
	 *
	 *	@param string $password
	 *	@return Hashed/salted password
	 */

	public function passwordHash($password) {
		
		$salt = '$2y$10$' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 22);
		
		return crypt($password, $salt);
		
	}


	/**
	 *	Verify if a password matches its hashed version.
	 *
	 *	@param string $password (clear text)
	 *	@param string $hash (hashed password)
	 *	@return true/false 
	 */

	public function passwordVerified($password, $hash) {
		
		return ($hash === crypt($password, $hash));
		
	}

	
	/**
	 *	Save the user accounts as serialized array to config/accounts.txt.
	 */
	
	public function saveAccounts($array) {
		
		return file_put_contents(AM_BASE_DIR . AM_FILE_ACCOUNTS, serialize($array));
		
	}
	
		
	/**
	 *	Create recursive site tree for editing a page. 
	 *	Every page link sends a post request to gui/pages.php containing the page's url.
	 *
	 *	@param string $parent
	 *	@param array $collection
	 *	@param boolean $hideCurrent
	 *	@return the branch's HTML
	 */
	
	public function siteTree($parent, $collection, $hideCurrent = false) {
		
		$selection = new Selection($collection);
		$selection->filterByParentUrl($parent);
		$selection->sortPagesByBasename();
		
		if ($pages = $selection->getSelection()) {
			
			$html = '<ul class="' . AM_HTML_CLASS_TREE . '">';
			
			if (isset($_POST['url'])) {
				$selected = $_POST['url'];
			} else {
				$selected = false;
			}
			
			foreach ($pages as $page) {
				
				if ($page->url != $selected || !$hideCurrent) {
				
					if (!$title = basename($page->path)) {
						$title = 'home';	
					}
				
					// Check if page is currently selected page
					if ($page->url == $selected) {
						$class = ' class="selected"';
					} else {
						$class = '';
					}
				
					$html .= 	'<li>' . 
							'<form action="' . AM_BASE_URL . '/automad/gui/pages.php' . '" method="post">' . 
							'<input type="hidden" name="url" value="' . $page->url . '" />' . 
							'<input' . $class . ' type="submit" value="' . $title . '" />' . 
							'</form>' .
							$this->siteTree($page->url, $collection, $hideCurrent) .
							'</li>';
				
				}
				
			}
			
			$html .= '</ul>';
		
			return $html;
			
		}
		
	}
	

	/**
	 *	Return the Site's name.
	 *
	 *	@return Site's name
	 */
	
	public function siteName() {
		
		return $this->siteData[AM_KEY_SITENAME];
		
	}


	/**
	 *	Return the currently logged in user.
	 * 
	 *	@return username
	 */

	public function user() {
		
		if (isset($_SESSION['username'])) {
			return $_SESSION['username'];
		}
		
	}
	
	
}


?>