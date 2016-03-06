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
 *	Copyright (c) 2016 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Html class provides all methods to generate HTML markup for the GUI. 
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2016 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Html {
	
	
	/**
	 *	The Automad object.
	 */
	
	private $Automad;
	
	
	/**
	 *	Set $this->Automad when creating an instance.
	 *
	 *	@param object $Automad
	 */
	
	public function __construct($Automad) {

		$this->Automad = $Automad;
		
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
	
	public function siteTree($parent, $collection, $parameters, $hideCurrent = false) {
		
		$current = \Automad\Core\Parse::queryKey('url');
		
		$selection = new \Automad\Core\Selection($collection);
		$selection->filterByParentUrl($parent);
		$selection->sortPages();
		
		if ($pages = $selection->getSelection()) {
			
			$html = '<ul class="nav nav-pills nav-stacked pages">';
			
			foreach ($pages as $key => $Page) {
				
				if ($key != $current || !$hideCurrent) {
				
					if (!$title = basename($Page->path)) {
						$title = '<span class="glyphicon glyphicon-home"></span> Home';	
					}
	
					// Check if page is currently selected page
					if ($key == $current) {
						$html .= '<li class="active"><div class="connector"></div>';
					} else {
						$html .= '<li>';
					}
					
					$html .= '<a title="' . basename($Page->path) . '" href="?' . http_build_query(array_merge($parameters, array('url' => $key)), '', '&amp;') . '">' . $title . '</a>' .
						 $this->siteTree($key, $collection, $parameters, $hideCurrent) .
						 '</li>';
					
				}
				
			}
			
			$html .= '</ul>';
		
			return $html;
			
		}
		
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
		
		
		// Find all templates of currently used site theme (set in /shared/data.txt).
		$siteThemeTemplates = 	array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/' . $this->Automad->Shared->get(AM_KEY_THEME) . '/*.php'), function($file) {
						return false === in_array(basename($file), array(AM_PAGE_NOT_FOUND_TEMPLATE . '.php', AM_PAGE_RESULTS_TEMPLATE . '.php'));
					});

		// Find all templates of all installed themes.
		$templates = 		array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/*/*.php'), function($file) {
						return false === in_array(basename($file), array(AM_PAGE_NOT_FOUND_TEMPLATE . '.php', AM_PAGE_RESULTS_TEMPLATE . '.php'));
					});
		
		// Create HTML
		$html = '<div class="form-group"><label for="' . $id . '">' . Text::get('page_theme_template') . '</label><select id="' . $id . '" class="form-control" name="' . $name . '">'; 
		
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
	 *	Create a form field for page/shared variables with optional button for removal and optional placeholder content (shared data).
	 *	
	 *	@param string $title
	 *	@param array $keys
	 *	@param array $data
	 *	@param boolean $removeButton
	 *	@param boolean $sharedDataPlaceholder
	 *	@return The HTML for the textarea
	 */
	
	public function formFields($title, $keys, $data, $removeButton = false, $sharedDataPlaceholder = true) {
		
		$html = '<h3>' . $title . '</h3>';
		
		foreach ($keys as $key) {
			
			$value = '';
		
			if (!empty($data[$key])) {
				$value = $data[$key];
			}
		
			$html .=  '<div class="form-group"><label for="input-data-' . $key . '">' . $key . '</label>';
		
			if ($removeButton) {
				$html .= '<button type="button" class="close automad-remove-parent">&times;</button>';
			}
		
			if ($sharedDataPlaceholder) {
				$placeholder = ' placeholder="' . htmlentities($this->Automad->Shared->get($key)) . '"';
			} else {
				$placeholder = '';
			}
		
			$html .= '<textarea' . $placeholder . ' id="input-data-' . $key . '" class="form-control" name="data[' . $key . ']" rows="10">' . $value . '</textarea></div>';
			
		}
		
		return $html;
		
	}
	
	
}


?>