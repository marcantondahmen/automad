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
	 * 	@param string $tooltip
	 * 	@param string $fullscreenBar
	 * 	@param string $attr
	 * 	@param string $value
	 * 	@return string The rendered markup.
	 */

	private static function fieldText($tooltip, $fullscreenBar, $attr, $value) {

		$class = '';

		if (self::isInPage()) {
			$class = 'class="am-fullscreen"';
		}

		return <<< HTML
				<div $class $tooltip>
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
	 * 	@param string $attr
	 * 	@return string The rendered markup
	 */

	private static function fieldImage($attr) {

		return <<< HTML
				<div class="am-form-icon-button-input uk-flex" data-am-select-image-field>
					<button type="button" class="uk-button uk-button-large">
						<i class="uk-icon-folder-open"></i>
					</button>
					<input type="text" class="uk-form-controls uk-width-1-1" $attr />
				</div>
HTML;

	}


	/**
	 *	Create markup for an URL field.
	 *
	 * 	@param string $attr
	 * 	@return string The rendered markup
	 */

	private static function fieldUrl($attr) {

		return <<< HTML
				<div class="am-form-icon-button-input uk-flex" data-am-link-field>
					<button type="button" class="uk-button uk-button-large">
						<i class="uk-icon-link"></i>
					</button>
					<input type="text" class="uk-form-controls uk-width-1-1" $attr />
				</div>
HTML;

	}


	/**
	 *	Create markup for a date field.
	 *
	 * 	@param string $tooltip
	 * 	@param string $attr
	 * 	@param string $attrDate
	 * 	@param string $attrTime
	 * 	@return string The rendered markup
	 */

	private static function fieldDate($tooltip, $attr, $attrDate, $attrTime) {

		return <<< HTML
				<div class="uk-flex" data-am-datetime $tooltip>
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
	 *	Create markup for a chackbox.
	 *
	 * 	@param string $tooltip
	 * 	@param string $text
	 * 	@param string $attr
	 * 	@return string The rendered markup
	 */

	private static function fieldCheckbox($tooltip, $text, $attr) {

		return <<< HTML
				<label class="am-toggle-switch uk-button" data-am-toggle $tooltip> 
					$text
					<input $attr type="checkbox" />
				</label>
HTML;

	}

	
	/**
	 *	Create markup for a color field.
	 *
	 * 	@param string $color
	 * 	@param string $attr
	 * 	@return string The rendered markup
	 */

	 private static function fieldColor($color, $attr) {

		return <<< HTML
				<div class="uk-flex" data-am-colorpicker> 
				 	<input type="color" class="uk-button" value="$color" />
				 	<input type="text" class="uk-form-controls uk-width-1-1" $attr />
				</div>
HTML;

	}


	/**
	 *	Create markup for a block edito.
	 *
	 * 	@param string $tooltip
	 * 	@param string $editorId
	 * 	@param string $fullscreenBar
	 * 	@param string $value
	 * 	@return string The rendered markup 
	 */

	private static function fieldBlockEditor($tooltip, $editorId, $fullscreenBar, $attr, $value) {

		return <<< HTML
				<div class="am-block-editor" $tooltip data-am-block-editor="$editorId">		
					$fullscreenBar
					<input type="hidden" $attr value="$value">
					<div id="$editorId" class="am-text am-block-editor-container am-fullscreen-container"></div>
				</div>
HTML;

	}

	
	/**
	 *	Create markup for a default text area.
	 *
	 * 	@param string $attr
	 * 	@param string $value
	 * 	@return string The rendered markup
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
	 * 	@param object $Automad
	 * 	@param string $label
	 * 	@return string The rendered markup
	 */

	 private static function fullscreenBar($Automad, $label) {

		if (self::isInPage()) {
			return false;	
		} 

		if ($url = Core\Request::post('url')) {
			$Page = $Automad->getPage($url);
			$title = $Page->get(AM_KEY_TITLE);
		} else {
			$title = Text::get('shared_title');
		}

		return Bar::render($title, $label);

	}


	/**
	 *	Check if request is made in in-page edit mode.
	 *
	 * 	@return string The converted label.
	 */

	private static function isInPage() {

		return (strpos(Core\Request::query('ajax'), 'inpage') !== false);

	}


	/**
	 *	Return the label markup when not in in-page edit mode.
	 *
	 * 	@param string $label
	 * 	@param string $id
	 * 	@return string The rendered markup
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
	 * 	@param boolean $hasRemoveButton
	 * 	@return string The rendered markup
	 */

	private static function removeButton($hasRemoveButton) {

		if ($hasRemoveButton) {

			return <<< HTML
				   <button 
				   type="button" 
				   class="am-remove-parent uk-margin-top uk-button uk-button-mini uk-button-danger"
				   ><i class="uk-icon-trash-o"></i></button>
HTML;

		}

	}


	/**
	 *	Convert camel case key into a human readable label.
	 *
	 * 	@param string $key
	 * 	@return string The converted label.
	 */
	
	public static function labelFromKey($key) {

		$label = str_replace('+', '<i class="uk-icon-plus-circle"></i> ', $key);
		$label = ucwords(trim(preg_replace('/([A-Z])/', ' $1', str_replace('_', ' ', $label))));

		return $label;

	}


	/**
	 *	Create a form field depending on the name.
	 *      
	 * 	@param object $Automad
	 *  @param string $key          
	 *  @param string $value        
	 *  @param boolean $removeButton 
	 *  @param object $Theme
	 * 	@param string $label
	 *  @return string The generated HTML            
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

		// Tooltip.
		if ($Theme) {
			$tooltip = ' title="' . $Theme->getTooltip($key) . '" data-uk-tooltip';
		} else {
			$tooltip = '';
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
		$html .= self::removeButton($removeButton);

		// Create field dependig on the start of $key.
		if (strpos($key, 'text') === 0) {
			
			$attr .= $placeholder;
			$html .= self::fieldText($tooltip, self::fullscreenBar($Automad, $label), $attr, $value);

		} else if (strpos($key, 'image') === 0 && strpos($key, 'images') === false) {
		
			$attr .= ' value="' . $value . '"' . $placeholder . $tooltip;
			$html .= self::fieldImage($attr);

		} else if (strpos($key, 'url') === 0) {
		
			$attr .= ' value="' . $value . '"' . $placeholder . $tooltip;
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
			
			$html .= self::fieldDate($tooltip, $attr, $attrDate, $attrTime);

		} else if (strpos($key, 'checkbox') === 0) {
			
			if ($value) {
				$attr .= ' checked';
			}

			$text = ucwords(trim(preg_replace('/([A-Z])/', ' $1', str_replace('_', ' ', str_replace('checkbox', '', $key)))));
			$html .= self::fieldCheckbox($tooltip, $text, $attr);

		} else if (strpos($key, 'color') === 0) {

			if (strlen($value)) {
				$color = $value;
			} else {
				$color = $shared;
			} 
			
			$attr .= ' value="' . $value . '"' . $placeholder . $tooltip;			
			$html .= self::fieldColor($color, $attr);
			
		} else if (strpos($key, '+') === 0) {

			$editorId = 'am-block-editor-' . str_replace('+', '', $key);
			$html .= self::fieldBlockEditor($tooltip, $editorId, self::fullscreenBar($Automad, $label), $attr, $value);

		} else {
			
			$attr .= $placeholder . $tooltip;
			$html .= self::fieldDefault($attr, $value);
			
		}
		
		$html .= '</div>';
		
		return $html;
		
	}


}