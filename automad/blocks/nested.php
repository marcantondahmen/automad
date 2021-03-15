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
		$class = '';

		if (!empty($data->style)) {

			if (!empty($data->style->card)) {
				$class = ' am-nested-card';
			}

			if (!empty($data->style->backgroundImage)) {
				$style .= " background-image: url('{$data->style->backgroundImage}');"; 
			}

			if (!empty($data->style->matchRowHeight)) {
				$style .= ' height: 100%;';
			}

			if (!empty($data->style->shadow)) {
				$style .= ' box-shadow: var(--am-nested-shadow);';
			}

			foreach(array(
				'backgroundColor',
				'borderWidth',
				'borderRadius',
				'paddingTop',
				'paddingBottom'
			) as $item) {

				$property = strtolower(preg_replace('/([A-Z])/', '-$1', $item));

				if (!empty($data->style->$item)) {
					$style .= " $property: {$data->style->$item};";
				}

			}

			foreach(array(
				'color',
				'borderColor'
			) as $item) {

				$property = strtolower(preg_replace('/([A-Z])/', '-$1', $item));

				if (!empty($data->style->$item)) {
					$style .= " --am-nested-$property: {$data->style->$item};";
				}

			}

		}

		if ($style) {
			$style = 'style="' . trim($style) . '"';
		}

		return <<< HTML
				<section class="am-container am-nested{$class}" $style>
					$html
				</section>
HTML;

	}


}