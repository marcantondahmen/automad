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
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The GUI class provides all methods used for the GUI. 
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class GUI {
	
	
	/**
	 *	Automad's collection.
	 */	
		
	private $collection;
	
	
	/**
	 *	The GUI page's title.
	 */
	
	private $guiTitle = 'Automad';
	
	
	/**
	 *	The GUI's buffered HTML.
	 */
	
	public $output;
		
		
	/**
	 *	The Site's data and settings.
	 */
	
	private $siteData;
	
	
	/**
	 *	Text blocks to be used for feedback, button text and alerts.
	 */
	
	private $tb = array();
	
	
	/**
	 *	Load the site's global data and include modules according to the current context.
	 *
	 *	When a the GUI gets initialize, at first it gets verified, if an user is logged in or not. That has the highest priority.
	 *	If no user is logged in, the existance of "config/accounts.txt" gets checked and either the login or the install module gets loaded.
	 *
	 *	If an user is logged in, the Automad object gets created and the current "context" gets determined from the GET parameters.
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
		
		// Get Site data.
		$this->siteData = \Automad\Core\Parse::siteData();
		
		// Get all GUI text blocks.
		$this->tb = \Automad\Core\Parse::markdownFile(AM_BASE_DIR . '/automad/gui/text_blocks.txt');
		
		// Start Session.
		session_start();
		
		// Check if an user is logged in.
		if ($this->user()) {
	
			// If user is logged in, continue with getting the Automad object and the collection.
			$Automad = new \Automad\Core\Automad(false);
			$this->collection = $Automad->getCollection();
				
			// Check if context/ajax matches an existing .php file.
			// If there is no (or no matching context), load the start page.
			if (in_array(AM_BASE_DIR . AM_DIR_GUI_INC . '/context/' . \Automad\Core\Parse::queryKey('context') . '.php', glob(AM_BASE_DIR . AM_DIR_GUI_INC . '/context/*.php'))) {		
				$inc = 'context/' . \Automad\Core\Parse::queryKey('context');
			} else if (in_array(AM_BASE_DIR . AM_DIR_GUI_INC . '/ajax/' . \Automad\Core\Parse::queryKey('ajax') . '.php', glob(AM_BASE_DIR . AM_DIR_GUI_INC . '/ajax/*.php'))) {		
				$inc = 'ajax/' . \Automad\Core\Parse::queryKey('ajax');
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
		require AM_BASE_DIR . AM_DIR_GUI_INC . '/' . $inc . '.php';	
		
		$this->output = ob_get_contents();
		ob_end_clean();
		
	}
	
	
	/**
	 *	Generate the PHP code for the accounts file. Basically the code returns the unserialized serialized array with all users.
	 *	That way, the accounts array can be stored as PHP.
	 *	The accounts file has to be a PHP file for security reasons. When trying to access the file directly via the browser, 
	 *	it gets executed instead of revealing any user names.
	 *	
	 *	@param array $accounts
	 *	@return The PHP code as string
	 */
	
	private function accountsGeneratePHP($accounts) {
		
		return 	"<?php defined('AUTOMAD') or die('Direct access not permitted!');\n" .
			'return unserialize(\'' . serialize($accounts) . '\');' .
			"\n?>";
			
	} 
	
	
	/**
	 *	Get the accounts array by including the accounts PHP file.
	 *
	 *	@return The accounts array
	 */
	
	private function accountsGetArray() {
		
		return (include AM_FILE_ACCOUNTS);
		
	}
	
	
	/**
	 *	Save the accounts array as PHP to AM_FILE_ACCOUNTS.
	 *
	 *	@return Success (true/false)
	 */

	private function accountsSaveArray($accounts) {
		
		return @file_put_contents(AM_FILE_ACCOUNTS, $this->accountsGeneratePHP($accounts));
		
	}
	
	
	/**
	 *	Load GUI element from automad/gui/elements.
	 *
	 *	@param string $element
	 */
	
	private function element($element) {
		
		require AM_BASE_DIR . AM_DIR_GUI_INC . '/elements/' . $element . '.php';
		
	}


	/**
	 *	Extract the deepest directory's prefix from a given path.
	 *
	 *	@return Prefix
	 */

	private function extractPrefixFromPath($path) {
		
		return substr(basename($path), 0, strpos(basename($path), '.'));
			
	}


	/**
	 *	Get a list (array) of all variables used in a template file and its nested elements.
	 * 	This method also parses extensions and toolbox methods and therefore also finds all variables created dynamically by these methods. 
	 *
	 *	@param string $theme
	 *	@param string $template
	 *	@return Array of matched variables
	 */

	private function getPageVarsInTemplate($theme, $template) {
		
		// A new full (!) Automad object has to be created, since the template's methods need all page data to succeed.
		$Automad = new \Automad\Core\Automad();
		
		// Get template form the Site's theme, if $theme is false.
		if (!$theme) {
			$theme = $this->siteData[AM_KEY_THEME];
		}
		
		$file = AM_BASE_DIR . AM_DIR_THEMES . '/' . $theme . '/' . $template . '.php';
		
		// Get template file content including all nested elements.
		$content = \Automad\Core\Parse::templateBuffer($file, $Automad);
		$content = \Automad\Core\Parse::templateNestedIncludes($content, dirname($file), $Automad);
		
		// Find all variables within the template existing before parsing any method.
		preg_match_all(AM_REGEX_PAGE_VAR, $content, $matches);
				
		$vars = $matches[1];
		
		// Parse all the template's methods to get also the variables generated by these methods.
		// In case the page gets redirected, the parsing gets skipped to avoid errors due to the fact, that $Page->url won't be a key
		// within the $collection array. That issue only occures when the GUI parses these pages, since the would normally be redirected before parsing.
		if (!isset($Automad->getCurrentPage()->data[AM_KEY_URL])) {
			$content = \Automad\Core\Parse::templateMethods($content, $Automad);
		}
		
		// Now find all variables generated by the template's methods by parsing again the content for the second time.
		preg_match_all(AM_REGEX_PAGE_VAR, $content, $matches);
		
		$vars = array_merge($vars, $matches[1]);
		
		// Clean up before returning to remove all doubled items due to parsing twice (before parsing methods and after).
		// All variables are stored in $vars in order of appearance.
		return array_unique($vars);
			
	}


	/**
	 *	Collect all site variables used in any .php file below the /themes directory.
	 * 	
	 * 	Note: Unlike getPageVarsInTemplate(), this method doesn't parse any extensions or tools, since there is no current page when editing global settings.
	 * 	Therefore it is not possible to find any shared variable which gets created by an extension or a toolbox method.
	 * 	Only variables which are hardcoded in the template files or its nested elements get collected.
	 * 	As a good practice, it should be avoided within any extension to create site variables dynamically. That should only be done with page variables.
	 *	
	 *	@return Array with site variables
	 */

	private function getSiteVarsInThemes() {

		// Collect all .php files below "/themes"
		$dir = AM_BASE_DIR . AM_DIR_THEMES;	
		$arrayDirs = array();
		$arrayFiles = array();
		
		while ($dirs = glob($dir . '/*', GLOB_ONLYDIR)) {
			$dir .= '/*';
			$arrayDirs = array_merge($arrayDirs, $dirs);
		}
		
		foreach ($arrayDirs as $d) {
			if ($f = glob($d . '/*.php')) {
				$arrayFiles = array_merge($arrayFiles, $f);
			}
		}

		// Scan content of all the files for site variables.
		$content = '';
		
		foreach ($arrayFiles as $file) {
			$content .= file_get_contents($file);
		}

		preg_match_all(AM_REGEX_SITE_VAR, $content, $matches);
		sort($matches[1]);

		return array_unique($matches[1]);
		
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

	private function movePage($oldPath, $newParentPath, $prefix, $title) {
		
		// Normalize parent path.
		$newParentPath = '/' . ltrim(trim($newParentPath, '/') . '/', '/');
		
		// Not only sanitize strings, but also remove all dots, to make sure a single dot will work fine as a prefix.title separator.
		$prefix = ltrim(\Automad\Core\Parse::sanitize($prefix, true) . '.', '.');
		$title = \Automad\Core\Parse::sanitize($title, true) . '/';

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

	private function pageFile($page) {
		
		return AM_BASE_DIR . AM_DIR_PAGES . $page->path . $page->template . '.' . AM_FILE_EXT_DATA;
	
	}


	/**
	 *	Create hash from password to store in accounts.txt.
	 *
	 *	@param string $password
	 *	@return Hashed/salted password
	 */

	private function passwordHash($password) {
		
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

	private function passwordVerified($password, $hash) {
		
		return ($hash === crypt($password, $hash));
		
	}

			
	/**
	 *	Create recursive site tree for editing a page. 
	 *	Every page link sends a post request to gui/pages.php containing the page's url.
	 *
	 *	@param string $parent
	 *	@param array $collection
	 *	@param array $parameters (additional query string parameters to be passed along with the url)
	 *	@param boolean $hideCurrent
	 *	@return the branch's HTML
	 */
	
	private function siteTree($parent, $collection, $parameters, $hideCurrent = false) {
		
		$current = \Automad\Core\Parse::queryKey('url');
		
		$selection = new \Automad\Core\Selection($collection);
		$selection->filterByParentUrl($parent);
		$selection->sortPagesByBasename();
		
		if ($pages = $selection->getSelection()) {
			
			$html = '<ul class="nav nav-pills nav-stacked pages">';
			
			foreach ($pages as $page) {
				
				if ($page->url != $current || !$hideCurrent) {
				
					if (!$title = basename($page->path)) {
						$title = '<span class="glyphicon glyphicon-home"></span> Home';	
					}
	
					// Check if page is currently selected page
					if ($page->url == $current) {
						$html .= '<li class="active"><div class="connector"></div>';
					} else {
						$html .= '<li>';
					}
					
					$html .= '<a title="' . basename($page->path) . '" href="?' . http_build_query(array_merge($parameters, array('url' => $page->url)), '', '&amp;') . '">' . $title . '</a>' .
						 $this->siteTree($page->url, $collection, $parameters, $hideCurrent) .
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
	
	private function siteName() {
		
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

	private function templateSelectBox($id = '', $name = '', $selectedTheme = false, $selectedTemplate = false) {
		
		
		// Find all templates of currently used site theme (set in site.txt).
		$siteThemeTemplates = 	array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/' . $this->siteData[AM_KEY_THEME] . '/*.php'), function($file) {
						return false === in_array(basename($file), array(AM_PAGE_NOT_FOUND_TEMPLATE . '.php', AM_PAGE_RESULTS_TEMPLATE . '.php'));
					});

		// Find all templates of all installed themes.
		$templates = 		array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/*/*.php'), function($file) {
						return false === in_array(basename($file), array(AM_PAGE_NOT_FOUND_TEMPLATE . '.php', AM_PAGE_RESULTS_TEMPLATE . '.php'));
					});
		
		// Create HTML
		$html = '<div class="form-group"><label for="' . $id . '">' . $this->tb['page_theme_template'] . '</label><select id="' . $id . '" class="form-control" name="' . $name . '">'; 
		
		// List templates of current sitewide theme
		foreach($siteThemeTemplates as $template) {

			$html .= '<option';

			if (!$selectedTheme && basename($template) === $selectedTemplate . '.php') {
				 $html .= ' selected';
			}

			$html .= ' value="' . basename($template) . '">' . ucwords(str_replace(array('_', '.php'), array(' ', ''), basename($template))) . ' (Global Theme)</option>';

		}

		// List all found template along with their theme folder
		foreach($templates as $template) {

			$html .= '<option';

			if ($selectedTheme === basename(dirname($template)) && basename($template) === $selectedTemplate . '.php') {
				 $html .= ' selected';
			}

			$html .= ' value="' . basename(dirname($template)) . '/' . basename($template) . '">' . 
				 ucwords(str_replace('_', ' ', basename(dirname($template)))) . ' Theme > ' . ucwords(str_replace(array('_', '.php'), array(' ', ''), basename($template))) . 
				 '</option>';
		}
		
		$html .= '</select></div>';
		
		return $html;
		
	}


	/**
	 *	Return the currently logged in user.
	 * 
	 *	@return username
	 */

	private function user() {
		
		if (isset($_SESSION['username'])) {
			return $_SESSION['username'];
		}
		
	}
	
	
	/**
	 *	Create textarea for page/shared variables with optional button for removal.
	 *	
	 *	@param string $key (name)
	 *	@param string $value (value)
	 *	@param boolean $removeButton
	 *	@return The HTML for the textarea
	 */
	
	private function varTextArea($key, $value, $removeButton = false) {
		
		$html =  '<div class="form-group"><label for="input-data-' . $key . '">' . $key . '</label>';
		
		if ($removeButton) {
			$html .= '<button type="button" class="close automad-remove-parent">&times;</button>';
		}
		
		$html .= '<textarea id="input-data-' . $key . '" class="form-control" name="data[' . $key . ']" rows="10">' . $value . '</textarea></div>';
		
		return $html;
		
	}
	
	
}


?>