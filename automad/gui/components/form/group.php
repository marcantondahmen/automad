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
use Automad\GUI\Text as Text;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The form group component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Group {

	
	/**
	 *	Create form fields for page/shared variables.         
	 *      
	 *  Passing a string for $addVariableIdPrefix will create the required markup for a modal dialog to add variables.   
	 *  Note used prefix must match the ID selectors defined in 'add_variable.js'.
	 *
	 * 	@param object $Automad
	 *	@param array $keys
	 *	@param array $data
	 *	@param string $addVariableIdPrefix (automatically prefies all IDs for the HTML elements needed for the modal to add variables)
	 *	@param object $Theme
	 *	@return string The HTML for the textarea
	 */
	
	public static function render($Automad, $keys, $data = array(), $addVariableIdPrefix = false, $Theme = false) {
			
		$html = '';
		
		// The HTML for the variable fields.
		foreach ($keys as $key) {
		
			if (isset($data[$key])) {
				$value = $data[$key];
			} else {
				$value = '';
			}
	
			// Note that passing $addVariableIdPrefix only to create remove buttons if string is not empty.
			$html .= Field::render($Automad, $key, $value, $addVariableIdPrefix, $Theme);
			
		}
		
		// Optionally create the HTML for a dialog to add more variables to the form.
		// Therefore $addVariableIdPrefix has to be defined.
		if ($addVariableIdPrefix) {
			
			$addVarModalId = $addVariableIdPrefix . '-modal';
			$addVarSubmitId = $addVariableIdPrefix . '-submit';
			$addVarInputlId = $addVariableIdPrefix . '-input';
			$addVarContainerId = $addVariableIdPrefix . '-container';
			
			$btnAddVar = Text::get('btn_add_var');
			$btnClose = Text::get('btn_close');
			$pageVarName = Text::get('page_var_name');
			$errorVarExists = Text::get('error_var_exists');
			$errorVarName = Text::get('error_var_name');

			$html =	<<< HTML
					<div id="$addVarContainerId" class="uk-margin-bottom">$html</div>
					<a href="#$addVarModalId" class="uk-button uk-button-success uk-margin-small-top" data-uk-modal>
						<i class="uk-icon-plus"></i>&nbsp;
						$btnAddVar
					</a>
					<div id="$addVarModalId" class="uk-modal">
						<div class="uk-modal-dialog">
							<div class="uk-modal-header">
								$btnAddVar
								<a href="" class="uk-modal-close uk-close"></a>
							</div>	
							<input 
							id="$addVarInputlId" 
							type="text" 
							class="uk-form-controls uk-width-1-1"
							placeholder="$pageVarName" 
							required
							data-am-enter="#$addVarSubmitId" 
							data-am-watch-exclude 
							/>
							<div class="uk-modal-footer uk-text-right">
								<button type="button" class="uk-modal-close uk-button">
									<i class="uk-icon-close"></i>&nbsp;
									$btnClose
								</button>
								<button 
								id="$addVarSubmitId" 
								type="button" 
								class="uk-button uk-button-success" 
								data-am-error-exists="$errorVarExists" 
								data-am-error-name="$errorVarName"
								>
									<i class="uk-icon-plus"></i>&nbsp;
									$btnAddVar
								</button>
							</div>
						</div>
					</div>
HTML;
								
		} 
		
		return $html;
			
	}
	

}