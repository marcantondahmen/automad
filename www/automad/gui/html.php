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
	 *      Create a form field depending on the name.
	 *      
	 *      @param string $key          
	 *      @param string $value        
	 *      @param boolean $removeButton 
	 *      @return The generated HTML            
	 */
	
	public function formField($key = '', $value = '', $removeButton = false) {
		
		// Escape $value.
		$value = htmlspecialchars($value);
		
		// The field ID.
		$id = 'automad-input-data-' . $key;
	
		$html = '<div class="uk-form-row">' .
			'<label class="uk-form-label" for="' . $id . '">' . 
			$key . 
			 '</label>';

		if ($removeButton) {
			$html .= '<button type="button" class="automad-remove-parent uk-position-top-right uk-close"></button>';
		}

		// Build attribute string.
		$attr = 'id="' . $id . '" name="data[' . $key . ']"';

		// Append placeholder to $attr when editing a page. Therefore check if any URL is set in $_POST.
		if (!empty($_POST['url'])) {
			$attr .= ' placeholder="' . htmlspecialchars($this->Automad->Shared->get($key)) . '"';
		} 
		
		// Create field dependig on the start of $key.
		if (strpos($key, 'text') === 0) {
			
			$html .= 	'<textarea ' . $attr . ' class="uk-form-controls uk-width-1-1" rows="10" data-uk-htmleditor="{markdown:true}">' . 
					$value . 
					'</textarea>';
			
		} else if (strpos($key, 'date') === 0) {
			
			$attr .= 	' value="' . $value . '"';
			
			$html .=	'<div class="uk-form-icon uk-width-1-1">' . 
					'<i class="uk-icon-calendar"></i>' .
					'<input ' . $attr . ' type="text" class="uk-width-1-1" data-uk-datepicker="{format:\'YYYY-MM-DD\'}" />' .
					'</div>';
			
		} else if (strpos($key, 'checkbox') === 0) {
			
			if ($value) {
				$attr .= ' checked';
			}
			
			$html .=	'<label class="uk-button" data-automad-toggle>' . 
					ucwords(preg_replace('/([A-Z])/', ' $1', str_replace('_', ' ', $key))) . 
					'<input ' . $attr . ' type="checkbox"  />' .
					'</label>';
			
		} else {
			
			// The default is a simple textarea.
			$html .= 	'<textarea ' . $attr . ' class="uk-form-controls uk-width-1-1" rows="10">' . 
					$value . 
					'</textarea>';
			
		}
		
		$html .= '</div>';
		
		return $html;
		
	}
	

	/**
	 *	Create form fields for page/shared variables.     
	 *
	 *      By passing any text for the parameter $wrapper, all inputs get wrapped in a toggle box with a 
	 *      related button using the parameter's value as text.    
	 *      Setting $open = true keeps the container open on load.      
	 *      
	 *      Passing a string for $addVariableIdPrefix will create the required markup for a modal dialog to add variables.   
	 *      Note used prefix must match the ID selectors defined in 'add_variable.js'.
	 *
	 *	@param array $keys
	 *	@param array $data
	 *	@param string $wrapper (wrapper button text)
	 *	@param boolean $open (initially open wrapper)
	 *	@param string $addVariableIdPrefix (automatically prefies all IDs for the HTML elements needed for the modal to add variables)
	 *	@return The HTML for the textarea
	 */
	
	public function formGroup($keys, $data = array(), $wrapper = false, $open = false, $addVariableIdPrefix = false) {
			
		$html = '';
		
		// The HTML for the variable fields.
		foreach ($keys as $key) {
		
			if (isset($data[$key])) {
				$value = $data[$key];
			} else {
				$value = '';
			}
	
			// Note that passing $addVariableIdPrefix only to create remove buttons if string is not empty.
			$html .= $this->formField($key, $value, $addVariableIdPrefix);
			
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
			
			if ($open) {
				$hidden = '';
				// If the container should be open, set the open icon to be hidden and only show the hide icon.
				$open =  ' uk-hidden';
			} else {
				$hidden = ' uk-hidden';
			}
			
			$html = 	// Toggle button.
					'<button type="button" class="uk-button uk-margin-bottom uk-text-left uk-width-1-1" data-uk-toggle="{target:\'.' . $wrapperClass . '\', animation:\'uk-animation-fade\'}">' . 
					'<i class="' . $wrapperClass . $open . ' uk-icon-chevron-down"></i>' .
					'<i class="' . $wrapperClass . $hidden . ' uk-icon-chevron-up"></i>' .
					'&nbsp;&nbsp;' . $wrapper . 
					'</button>' .
					// The toggle container.
					'<div class="' . $wrapperClass . $hidden . ' uk-margin-bottom">' . $html . '</div>';
			
		} 
		
		return $html;
			
	}
	
	
	/**
	 *      Generate thumbnail for page grid.
	 *      
	 *      @param string $file  
	 *      @param float $w     
	 *      @param float $h     
	 *      @param string $gridW (uk-width-* suffix) 
	 *      @return The generated markup
	 */
	
	private function gridThumbnail($file, $w, $h, $gridW) {
		
		$img = new Core\Image($file, $w, $h, true);
		return 	'<li class="uk-width-' . $gridW . '">' .
			'<img src="' . AM_BASE_URL . $img->file . '" alt="' . basename($img->file) . '" width="' . $img->width . '" height="' . $img->height . '">' .
			'</li>';
	
	}
	
	
	/**
	 *	Create a grid based page list for the given array of pages.
	 *
	 *	@param array $pages
	 *	@return the HTML for the grid
	 */
	
	public function pageGrid($pages) {
	
		$html = '<ul class="uk-grid uk-grid-width-1-2 uk-grid-width-medium-1-3" data-uk-grid="{animation:false}">';
		
		foreach ($pages as $key => $Page) {
			
			$html .= 	'<li>' . 
					'<div class="uk-position-relative uk-margin-bottom">' . 
					
					// Panel.
					'<div class="uk-panel uk-panel-box">';
			
			// Build file grid with up to 4 images.
			$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
			$files = glob($path . '{*.jpg, *.png, *.gif}', GLOB_BRACE);
			
			if (!empty($files)) {
				
				$count = count($files);
				$wFull = 400;
				$hFull = 300;

				// File grid.
				$html .= '<ul class="uk-grid uk-grid-collapse uk-border-rounded uk-overflow-hidden">';
				
				if ($count == 1) {
					$html .= $this->gridThumbnail($files[0], $wFull, $hFull, '1-1');
				}
				
				if ($count == 2) {
					$html .= $this->gridThumbnail($files[0], $wFull/2, $hFull, '1-2');
					$html .= $this->gridThumbnail($files[1], $wFull/2, $hFull, '1-2');
				}
				
				if ($count == 3) {
					$html .= $this->gridThumbnail($files[0], $wFull, $hFull/2, '1-1');
					$html .= $this->gridThumbnail($files[1], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[2], $wFull/2, $hFull/2, '1-2');
				}
				
				if ($count == 4) {
					$html .= $this->gridThumbnail($files[0], $wFull, $hFull/2, '1-1');
					$html .= $this->gridThumbnail($files[1], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[2], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[3], $wFull/3, $hFull/2, '1-3');
				}
				
				if ($count == 5) {
					$html .= $this->gridThumbnail($files[0], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[1], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[2], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[3], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[4], $wFull/3, $hFull/2, '1-3');
				}
				
				if ($count == 6) {
					$html .= $this->gridThumbnail($files[0], $wFull, $hFull/2, '1-1');
					$html .= $this->gridThumbnail($files[1], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[2], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[3], $wFull/3, $hFull/3, '1-3');
					$html .= $this->gridThumbnail($files[4], $wFull/3, $hFull/3, '1-3');
					$html .= $this->gridThumbnail($files[5], $wFull/3, $hFull/3, '1-3');
				}
				
				if ($count == 7) {
					$html .= $this->gridThumbnail($files[0], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[1], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[2], $wFull/3, $hFull/3, '1-3');
					$html .= $this->gridThumbnail($files[3], $wFull/3, $hFull/3, '1-3');
					$html .= $this->gridThumbnail($files[4], $wFull/3, $hFull/3, '1-3');
					$html .= $this->gridThumbnail($files[5], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[6], $wFull/2, $hFull/2, '1-2');
				}
				
				if ($count >= 8) {
					$html .= $this->gridThumbnail($files[0], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[1], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[2], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[3], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[4], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[5], $wFull/3, $hFull/3, '1-3');
					$html .= $this->gridThumbnail($files[6], $wFull/3, $hFull/3, '1-3');
					$html .= $this->gridThumbnail($files[7], $wFull/3, $hFull/3, '1-3');
				}
				
				$html .= '</ul>';
				
			} else {
				
				$html .= 	'<div class="uk-panel uk-panel-box uk-panel-box-secondary uk-border-rounded uk-text-center">' .
						'<i class="uk-block uk-text-muted uk-icon-eye-slash uk-icon-large"></i>' .
						'</div>';
			}
			
			$html .= 	// Title & date.
			 		'<div class="uk-panel-title uk-margin-small-bottom uk-margin-top">' . $Page->get(AM_KEY_TITLE) . '</div>' .
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