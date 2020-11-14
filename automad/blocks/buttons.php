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


namespace Automad\Blocks;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The buttons block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Buttons {

	
	/**	
	 *	Render a buttons block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	public static function render($data) {

		$defaults = array(
			'primaryText' => '',
			'primaryLink' => '',
			'secondaryText' => '',
			'secondaryLink' => '',
			'alignment' => 'left'
		);

		$options = array_merge($defaults, (array) $data);
		$data = (object) $options;
		$html = '';
		$class = ' class="am-buttons"';

		if ($data->alignment == 'center') {
			$class = ' class="am-buttons am-buttons-center"';
		}

		if (trim($data->primaryText) && trim($data->primaryLink)) {
			$primaryText = htmlspecialchars_decode($data->primaryText);
			$html .= <<< HTML
					<a 
					href="$data->primaryLink"
					class="am-button am-button-primary">
						$primaryText
					</a>
HTML;
		}

		if (trim($data->secondaryText) && trim($data->secondaryLink)) {
			$secondaryText = htmlspecialchars_decode($data->secondaryText);
			$html .= <<< HTML
					<a 
					href="$data->secondaryLink"
					class="am-button">
						$secondaryText
					</a>
HTML;
		}

		if ($html) {

			return "<section$class>$html</section>";

		}

	}


}