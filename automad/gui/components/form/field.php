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
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI\Components\Form;
use Automad\GUI\Components\Fullscreen\Bar as Bar;
use Automad\Core as Core;
use Automad\GUI\Text as Text;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The form field component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Field {


	/**
	 *	Create markup for a markdown editor.
	 *
	 *	@param string $fullscreenBar
	 *	@param string $attr
	 *	@param string $value
	 *	@return string The rendered markup.
	 */

	private static function fieldText($fullscreenBar, $attr, $value) {

		$class = '';

		if (self::isInPage()) {
			$class = 'class="am-fullscreen"';
		}

		return <<< HTML
				<div $class>
					$fullscreenBar
					<textarea 
					$attr 
					class="uk-form-controls uk-width-1-1" 
					rows="10" 
					data-uk-markdowneditor
					>$value</textarea>
				</div>
HTML;

	}


	/**
	 *	Create markup for an image field.
	 *
	 *	@param string $attr
	 *	@return string The rendered markup
	 */

	private static function fieldImage($attr) {

		return <<< HTML
				<div class="am-form-icon-button-input uk-flex" data-am-select-image-field>
					<button type="button" class="uk-button">
						<i class="uk-icon-folder-open-o"></i>
					</button>
					<input type="text" class="uk-form-controls uk-width-1-1" $attr />
				</div>
HTML;

	}


	/**
	 *	Create markup for an URL field.
	 *
	 *	@param string $attr
	 *	@return string The rendered markup
	 */

	private static function fieldUrl($attr) {

		return <<< HTML
				<div class="am-form-icon-button-input uk-flex" data-am-link-field>
					<button type="button" class="uk-button">
						<i class="uk-icon-link"></i>
					</button>
					<input type="text" class="uk-form-controls uk-width-1-1" $attr />
				</div>
HTML;

	}


	/**
	 *	Create markup for a date field.
	 *
	 *	@param string $attr
	 *	@param string $attrDate
	 *	@param string $attrTime
	 *	@return string The rendered markup
	 */

	private static function fieldDate($attr, $attrDate, $attrTime) {

		return <<< HTML
				<div class="uk-flex" data-am-datetime>
					<input type="hidden" $attr />
					<div class="uk-form-icon"> 
						<i class="uk-icon-calendar"></i>
						<input 
						type="text" 
						class="uk-width-1-1" 
						$attrDate 
						data-uk-datepicker="{format:'YYYY-MM-DD',pos:'bottom'}" 
						/>
					</div>
					<div class="uk-form-icon">
						<i class="uk-icon-clock-o"></i>
						<input 
						type="text" 
						class="uk-width-1-1" 
						$attrTime 
						data-uk-timepicker="{format:'24h'}" 
						/>
					</div>
					<button type="button" class="uk-button" data-am-clear-date>
						<i class="uk-icon-remove"></i>
					</button>
				</div>
HTML;

	}	

	
	/**
	 *	Create markup for a checkbox with optional 'default' option for page data.
	 *
	 *	@param string $text
	 *	@param string $attr
	 *	@param string $value
	 *	@param string $shared
	 *	@param boolean $isPage
	 *	@return string The rendered markup
	 */

	private static function fieldCheckbox($text, $attr, $value, $shared, $isPage) {
		
		if ($isPage) {

			$options = array(
				(object) array(
					'value' => '',
					'text' => 'Undefined - Use Shared Default',
					'selected' => ($value === '')
				),
				(object) array(
					'value' => '1',
					'text' => 'On',
					'selected' => ($value == true)
				),
				(object) array(
					'value' => '0',
					'text' => 'Off',
					'selected' => ($value == false && strlen($value))
				)
			);

			$optionsHtml = '';

			foreach ($options as $option) {

				$selected = '';
				
				if ($option->selected) {
					$selected = 'selected';
				}

				$optionsHtml .= '<option value="' . $option->value . '" ' . $selected . '>' . $option->text . '</option>';

			}
			
			return <<< HTML
					<div 
					class="uk-button uk-text-left uk-form-select uk-width-1-1" 
					data-uk-form-select="{activeClass:''}" 
					data-am-toggle-default="$shared"
					> 
						&nbsp; $text
						<select $attr>
							$optionsHtml
						</select> 
					</div>
HTML;

		} else {

			$checked = '';

			if ($value) {
				$checked = 'checked';
			}

			return <<< HTML
					<label class="am-toggle-switch uk-button" data-am-toggle> 
						$text
						<input $attr type="checkbox" $checked>
					</label>
HTML;

		}

	}

	
	/**
	 *	Create markup for a color field.
	 *
	 *	@param string $color
	 *	@param string $attr
	 *	@return string The rendered markup
	 */

	 private static function fieldColor($color, $attr) {

		return <<< HTML
				<div class="uk-flex" data-am-colorpicker> 
				 	<input type="color" value="$color" />
				 	<input type="text" class="uk-form-controls uk-width-1-1" $attr />
				</div>
HTML;

	}


	/**
	 *	Create markup for a block editor.
	 *
	 *	@param string $editorId
	 *	@param string $fullscreenBar
	 *	@param string $value
	 *	@return string The rendered markup 
	 */

	private static function fieldBlockEditor($editorId, $fullscreenBar, $attr, $value) {

		return <<< HTML
				<div class="am-block-editor" data-am-block-editor="$editorId">		
					$fullscreenBar
					<input type="hidden" $attr value="$value">
					<div id="$editorId" class="am-text am-block-editor-container am-fullscreen-container am-fullscreen-container-large"></div>
				</div>
HTML;

	}

	
	/**
	 *	Create markup for a default text area.
	 *
	 *	@param string $attr
	 *	@param string $value
	 *	@return string The rendered markup
	 */
	
	 private static function fieldDefault($attr, $value) {

		return <<< HTML
				<textarea 
				$attr 
				class="uk-form-controls uk-width-1-1" rows="10"
				>$value</textarea>
HTML;

	}


	/**
	 *	Return the fullscreen bar markup when not in in-page edit mode.
	 *
	 *	@param object $Automad
	 *	@param string $label
	 *	@return string The rendered markup
	 */

	 private static function fullscreenBar($Automad, $label) {

		if (self::isInPage()) {
			return false;	
		} 

		if ($url = Core\Request::post('url')) {
			$Page = $Automad->getPage($url);
			$title = htmlspecialchars($Page->get(AM_KEY_TITLE));
		} else {
			$title = Text::get('shared_title');
		}

		return Bar::render($title, $label);

	}


	/**
	 *	Check if request is made in in-page edit mode.
	 *
	 *	@return string The converted label.
	 */

	private static function isInPage() {

		return (strpos(Core\Request::query('ajax'), 'inpage') !== false);

	}


	/**
	 *	Return the label markup when not in in-page edit mode.
	 *
	 *	@param string $label
	 *	@param string $id
	 *	@return string The rendered markup
	 */

	private static function labelHtml($label, $id) {

		if (self::isInPage()) {
			return false;	
		} 

		return '<label class="uk-form-label" for="' . $id . '">' . 
			   $label . 
			   '</label>';

	}


	/**
	 *	Return the remove button markup if needed.
	 *
	 *	@param boolean $hasRemoveButton
	 *	@return string The rendered markup
	 */

	private static function removeButton($hasRemoveButton) {

		if ($hasRemoveButton) {

			$Text = Text::getObject();

			return <<< HTML
				   <button 
				   type="button" 
				   class="am-remove-parent am-button-remove-parent uk-margin-top"
				   title="$Text->btn_remove"
				   data-uk-tooltip
				   ><i class="uk-icon-remove"></i></button>
HTML;

		}

	}


	/**
	 *	Convert camel case key into a human readable label.
	 *
	 *	@param string $key
	 *	@return string The converted label.
	 */
	
	public static function labelFromKey($key) {

		$label = str_replace('+', '', $key);
		$label = ucwords(trim(preg_replace('/([A-Z])/', ' $1', str_replace('_', ' ', $label))));

		return $label;

	}


	/**
	 *	Create tooltip dropdown.
	 *
	 *	@param object $Theme
	 *	@param string $key
	 *	@return string The markup for the dropdown.
	 */

	private static function tooltip($Theme, $key) {

		if (self::isInPage()) {
			return false;	
		} 

		if ($Theme) {

			if ($tooltip = $Theme->getTooltip($key)) {

				return <<< HTML
						<div 
						class="am-dropdown-tooltip" 
						data-uk-dropdown
						>
							<div class="am-dropdown-tooltip-icon">
								<i class="uk-icon-lightbulb-o"></i>
							</div>
							<div class="uk-dropdown">
								$tooltip
							</div>
						</div>

HTML;

			}
			
		} 

	}


	/**
	 *	Create a form field depending on the name.
	 *      
	 *	@param object $Automad
	 *	@param string $key          
	 *	@param string $value        
	 *	@param boolean $removeButton 
	 *	@param object $Theme
	 *	@param string $label
	 *	@return string The generated HTML            
	 */
	
	public static function render($Automad, $key = '', $value = '', $removeButton = false, $Theme = false, $label = false) {
		
		// Convert special characters in $value to HTML entities.
		$value = htmlspecialchars($value);
		
		$url = Core\Request::post('url');
		$context = Core\Request::post('context');

		// The field ID.
		$id = 'am-input-data-' . $key;

		// Label.
		if (!$label) {
			$label = self::labelFromKey($key);
		}

		// Build attribute string.
		$attr = 'id="' . $id . '" name="data[' . $key . ']"';

		// Global value.
		$shared = htmlspecialchars($Automad->Shared->get($key));

		// Append placeholder to $attr when editing a page. Therefore check if any URL or context (inpage-edit) is set in $_POST.
		if ($url || $context) {
			$placeholder = ' placeholder="' . $shared . '"';
		} else {
			$placeholder = '';
		}

		$html = '<div class="uk-form-row uk-position-relative">';
		$html .= self::labelHtml($label, $id);
		$html .= self::tooltip($Theme, $key);
		$html .= self::removeButton($removeButton);

		// Create field dependig on the start of $key.
		if (strpos($key, 'text') === 0) {
			
			$attr .= $placeholder;
			$html .= self::fieldText(self::fullscreenBar($Automad, $label), $attr, $value);

		} else if (strpos($key, 'image') === 0 && strpos($key, 'images') === false) {
		
			$attr .= ' value="' . $value . '"' . $placeholder;
			$html .= self::fieldImage($attr);

		} else if (strpos($key, 'url') === 0) {
		
			$attr .= ' value="' . $value . '"' . $placeholder;
			$html .= self::fieldUrl($attr);
		
		} else if (strpos($key, 'date') === 0) {
			
			$attr .= ' value="' . $value . '"';
			
			$formatDate = 'Y-m-d';
			$formatTime = 'H:i';
			
			$attrDate = 'value="' . Core\Str::dateFormat($value, $formatDate) . '" readonly="true"';
			$attrTime = 'value="' . Core\Str::dateFormat($value, $formatTime) . '" readonly="true"';
			
			if ($url || $context) {
				$attrDate .= ' placeholder="' . Core\Str::dateFormat($shared, $formatDate) . '"';
				$attrTime .= ' placeholder="' . Core\Str::dateFormat($shared, $formatTime) . '"';
			}
			
			$html .= self::fieldDate($attr, $attrDate, $attrTime);

		} else if (strpos($key, 'checkbox') === 0) {
			
			$text = ucwords(trim(preg_replace('/([A-Z])/', ' $1', str_replace('_', ' ', str_replace('checkbox', '', $key)))));
			$html .= self::fieldCheckbox($text, $attr, $value, $shared, ($url || $context));

		} else if (strpos($key, 'color') === 0) {

			if (strlen($value)) {
				$color = $value;
			} else {
				$color = $shared;
			} 
			
			$attr .= ' value="' . $value . '"' . $placeholder;			
			$html .= self::fieldColor($color, $attr);
			
		} else if (strpos($key, '+') === 0) {

			$editorId = 'am-block-editor-' . str_replace('+', '', $key);
			$html .= self::fieldBlockEditor($editorId, self::fullscreenBar($Automad, $label), $attr, $value);

		} else {
			
			$attr .= $placeholder;
			$html .= self::fieldDefault($attr, $value);
			
		}
		
		$html .= '</div>';
		
		return $html;
		
	}


}