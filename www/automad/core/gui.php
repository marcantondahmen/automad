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
 *	The GUI class provides all methods used for the GUI. 
 */


class GUI {
	
	
	/**
	 *	The Site's collection.
	 */	
		
	public $collection;
	
	
	/**
	 *	The GUI page's title.
	 */
	
	public $guiTitle = 'Automad';
	
	
	/**
	 *	The GUI's buffered HTML.
	 */
	
	public $output;
		
		
	/**
	 *	The Site's data and settings.
	 */
	
	public $siteData;
	
	
	/**
	 *	Load the site's global data and include modules according to the current context.
	 *
	 *	When a the GUI gets initialize, at first it gets verified, if an user is logged in or not. That has the highest priority.
	 *	If no user is logged in, the existance of "config/accounts.txt" gets checked and either the login or the install module gets loaded.
	 *
	 *	If an user is logged in, the Site object gets created and the current "context" gets determined from the GET parameters.
	 *	According that context, the matching module gets loaded after verifying its exsistance. 
	 *	When there is no context passed via get, it gets checked for "ajax", to possibly call a matching ajax module.
	 *	If both is negative, the start page's module gets included.
	 *
	 *	Example Context: 	http://domain.com/gui?context=edit_page will include 	automad/gui/context/edit_page.php
	 *	Example Ajax:		http://domain.com/gui?ajax=page_data will include 	automad/gui/ajax/page_data.php
	 *
	 *	Since every request for the gui (pages and ajax) gets still routed over "/index.php" > "/automad/init.php" > new GUI(), 
	 *	all the session/login checking needs only to be done here once, simply because all modules get includede here.   
	 */
	
	public function __construct() {
		
		$defaults = array(AM_KEY_SITENAME => $_SERVER['SERVER_NAME']);
		$this->siteData = array_merge($defaults, Parse::textFile(AM_FILE_SITE_SETTINGS));
		
		session_start();
		
		// Check if an user is logged in.
		if ($this->user()) {
	
			// If user is logged in, continue with getting the Site object and the collection.
			$S = new Site(false);
			$this->collection = $S->getCollection();
				
			// Check if context/ajax matches an existing .php file.
			// If there is no (or no matching context), load the start page.
			if (in_array(AM_BASE_DIR . '/automad/gui/context/' . Parse::queryKey('context') . '.php', glob(AM_BASE_DIR . '/automad/gui/context/*.php'))) {		
				$inc = 'context/' . Parse::queryKey('context');
			} else if (in_array(AM_BASE_DIR . '/automad/gui/ajax/' . Parse::queryKey('ajax') . '.php', glob(AM_BASE_DIR . '/automad/gui/ajax/*.php'))) {		
				$inc = 'ajax/' . Parse::queryKey('ajax');
			} else {
				$inc = 'start';
			}
	
		} else {
	
			// If no user is logged in, check if accounts.txt exists. If yes, set $inc to the login page, else to the installer.
			if (file_exists(AM_FILE_ACCOUNTS)) {
				$inc = 'login';
			} else {
				$inc = 'install';
			}

		}
		
		// Buffer the HTML to merge the output with the debug log in init.php.
		ob_start();
		
		// Load page according to the current context.
		require AM_BASE_DIR . '/automad/gui/' . $inc . '.php';	
		
		$this->output = ob_get_contents();
		ob_end_clean();
		
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
		
		// Normalize parent path.
		$newParentPath = '/' . ltrim(trim($newParentPath, '/') . '/', '/');
		
		// Not only sanitize strings, but also remove all dots, to make sure a single dot will work fine as a prefix.title separator.
		$prefix = ltrim(Parse::sanitize(str_replace('.', '_', $prefix)) . '.', '.');
		$title = Parse::sanitize(str_replace('.', '_', $title)) . '/';

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
		
			$old = umask(0);		
			
			if (!file_exists(AM_BASE_DIR . AM_DIR_PAGES . $newParentPath)) {
				mkdir(AM_BASE_DIR . AM_DIR_PAGES . $newParentPath, 0777, true);
			}
			
			rename(AM_BASE_DIR . AM_DIR_PAGES . $oldPath, AM_BASE_DIR . AM_DIR_PAGES . $newPath);
			
			umask($old);
		
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
	 *	@param array $parameters (additional query string parameters to be passed along with the url)
	 *	@param boolean $hideCurrent
	 *	@return the branch's HTML
	 */
	
	public function siteTree($parent, $collection, $current, $parameters, $hideCurrent = false) {
		
		$selection = new Selection($collection);
		$selection->filterByParentUrl($parent);
		$selection->sortPagesByBasename();
		
		if ($pages = $selection->getSelection()) {
			
			$html = '<ul class="nav nav-pills nav-stacked">';
			
			foreach ($pages as $page) {
				
				if ($page->url != $current || !$hideCurrent) {
				
					if (!$title = basename($page->path)) {
						$title = 'home';	
					}
				
					// Check if page is currently selected page
					if ($page->url == $current) {
						$class = ' class="active"';
					} else {
						$class = '';
					}
				
					$html .= 	'<li' . $class . '><a href="?' . http_build_query(array_merge($parameters, array('url' => $page->url))) . '"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;&nbsp;' . $title . '</a>' . 
							$this->siteTree($page->url, $collection, $current, $parameters, $hideCurrent) . '</li>';
				
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
	 *	Create a select box containing all installed themes/templates to be included in a HTML form.
	 *
	 *	@param string $id (HTML id)
	 *	@param string $name (Fieldname)
	 *	@param string $selectedTheme
	 *	@param string $selectedTemplate
	 *	@return The HTML for the select box including a label and a wrapping div.
	 */

	public function templateSelectBox($id = '', $name = '', $selectedTheme = false, $selectedTemplate = false) {
		
		
		// Find all templates of currently used site theme (set in site.txt).
		$siteThemeTemplates = 	array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/' . $this->siteData[AM_KEY_THEME] . '/*.php'), function($file) {
						return false === in_array(basename($file), array('error.php', 'results.php'));
					});

		// Find all templates of all installed themes.
		$templates = 		array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/*/*.php'), function($file) {
						return false === in_array(basename($file), array('error.php', 'results.php'));
					});
		
		// Create HTML
		$html = '<div class="form-group"><label for="' . $id . '" class="text-muted">Template</label><select id="' . $id . '" class="form-control input-sm" name="' . $name . '" size="1">'; 
		
		// List templates of current sitewide theme
		foreach($siteThemeTemplates as $template) {

			$html .= '<option';

			if (!$selectedTheme && basename($template) === $selectedTemplate . '.php') {
				 $html .= ' selected';
			}

			$html .= ' value="' . basename($template) . '">' . ucwords(str_replace('.php', '', basename($template))) . ' (Global Theme)</option>';

		}

		// List all found template along with their theme folder
		foreach($templates as $template) {

			$html .= '<option';

			if ($selectedTheme === basename(dirname($template)) && basename($template) === $selectedTemplate . '.php') {
				 $html .= ' selected';
			}

			$html .= ' value="' . basename(dirname($template)) . '/' . basename($template) . '">' . ucwords(basename(dirname($template))) . ' Theme > ' . ucwords(str_replace('.php', '', basename($template))) . '</option>';
		}
		
		$html .= '</select></div>';
		
		return $html;
		
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