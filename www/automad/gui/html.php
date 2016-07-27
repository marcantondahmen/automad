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
use Automad\Core as Core;


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
	 *	Create a breadcrumb navigation based on $_GET.
	 *
	 *	@return the breadcrumb naviagtion markup
	 */
	
	public function breadcrumbs() {
		
		$Selection = new Core\Selection($this->Automad->getCollection());
		$Selection->filterBreadcrumbs(Core\Parse::queryKey('url'));
		$pages = $Selection->getSelection(false);
		
		$html = '<ul class="automad-navbar-breadcrumbs uk-subnav">';
		$i = count($pages);
		
		$small = 2;
		$medium = 4;
		$large = 4;
		
		if ($i > $small) {
			$html .= '<li class="uk-visible-small uk-disabled"><span>...</span></li>';
		}
		
		if ($i > $medium) {
			$html .= '<li class="uk-visible-medium uk-disabled"><span>...</span></li>';
		}
		
		if ($i > $large) {
			$html .= '<li class="uk-visible-large uk-disabled"><span>...</span></li>';
		}
		
		foreach ($pages as $url => $Page) {
			
			if ($i <= $large) {
				
				$class= '';
			
				if ($i > $small) {
					$class = ' class="uk-hidden-small"';
				}
			
				if ($i > $medium) {
					$class = ' class="uk-hidden-small uk-hidden-medium"';
				}
				
				if ($i < count($pages)) {
					$html .= '<li' . $class . '><i class="uk-icon-angle-right"></i></li>';
				}
				
				if ($i == 1) {
					$class = ' class="uk-active"';
				}
			
				$html .= '<li' . $class . '><a class="uk-text-truncate" href="?context=edit_page&url=' . urlencode($url) . '">' . $Page->get(AM_KEY_TITLE) . '</a></li>';
				
			}
			
			$i--;
			
		}
		
		$html .= '</ul>';
		
		return $html;
	
	}
	
	
	/**
	 *	Create form fields for page/shared variables.     
	 *
	 *      By passing any text for the parameter $wrapper, all inputs get wrapped in a toggle box with a 
	 *      related button using the parameter's value as text.    
	 *      
	 *      Setting $removeButton = true will create a little 'x' button for each input for removal.     
	 *       
	 *      Setting $sharedDataPlaceholder = true will use values matching the given 
	 *      key in the Shared object as placeholders.   
	 *      
	 *      Passing a string for $addVariableIdPrefix will create the required markup for a modal dialog to add variables.   
	 *      Note used prefix must match the ID selectors defined in 'add_variable.js'.
	 *
	 *	@param array $keys
	 *	@param array $data
	 *	@param string $wrapper (wrapper button text)
	 *	@param boolean $removeButton
	 *	@param boolean $sharedDataPlaceholder
	 *	@param string $addVariableIdPrefix (automatically prefies all IDs for the HTML elements needed for the modal to add variables)
	 *	@return The HTML for the textarea
	 */
	
	public function formFields($keys, $data = array(), $wrapper = false, $removeButton = false, $sharedDataPlaceholder = true, $addVariableIdPrefix = false) {
			
		$html = '';
		
		// The HTML for the variable fields.
		foreach ($keys as $key) {
		
			$value = '';
	
			if (isset($data[$key])) {
				$value = $data[$key];
			}
	
			$html .=  '<div class="uk-form-row"><label class="uk-form-label" for="automad-input-data-' . $key . '">' . $key . '</label>';
	
			if ($removeButton) {
				$html .= '<button type="button" class="automad-remove-parent uk-position-top-right uk-close"></button>';
			}
	
			if ($sharedDataPlaceholder) {
				$placeholder = ' placeholder="' . htmlentities($this->Automad->Shared->get($key)) . '"';
			} else {
				$placeholder = '';
			}
	
			$html .= '<textarea' . $placeholder . ' id="automad-input-data-' . $key . '" class="uk-form-controls uk-width-1-1" name="data[' . $key . ']" rows="10">' . $value . '</textarea></div>';
		
		}
		
		// Optionally create the HTML for a dialog to add more variables to the form.
		// Therefore $addVariableIdPrefix has to be defined.
		if ($addVariableIdPrefix) {
			
			$addVarModalId = $addVariableIdPrefix . '-modal';
			$addVarSubmitId = $addVariableIdPrefix . '-submit';
			$addVarInputlId = $addVariableIdPrefix . '-input';
			$addVarContainerId = $addVariableIdPrefix . '-container';
			
			$html =		'<div id="' . $addVarContainerId . '" class="uk-margin-bottom">' . $html . '</div>' .
					// The modal button.
					'<a type="button" href="#' . $addVarModalId . '" class="uk-button uk-button-large uk-width-1-1" data-uk-modal>' .
					'<i class="uk-icon-plus"></i>&nbsp;&nbsp;' . Text::get('btn_add_var') .
					'</a>' . 
					// The actual modal.
					'<div id="' . $addVarModalId . '" class="uk-modal">' .
					'<div class="uk-modal-dialog">' .
					'<div class="uk-modal-header">' . Text::get('btn_add_var') . '</div>' .	
					'<input id="' . $addVarInputlId . '" type="text" class="uk-form-controls uk-width-1-1" placeholder="' . Text::get('page_var_name') . '" required data-automad-enter="#' . $addVarSubmitId . '" />' .
					'<div class="uk-modal-footer uk-text-right">' .
						'<button type="button" class="uk-modal-close uk-button">' .
							'<i class="uk-icon-close"></i>&nbsp;&nbsp;' . Text::get('btn_close') .
						'</button>' .
						'<button id="' . $addVarSubmitId . '" type="button" class="uk-button uk-button-primary" data-automad-error-exists="' . Text::get('error_var_exists') . '" data-automad-error-name="' . Text::get('error_var_name') . '">
							<i class="uk-icon-plus"></i>&nbsp;&nbsp;' . Text::get('btn_add_var') .
						'</button>' .
					'</div>' .
					'</div>' .
					'</div>';
								
		} 
		
		// Wrap the HTML into a div and create a toggle button in case $wrapper is defined. 
		if ($wrapper) {
			
			$wrapperClass = 'automad-' . Core\String::sanitize($wrapper, true);
			
			$html = 	// Toggle button.
					'<button type="button" class="uk-button uk-button-primary uk-margin-bottom uk-text-left uk-width-1-1" data-uk-toggle="{target:\'.' . $wrapperClass . '\', animation:\'uk-animation-fade\'}">' . 
					'<i class="' . $wrapperClass . ' uk-icon-chevron-down"></i>' .
					'<i class="' . $wrapperClass . ' uk-icon-chevron-up uk-hidden"></i>' .
					'&nbsp;&nbsp;' . $wrapper . ' (<strong>' . count($keys) . '</strong>)' .
					'</button>' .
					// The toggle container.
					'<div class="' . $wrapperClass . ' uk-hidden uk-margin-bottom">' . $html . '</div>';
			
		} 
		
		return $html;
			
	}
	
	
	/**
	 *	Create a grid based page list for the given array of pages.
	 *
	 *	@param array $pages
	 *	@return the HTML for the grid
	 */
	
	public function pageGrid($pages) {
	
		$html = '<ul class="uk-grid uk-grid-small uk-grid-width-1-2 uk-grid-width-medium-1-3" data-uk-grid="{animation: false}">';
		
		foreach ($pages as $key => $Page) {
			
			$html .= 	'<li>' . 
					'<div class="uk-position-relative uk-margin-small-bottom">' . 
					
					// Panel.
					'<div class="uk-panel uk-panel-box ">';
			
			// Get thumbnail of page.
			$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
			$files = glob($path . '{*.jpg, *.png, *.gif}', GLOB_BRACE);
			
			if ($files) {
				$file = reset($files);
				$img = new Core\Image($file, 450);
				$html .= '<img class="uk-border-rounded" src="' . AM_BASE_URL . $img->file . '" alt="' . basename($img->file) . '">';
			} else {
				
				$html .= 	'<div class="uk-panel uk-panel-box uk-panel-box-secondary uk-border-rounded">' .
						'<i class="uk-text-muted uk-icon-file-text-o uk-icon-medium"></i>' .
						'</div>';
				
			}
			
			$html .= 	'<div class="uk-panel-title uk-margin-small-bottom uk-margin-top">' . $Page->get(AM_KEY_TITLE) . '</div>' .
					'<div class="uk-text-small uk-text-muted">' . Core\String::dateFormat($Page->getMtime(), 'j. M Y') . '</div>' .
					'</div>' . 
					
					// Overlay icon link.
					'<a href="?context=edit_page&url=' . urlencode($key) . '" class="uk-overlay uk-overlay-hover uk-position-absolute uk-position-top-left uk-height-1-1 uk-width-1-1">' .
					'<div class="uk-height-1-1 uk-overlay-panel uk-overlay-background uk-overlay-fade uk-overlay-icon"></div>' .
					'</a>' .
					
					'</div>' .
					'</li>';
				
		}
		
		$html .= '</ul>';
		
		return $html;
		
	}
	
	
	/**
	 *	Create recursive site tree for editing a page. 
	 *	Every page link sends a post request to gui/pages.php containing the page's url.
	 *
	 *	@param string $parent
	 *	@param array $collection
	 *	@param array $parameters (additional query string parameters to be passed along with the url)
	 *	@param boolean $hideCurrent
	 *	@param string $header
	 *	@return the branch's HTML
	 */
	
	public function siteTree($parent, $collection, $parameters, $hideCurrent = false, $header = false) {
		
		$current = \Automad\Core\Parse::queryKey('url');
		
		$selection = new \Automad\Core\Selection($collection);
		$selection->filterByParentUrl($parent);
		$selection->sortPages();
		
		$Content = new Content($this->Automad);
		
		if ($pages = $selection->getSelection(false)) {
			
			$html = '<ul class="uk-nav uk-nav-side">';
			
			if ($header) {
				$html .= '<li class="uk-nav-header">' . $header . '</li>';
			}
			
			foreach ($pages as $key => $Page) {
				
				// Set page icon.
				if ($Page->url == '/') {
					$icon = '<i class="uk-icon-home"></i>&nbsp;&nbsp;';
				} else {   
					$icon = '<i class="uk-icon-folder-o"></i>&nbsp;&nbsp;';
				}
				
				if ($key != $current || !$hideCurrent) {
				
					// Check if page is currently selected page
					if ($key == $current) {
						$html .= '<li class="uk-active">';
					} else {
						$html .= '<li>';
					}
					
					// Set title in tree.
					$title = $Page->get(AM_KEY_TITLE);
					
					if ($prefix = $Content->extractPrefixFromPath($Page->path)) {
						$title = $prefix . ' - ' . $title;
					}
					
					$html .= '<a class="uk-text-truncate" title="' . $Page->url . '" href="?' . http_build_query(array_merge($parameters, array('url' => $key)), '', '&amp;') . '">' . 
						 $icon . $title . 
						 '</a>' .
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
	 *	@param string $name
	 *	@param string $selectedTheme
	 *	@param string $selectedTemplate
	 *	@return The HTML for the select box including a label and a wrapping div.
	 */

	public function templateSelectBox($name = '', $selectedTheme = false, $selectedTemplate = false) {
		
		// Find all templates of currently used site theme (set in /shared/data.txt).
		$siteThemeTemplates = 	array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/' . $this->Automad->Shared->get(AM_KEY_THEME) . '/*.php'), function($file) {
						return false === in_array(basename($file), array(AM_PAGE_NOT_FOUND_TEMPLATE . '.php', AM_PAGE_RESULTS_TEMPLATE . '.php'));
					});

		// Find all templates of all installed themes.
		$templates = 		array_filter(glob(AM_BASE_DIR . AM_DIR_THEMES . '/*/*.php'), function($file) {
						return false === in_array(basename($file), array(AM_PAGE_NOT_FOUND_TEMPLATE . '.php', AM_PAGE_RESULTS_TEMPLATE . '.php'));
					});
		
		// Create HTML
		$html = '<select class="uk-width-1-1" name="' . $name . '">'; 
		
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
		
		$html .= '</select>';
		
		return $html;
		
	}
	
	
}


?>