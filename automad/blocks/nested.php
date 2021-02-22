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
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\Blocks;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The nested editor block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Nested {


	/**	
	 *	Render a nested editor block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {

		$json = json_encode($data->nestedData);
		$html = Core\Blocks::render($json, $Automad);
		$style = '';
		$cardClass = '';

		if (!empty($data->isCard)) {

			$cardClass = ' am-nested-card';
			$cardStyle = array();
			
			if (!empty($data->cardStyle) && is_object($data->cardStyle)) {

				$cardStyle = $data->cardStyle;
				$items = array(
					'color' => '--am-card-color',
					'backgroundColor' => '--am-card-background',
					'borderColor' => '--am-card-border-color',
				);

				foreach ($items as $key => $property) {
					if (!empty($cardStyle->$key)) {
						$style .= "$property: {$cardStyle->$key}; ";
					}
				} 

				if (!empty($cardStyle->shadow)) {
					$style .= 'box-shadow: var(--am-card-shadow); ';
				}

				if (!empty($cardStyle->css)) {
					$style .= preg_replace('/\s+/s', ' ', $cardStyle->css);
				}

				if ($style) {
					$style = 'style="' . trim($style) . '"';
				}

			}

		}

		return <<< HTML
				<section class="am-nested{$cardClass}" $style>
					$html
				</section>
HTML;

	}


}