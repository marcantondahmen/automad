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
	 *	Test if a given path resolves to a path below the base directory, to validate a user's input.
	 *
	 *	@param string $path
	 *	@return true/false
	 */

	public function isBelowBaseDir($path) {
		
		$real = realpath($path);
		
		if (substr($real, 0, strlen(AM_BASE_DIR)) == AM_BASE_DIR) {
			return true;
		} else {
			return false;
		}
		
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
		$html = '<label for="' . $id . '" class="bg input">Theme</label><div class="selectbox bg input"><select id="' . $id . '" name="' . $name . '" size="1">'; 
		
		// List templates of current sitewide theme
		foreach($siteThemeTemplates as $template) {

			$html .= '<option';

			if (!$selectedTheme && basename($template) === $selectedTemplate . '.php') {
				 $html .= ' selected';
			}

			$html .= ' value="' . basename($template) . '">' . ucwords(str_replace('.php', '', basename($template))) . ' (Theme from Site Settings)</option>';

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