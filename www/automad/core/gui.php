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
	 *	Extract the deepest directory's prefix from a given path.
	 *
	 *	@return Prefix
	 */

	public function extractPrefixFromPath($path) {
		
		return substr(basename($path), 0, strpos(basename($path), '.'));
			
	}


	/**
	 *	Move a page's directory to a new location.
	 *	The final path is composed of the parent directoy, the prefix and the title.
	 *	In case the resulting path is already occupied, an index get appended to the prefix, to be reproducible when resaving the page.
	 *
	 *	@param string $oldPath
	 *	@param string $newParentPath (destination)
	 *	@param string $prefix
	 *	@param string $title
	 *	@return $newPath
	 */

	public function movePage($oldPath, $newParentPath, $prefix, $title) {
		
		// Normalize & sanitize parts.
		$newParentPath = '/' . ltrim(trim($newParentPath, '/') . '/', '/');
		$prefix = ltrim($this->sanitize($prefix) . '.', '.');
		$title = $this->sanitize($title) . '/';

		// Build new path.
		$newPath = $newParentPath . $prefix . $title;
			
		// Contiune only if old and new paths are different.	
		if ($oldPath != $newPath) {
			
			$i = 1;
			
			// Check if path exists already
			while (file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newPath)) {
				
				$newPrefix = ltrim(trim($prefix, '.') . '-' . $i, '-') . '.';
				$newPath = $newParentPath . $newPrefix . $title;
				$i++;
				
			}
			
			rename(AM_BASE_DIR . AM_DIR_PAGES . $oldPath, AM_BASE_DIR . AM_DIR_PAGES . $newPath);
			
		}
		
		return $newPath;
		
	}


	/**
	 *	Return the full file system path of a page's data file.
	 *
	 *	@param object $page
	 *	@return Filename
	 */

	public function pageFile($page) {
		
		return AM_BASE_DIR . AM_DIR_PAGES . $page->path . $page->template . '.' . AM_FILE_EXT_DATA;
	
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
	 *	Sanitize a string to be valid as pathname.
	 *
	 *	@param string $str
	 *	@return Sanitized string
	 */
	
	private function sanitize($str) {
			
		$search  = array(' ','&'  ,'/','*','+'  ,'@','ä','ö','ü','å','ø','á','à','é','è');
		$replace = array('_','and','-','x','and','_at_','a','o','u','a','o','a','a','e','e');
		
		return preg_replace('/[^\w_\-]/', '_',str_replace($search, $replace, strtolower(trim($str))));
		
	}
	
	
	/**
	 *	Save the user accounts as serialized array to config/accounts.txt.
	 */
	
	public function saveAccounts($array) {
		
		return file_put_contents(AM_FILE_ACCOUNTS, serialize($array));
		
	}
	
		
	/**
	 *	Create recursive site tree for editing a page. 
	 *	Every page link sends a post request to gui/pages.php containing the page's url.
	 *
	 *	@param string $parent
	 *	@param array $collection
	 *	@param string $current (URL)
	 *	@param boolean $hideCurrent
	 *	@return the branch's HTML
	 */
	
	public function siteTree($parent, $collection, $current, $hideCurrent = false) {
		
		$selection = new Selection($collection);
		$selection->filterByParentUrl($parent);
		$selection->sortPagesByBasename();
		
		if ($pages = $selection->getSelection()) {
			
			$html = '<ul class="' . AM_HTML_CLASS_TREE . '">';
			
			foreach ($pages as $page) {
				
				if ($page->url != $current || !$hideCurrent) {
				
					if (!$title = basename($page->path)) {
						$title = 'home';	
					}
				
					// Check if page is currently selected page
					if ($page->url == $current) {
						$class = ' class="selected"';
					} else {
						$class = '';
					}
				
					$html .= 	'<li>' . 
							'<form method="post">' . 
							'<input type="hidden" name="url" value="' . $page->url . '" />' . 
							'<input' . $class . ' type="submit" value="' . $title . '" />' . 
							'</form>' .
							$this->siteTree($page->url, $collection, $current, $hideCurrent) .
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