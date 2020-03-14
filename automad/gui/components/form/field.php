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
use Automad\Core as Core;


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
		
		// The field ID.
		$id = 'am-input-data-' . $key;

		if (!$label) {
			$label = str_replace('+', '<i class="uk-icon-plus"></i> ', $key);
			$label = ucwords(trim(preg_replace('/([A-Z])/', ' $1', str_replace('_', ' ', $label))));
		}
	
		$html = <<< HTML
				<div class="uk-form-row uk-position-relative">
					<label class="uk-form-label" for="$id">
						$label
					</label>
HTML;

		if ($removeButton) {

			$html .= <<< HTML
					 <button 
					 type="button" 
					 class="am-remove-parent uk-position-top-right uk-margin-top uk-close"
					 ></button>
HTML;

		}

		$tooltip = '';
		
		if ($Theme) {
			$tooltip = ' title="' . $Theme->getTooltip($key) . '" data-uk-tooltip';
		}

		// Build attribute string.
		$attr = 'id="' . $id . '" name="data[' . $key . ']"';

		// Global value.
		$shared = htmlspecialchars($Automad->Shared->get($key));

		// Append placeholder to $attr when editing a page. Therefore check if any URL or context (inpage-edit) is set in $_POST.
		$placeholder = '';
		
		if (!empty($_POST['url']) || !empty($_POST['context'])) {
			$placeholder = ' placeholder="' . $shared . '"';
		}
		
		// Create field dependig on the start of $key.
		if (strpos($key, 'text') === 0) {
			
			$attr .= $placeholder;
			
			$html .= <<< HTML
					 <div $tooltip>
						 <textarea 
						 $attr 
						 class="uk-form-controls uk-width-1-1" 
						 rows="10" 
						 data-uk-markdowneditor
						 >$value</textarea>
					 </div>
HTML;
			
		// Only match "image" and not "images". For multiple images a pattern is needed
		// instead of one selected image.
		} else if (strpos($key, 'image') === 0 && strpos($key, 'images') === false) {
		
			$attr .= ' value="' . $value . '"' . $placeholder . $tooltip;

			$html .= <<< HTML
					 <div class="am-form-icon-button-input uk-flex" data-am-select-image-field>
					 	<button type="button" class="uk-button uk-button-large">
							 <i class="uk-icon-folder-open"></i>
						</button>
					 	<input type="text" class="uk-form-controls uk-width-1-1" $attr />
					 </div>
HTML;

		} else if (strpos($key, 'url') === 0) {
		
			$attr .= ' value="' . $value . '"' . $placeholder . $tooltip;

			$html .= <<< HTML
					 <div class="am-form-icon-button-input uk-flex" data-am-link-field>
					 	<button type="button" class="uk-button uk-button-large">
							 <i class="uk-icon-link"></i>
						</button>
					 	<input type="text" class="uk-form-controls uk-width-1-1" $attr />
					 </div>
HTML;
		
		} else if (strpos($key, 'date') === 0) {
			
			$attr .= ' value="' . $value . '"';
			
			$formatDate = 'Y-m-d';
			$formatTime = 'H:i';
			
			$attrDate = 'value="' . Core\Str::dateFormat($value, $formatDate) . '" readonly="true"';
			$attrTime = 'value="' . Core\Str::dateFormat($value, $formatTime) . '" readonly="true"';
			
			if (!empty($_POST['url']) || !empty($_POST['context'])) {
				$attrDate .= ' placeholder="' . Core\Str::dateFormat($shared, $formatDate) . '"';
				$attrTime .= ' placeholder="' . Core\Str::dateFormat($shared, $formatTime) . '"';
			}
			
			$html .= <<< HTML
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
			
		} else if (strpos($key, 'checkbox') === 0) {
			
			if ($value) {
				$attr .= ' checked';
			}

			$text = ucwords(trim(preg_replace('/([A-Z])/', ' $1', str_replace('_', ' ', str_replace('checkbox', '', $key)))));
			
			$html .= <<< HTML
					 <label class="am-toggle-switch uk-button" data-am-toggle $tooltip> 
					 	$text
					 	<input $attr type="checkbox" />
					 </label>
HTML;
			
		} else if (strpos($key, 'color') === 0) {

			if (strlen($value)) {
				$color = $value;
			} else {
				$color = $shared;
			} 
			
			$attr .= ' value="' . $value . '"' . $placeholder . $tooltip;
			
			$html .= <<< HTML
					 <div class="uk-flex" data-am-colorpicker> 
					 	<input type="color" class="uk-button" value="$color" />
					 	<input type="text" class="uk-form-controls uk-width-1-1" $attr />
					 </div>
HTML;
			
		} else if (strpos($key, '+') === 0) {

			$editorId = 'am-block-editor-' . str_replace('+', '', $key);

			$html .= <<< HTML
					 <div class="am-block-editor" $tooltip data-am-block-editor="$editorId">
						<input type="hidden" $attr value="$value">
						<div id="$editorId"></div>
					 </div>
HTML;

		} else {
			
			$attr .= $placeholder . $tooltip;
			
			// The default is a simple textarea.
			$html .= <<< HTML
					 <textarea 
					 $attr 
					 class="uk-form-controls uk-width-1-1" rows="10"
					 >$value</textarea>
HTML;
			
		}
		
		$html .= '</div>';
		
		return $html;
		
	}


}