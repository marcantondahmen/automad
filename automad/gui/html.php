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
 *	Copyright (c) 2016-2018 by Marc Anton Dahmen
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
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2018 Marc Anton Dahmen - <http://marcdahmen.de>
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
	 *	@return string The breadcrumb naviagtion markup
	 */
	
	public function breadcrumbs() {
		
		$Selection = new Core\Selection($this->Automad->getCollection());
		$Selection->filterBreadcrumbs(Core\Parse::query('url'));
		$pages = $Selection->getSelection(false);
		
		$html = '<ul class="am-breadcrumbs uk-subnav uk-subnav-pill uk-margin-top">';
		$html .= '<li class="uk-hidden-small"><i class="uk-icon-folder-open"></i></li>';
		
		$i = count($pages);
		
		$small = 2;
		$large = 4;
		
		if ($i > $small) {
			$html .= '<li class="uk-visible-small"><i class="uk-icon-angle-double-right"></i></li>';
		}
		
		if ($i > $large) {
			$html .= '<li class="uk-hidden-small"><i class="uk-icon-angle-double-right"></i></li>';
		}
		
		foreach ($pages as $url => $Page) {
			
			if ($i <= $large) {
				
				$class= '';
			
				if ($i > $small) {
					$class .= ' class="uk-hidden-small"';
				}
				
				$html .= '<li' . $class . '><a href="?context=edit_page&url=' . urlencode($url) . '">' . $Page->get(AM_KEY_TITLE) . '</a></li>';
				
				if ($i > 1) {
					$html .= '<li' . $class . '><i class="uk-icon-angle-right"></i></li>';
				}
				
			}
			
			$i--;
			
		}
		
		$html .= '</ul>';
		
		return $html;
	
	}
	
	
	/**
	 *	Create a form field depending on the name.
	 *      
	 *  @param string $key          
	 *  @param string $value        
	 *  @param boolean $removeButton 
	 *  @param object $Theme
	 *  @return string The generated HTML            
	 */
	
	public function formField($key = '', $value = '', $removeButton = false, $Theme = false) {
		
		// Convert special characters in $value to HTML entities.
		$value = htmlspecialchars($value);
		
		// The field ID.
		$id = 'am-input-data-' . $key;
	
		$html = '<div class="uk-form-row uk-position-relative">' .
				'<label class="uk-form-label" for="' . $id . '">' . 
				ucwords(trim(preg_replace('/([A-Z])/', ' $1', str_replace('_', ' ', $key)))) . 
				'</label>';

		if ($removeButton) {
			$html .= '<button type="button" class="am-remove-parent uk-position-top-right uk-margin-top uk-close"></button>';
		}

		$tooltip = '';
		
		if ($Theme) {
			$tooltip = ' title="' . $Theme->getTooltip($key) . '" data-uk-tooltip';
		}

		// Build attribute string.
		$attr = 'id="' . $id . '" name="data[' . $key . ']"';

		// Append placeholder to $attr when editing a page. Therefore check if any URL or context (inpage-edit) is set in $_POST.
		if (!empty($_POST['url']) || !empty($_POST['context'])) {
			$placeholder = ' placeholder="' . htmlspecialchars($this->Automad->Shared->get($key)) . '"';
		} else {
			$placeholder = '';
		}
		
		// Create field dependig on the start of $key.
		if (strpos($key, 'text') === 0) {
			
			$attr .= $placeholder;
			
			$html .= '<div' . $tooltip . '>' .
					 '<textarea ' . $attr . ' class="uk-form-controls uk-width-1-1" rows="10" data-uk-htmleditor>' . 
					 $value . 
					 '</textarea>' .
					 '</div>';
			
		} else if (strpos($key, 'date') === 0) {
			
			$attr .= ' value="' . $value . '"';
			
			$formatDate = 'Y-m-d';
			$formatTime = 'H:i';
			
			$attrDate = 'value="' . Core\Str::dateFormat($value, $formatDate) . '" readonly="true"';
			$attrTime = 'value="' . Core\Str::dateFormat($value, $formatTime) . '" readonly="true"';
			
			if (!empty($_POST['url']) || !empty($_POST['context'])) {
				$attrDate .= ' placeholder="' . Core\Str::dateFormat($this->Automad->Shared->get($key), $formatDate) . '"';
				$attrTime .= ' placeholder="' . Core\Str::dateFormat($this->Automad->Shared->get($key), $formatTime) . '"';
			}
			
			$html .= '<div data-am-datetime' . $tooltip . '>' .
					 // Actual combined date-time value (hidden).
					 '<input type="hidden" ' . $attr  . ' />' .
					 // Date picker.
					 '<div class="uk-form-icon">' . 
					 '<i class="uk-icon-calendar"></i>' .
					 '<input type="text" class="uk-width-1-1" ' . $attrDate . ' data-uk-datepicker="{format:\'YYYY-MM-DD\',pos:\'bottom\'}" />' .
					 '</div>' .
					 // Time picker.
					 '<div class="uk-form-icon">' . 
					 '<i class="uk-icon-clock-o"></i>' .
					 '<input type="text" class="uk-width-1-1" ' . $attrTime . ' data-uk-timepicker="{format:\'24h\'}" />' .
					 '</div>' .
					 // Reset button.
					 '<button type="button" class="uk-button" data-am-clear-date><i class="uk-icon-remove"></i></button>' .
					 '</div>';	
			
		} else if (strpos($key, 'checkbox') === 0) {
			
			if ($value) {
				$attr .= ' checked';
			}
			
			$html .= '<label class="am-toggle-switch uk-button" data-am-toggle' . $tooltip . '>&nbsp;' . 
					 ucwords(trim(preg_replace('/([A-Z])/', ' $1', str_replace('_', ' ', str_replace('checkbox', '', $key))))) . 
					 '<input ' . $attr . ' type="checkbox"  />' .
					 '</label>';
			
		} else {
			
			$attr .= $placeholder . $tooltip;
			
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
	 *  Passing a string for $addVariableIdPrefix will create the required markup for a modal dialog to add variables.   
	 *  Note used prefix must match the ID selectors defined in 'add_variable.js'.
	 *
	 *	@param array $keys
	 *	@param array $data
	 *	@param string $addVariableIdPrefix (automatically prefies all IDs for the HTML elements needed for the modal to add variables)
	 *	@param object $Theme
	 *	@return string The HTML for the textarea
	 */
	
	public function formGroup($keys, $data = array(), $addVariableIdPrefix = false, $Theme = false) {
			
		$html = '';
		
		// The HTML for the variable fields.
		foreach ($keys as $key) {
		
			if (isset($data[$key])) {
				$value = $data[$key];
			} else {
				$value = '';
			}
	
			// Note that passing $addVariableIdPrefix only to create remove buttons if string is not empty.
			$html .= $this->formField($key, $value, $addVariableIdPrefix, $Theme);
			
		}
		
		// Optionally create the HTML for a dialog to add more variables to the form.
		// Therefore $addVariableIdPrefix has to be defined.
		if ($addVariableIdPrefix) {
			
			$addVarModalId = $addVariableIdPrefix . '-modal';
			$addVarSubmitId = $addVariableIdPrefix . '-submit';
			$addVarInputlId = $addVariableIdPrefix . '-input';
			$addVarContainerId = $addVariableIdPrefix . '-container';
			
			$html =	'<div id="' . $addVarContainerId . '" class="uk-margin-bottom">' . $html . '</div>' .
					// The modal button.
					'<a href="#' . $addVarModalId . '" class="uk-button uk-button-success uk-margin-small-top" data-uk-modal>' .
					'<i class="uk-icon-plus"></i>&nbsp;&nbsp;' . Text::get('btn_add_var') .
					'</a>' . 
					// The actual modal.
					'<div id="' . $addVarModalId . '" class="uk-modal">' .
					'<div class="uk-modal-dialog">' .
					'<div class="uk-modal-header">' . Text::get('btn_add_var') . '<a href="" class="uk-modal-close uk-close"></a></div>' .	
					'<input id="' . $addVarInputlId . '" type="text" class="uk-form-controls uk-width-1-1" ' .
						'placeholder="' . Text::get('page_var_name') . '" required ' .
						'data-am-enter="#' . $addVarSubmitId . '" data-am-watch-exclude />' .
					'<div class="uk-modal-footer uk-text-right">' .
						'<button type="button" class="uk-modal-close uk-button">' .
							'<i class="uk-icon-close"></i>&nbsp;&nbsp;' . Text::get('btn_close') .
						'</button>&nbsp;' .
						'<button id="' . $addVarSubmitId . '" type="button" class="uk-button uk-button-success" data-am-error-exists="' . Text::get('error_var_exists') . '" data-am-error-name="' . Text::get('error_var_name') . '">
							<i class="uk-icon-plus"></i>&nbsp;&nbsp;' . Text::get('btn_add_var') .
						'</button>' .
					'</div>' .
					'</div>' .
					'</div>';
								
		} 
		
		return $html;
			
	}
	
	
	/**
	 *	Generate thumbnail for page grid.
	 *      
	 *  @param string $file  
	 *  @param float $w     
	 *  @param float $h     
	 *  @param string $gridW (uk-width-* suffix) 
	 *  @return string The generated markup
	 */
	
	private function gridThumbnail($file, $w, $h, $gridW) {
		
		$img = new Core\Image($file, $w, $h, true);
		return 	'<li class="uk-width-' . $gridW . '">' .
				'<img src="' . AM_BASE_URL . $img->file . '" alt="' . basename($img->file) . '" width="' . $img->width . '" height="' . $img->height . '">' .
				'</li>';
	
	}
	
	
	/**
	 *	Create loading icon.
	 *      
	 * 	@return string The HTML of the loading icon
	 */
	
	public function loading() {
		
		return '<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-small"></i>';
		
	}
	
	
	/**
	 *	Create a grid based page list for the given array of pages.
	 *
	 *	@param array $pages
	 *	@return string The HTML for the grid
	 */
	
	public function pageGrid($pages) {
	
		$html = '<ul class="uk-grid uk-grid-match uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-margin-top" data-uk-grid-match="{target:\'.uk-panel\'}" data-uk-grid-margin>';
		
		foreach ($pages as $key => $Page) {
			
			$link = '?context=edit_page&url=' . urlencode($key);
			
			$html .= '<li>' . 
					 '<div class="uk-panel uk-panel-box">' . 
					 '<a href="' . $link . '" class="uk-panel-teaser uk-display-block">'; 
			
			// Build file grid with up to 6 images.
			$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
			$files = glob($path . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
			
			if (!empty($files)) {
				
				$count = count($files);
				$wFull = 320;
				$hFull = 240;

				// File grid.
				$html .= '<ul class="uk-grid uk-grid-collapse">';
				
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
					$html .= $this->gridThumbnail($files[0], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[1], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[2], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[3], $wFull/2, $hFull/2, '1-2');
				}
				
				if ($count == 5) {
					$html .= $this->gridThumbnail($files[0], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[1], $wFull/2, $hFull/2, '1-2');
					$html .= $this->gridThumbnail($files[2], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[3], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[4], $wFull/3, $hFull/2, '1-3');
				}
				
				if ($count >= 6) {
					$html .= $this->gridThumbnail($files[0], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[1], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[2], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[3], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[4], $wFull/3, $hFull/2, '1-3');
					$html .= $this->gridThumbnail($files[5], $wFull/3, $hFull/2, '1-3');
				}
				
				$html .= '</ul>';
				
			} else {
				
				$html .= '<div class="am-panel-icon"><i class="uk-icon-folder-open"></i></div>';
				
			}
			
			$html .= 	'</a>' .
						// Title & date. 
						'<div class="uk-panel-title">' .
						$Page->get(AM_KEY_TITLE) . 
						'</div>' .
						'<div class="uk-text-small">' . Core\Str::dateFormat($Page->getMtime(), 'j. M Y') . '</div>' .
						'<div class="am-panel-bottom">' .
						'<span>' . 
						'<a href="' . $link . '" title="' . Text::get('btn_edit_page') . '" class="uk-icon-button uk-icon-pencil" data-uk-tooltip></a>&nbsp;' .
						'<a href="' . AM_BASE_INDEX . $Page->url . '" title="' . Text::get('btn_inpage_edit') . '" class="uk-icon-button uk-icon-share" data-uk-tooltip></a>' .
						'</span>' .
						'</div>' .
						'</div>' .
						'</li>';
				
		}
		
		$html .= '</ul>';
		
		return $html;
		
	}
	
	
	/**
	 *	Create a search field.
	 *      
	 *  @param string $tooltip
	 *  @return string The HTML for the search field
	 */
	
	public function searchField($placeholder = '', $tooltip = '') {
		
		if ($tooltip) {
			$tooltip = 'title="' . $tooltip . '" data-uk-tooltip="{pos:\'bottom\'}" ';
		}
		
		return  '<form class="uk-form uk-width-1-1" action="' . AM_BASE_INDEX . AM_PAGE_DASHBOARD . '" method="get" data-am-autocomplete-submit>' .
					'<input type="hidden" name="context" value="search" />' .
					'<div class="uk-autocomplete uk-width-1-1" data-uk-autocomplete="{source: Automad.autocomplete.data, minLength: 2}">' .
						'<input ' .
						'class="uk-form-controls uk-width-1-1" ' .
						'name="query" ' .
						'type="search" ' .
						'placeholder="' . $placeholder . '" ' .
						$tooltip .
						'required ' .
						'/>' .
					'</div>' . 
				'</form>';
		
	}
	
	
	/**
	 *	Create a select button.
	 *
	 *  @param string $name
	 *  @param array $values
	 *  @param string $selected
	 *  @param string $prefix
	 *  @return string The HTML for the buttons
	 */
	
	public function select($name, $values, $selected, $prefix = '') {
		
		// Set checked value, if $checked is not in $values, to prevent submitting an empty value.
		if (!in_array($selected, $values)) {
			$selected = reset($values);
		}
		
		$html = '<div class="uk-button uk-form-select" data-uk-form-select="{activeClass:\'\'}">' . 
				ltrim($prefix . ' ') . 
				'<span></span>&nbsp;&nbsp;' .
				'<i class="uk-icon-caret-down"></i>' . 
				'<select name="' . $name . '">';
		
		foreach ($values as $text => $value) {
			
			if ($value === $selected) {
				$attr = ' selected';
			} else {
				$attr = '';
			}
			
			$html .= '<option value="' . $value . '"' . $attr . '>' . $text . '</option>'; 	
			
		}
		
		$html .= '</select>' . 
			 	 '</div>';
		
		return $html;
		
	}
	
	
	/**
	 *	Create a select box containing all installed themes/templates to be included in a HTML form.
	 *
	 * 	@param object $Themelist
	 *	@param string $name
	 *	@param string $selectedTheme
	 *	@param string $selectedTemplate
	 *	@return string The HTML for the select box including a label and a wrapping div.
	 */

	public function selectTemplate($Themelist, $name = '', $selectedTheme = false, $selectedTemplate = false) {
		
		$themes = $Themelist->getThemes();
		$mainTheme = $Themelist->getThemeByKey($this->Automad->Shared->get(AM_KEY_THEME));
		
		// Create HTML.
		$html = '<div class="uk-form-select uk-button uk-button-large uk-button-success uk-width-1-1 uk-text-left" data-uk-form-select="{activeClass:\'\'}">' . 
				'<span></span>&nbsp;&nbsp;' .
				'<span class="uk-float-right"><i class="uk-icon-caret-down"></i></span>' .
				'<select class="uk-width-1-1" name="' . $name . '">'; 
		
		// List templates of current main theme.
		if ($mainTheme) {
			
			$html .= '<optgroup label="' . ucwords(Text::get('shared_theme')) . '">';
			
			foreach ($mainTheme->templates as $template) {

				$html .= '<option';

				if (!$selectedTheme && basename($template) === $selectedTemplate . '.php') {
					 $html .= ' selected';
				}

				$html .= ' value="' . basename($template) . '">' . 
					 '* / ' . ucwords(str_replace(array('_', '.php'), array(' ', ''), basename($template))) . 
					 '</option>';

			}
			
			$html .= '</optgroup>';
			
		}
		
		// List all other templated grouped by theme.
		foreach ($themes as $theme) {
			
			$html .= '<optgroup label="' . $theme->name . '">';
			
			foreach ($theme->templates as $template) {
				
				$html .= '<option';
				
				if ($selectedTheme === $theme->path && basename($template) === $selectedTemplate . '.php') {
					 $html .= ' selected';
				}
				
				$html .= ' value="' . $theme->path . '/' . basename($template) . '">' . 
						 $theme->name . 
						 ' / ' . 
						 ucwords(str_replace(array('_', '.php'), array(' ', ''), basename($template))) . 
						 '</option>';
				
			}
			
			$html .= '</optgroup>';
			
		}
		
		$html .= '</select>' .
			     '</div>';
		
		return $html;
		
	}
	
	
	/**
	 *  Create a status button for an AJAX status request with loading animation.
	 *      
	 *  @param string $status
	 *  @param string $tab
	 *  @return string The HTML for the status button
	 */
	
	public function status($status, $tab) {
		
		return	'<a '.
		 		'href="?context=system_settings#' . $tab . '" ' .
				'class="uk-button uk-button-large uk-width-1-1 uk-text-left" ' .
				'data-am-status="' . $status . '"' .
		 		'>' .
					'<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-justify"></i>&nbsp;&nbsp;' . 
					Text::get('btn_getting_data') .
				'</a>';
				
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
	 *	@return string The branch's HTML
	 */
	
	public function siteTree($parent, $collection, $parameters, $hideCurrent = false, $header = false) {
		
		$current = \Automad\Core\Parse::query('url');
		
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
				$icon = '<i class="uk-icon-folder uk-icon-justify"></i>&nbsp;&nbsp;';
				
				if ($key != $current || !$hideCurrent) {
				
					// Check if page is currently selected page
					if ($key == $current) {
						$html .= '<li class="uk-active">';
					} else {
						$html .= '<li>';
					}
					
					// Set title in tree.
					$title = htmlspecialchars($Page->get(AM_KEY_TITLE));
					$prefix = $Content->extractPrefixFromPath($Page->path);
					
					if (strlen($prefix) > 0) {
						$title = $prefix . ' - ' . $title;
					}
					
					$html .= '<a title="' . $Page->path . '" href="?' . http_build_query(array_merge($parameters, array('url' => $key)), '', '&amp;') . '">' . 
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
	 *  Create a sticky switcher menu with an optional dropdown menu.
	 *
	 *  @param string $target
	 *  @param array $items Main menu items
	 *  @param array $dropdown Dropdown menu items
	 *  @return string The rendered menu HTML
	 */
	
	public function stickySwitcher($target, $items = array(), $dropdown = array()) {
		
		$html = '<div class="am-switcher" data-uk-sticky="{top:60}">' .
				'<div class="am-switcher-buttons">' .
		 		'<div data-uk-switcher="{connect:\'' . $target . '\',animation:\'uk-animation-fade\',swiping:false}">';
	
		foreach ($items as $item) {
			
			// Clean up text to be used as id (also remove possible count badges).
			$tab = Core\Str::sanitize(preg_replace('/&\w+;/', '', strip_tags($item['text'])), true);
			
			$html .= '<button class="uk-button uk-button-large" data-am-tab="' . $tab . '">' . 
				 	 '<span class="uk-visible-small">' . $item['icon'] . '</span>' .
				 	 '<span class="uk-hidden-small">' . $item['text'] . '</span>' .
				  	 '</button>';
				 
		}
	
		$html .= '</div>';
	
		// Dropdown.
		if ($dropdown) {
			$html .= '<div data-uk-dropdown="{mode:\'click\',pos:\'bottom-right\'}">' . 
	        		 '<a href="#" class="uk-button uk-button-large">' .
				 	 Text::get('btn_more') . '&nbsp;&nbsp;<i class="uk-icon-caret-down"></i>' .
				 	 '</a>' .
	        		 '<div class="uk-dropdown uk-dropdown-small">' .
	            	 '<ul class="uk-nav uk-nav-dropdown">';
			
		    	foreach ($dropdown as $item) {
		    		$html .= '<li>' . $item . '</li>';
		    	}
			
		    	$html .= '</ul>' . 
				 		 '</div>' . 
				 	 	 '</div>';
		}
	
		$html .= '</div>' .
			 	 '</div>';
	
		return $html;
			
	}
	
	
}
