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


namespace Automad\GUI\Components\System;
use Automad\GUI\Components as Components;
use Automad\GUI\Text as Text;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The language system setting component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Language {


	/**
	 * 	Renders the language component.
	 * 
	 *	@return string The rendered HTML
	 */

	public static function render() {

		$Text = Text::getObject();
		$languages = array();
		
		foreach (glob(dirname(AM_FILE_GUI_TEXT_MODULES) . '/*.txt') as $file) {
			
			if (strpos($file, 'english.txt') !== false) {
				$value = '';
			} else {
				$value = \Automad\Core\Str::stripStart($file, AM_BASE_DIR);
			}

			$key = ucfirst(str_replace('.txt', '', basename($file)));
			$languages[$key] = $value;

		}

		$button = Components\Form\Select::render(
			'language', 
			$languages, 
			AM_FILE_GUI_TRANSLATION,
			'',
			'uk-button-large uk-button-success'
		);

		return <<< HTML
				<p>$Text->sys_language_info</p>
				<form 
				class="uk-form uk-form-stacked"
				data-am-handler="update_config" 
				data-am-auto-submit
				>
					<input type="hidden" name="type" value="language" />
					$button
				</form>
HTML;


	}


}