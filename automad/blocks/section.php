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
 *	The section editor block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Section {


	/**	
	 *	Render a section editor block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {

		$json = json_encode($data->sectionData);
		$html = Core\Blocks::render($json, $Automad);
		$style = '';
		$class = '';

		if (!empty($data->justifyContent)) {
			$class = " am-section-justify-{$data->justifyContent}";
		}

		if (!empty($data->style)) {

			if (!empty($data->style->card)) {
				$class = ' am-section-card';
			}

			if (!empty($data->style->backgroundImage)) {
				$style .= " background-image: url('{$data->style->backgroundImage}');"; 
			}

			if (!empty($data->style->matchRowHeight)) {
				$style .= ' height: 100%;';
			}

			if (!empty($data->style->shadow)) {
				$style .= ' box-shadow: var(--am-section-shadow);';
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
					$style .= " --am-section-$property: {$data->style->$item};";
				}

			}

		}

		if ($style) {
			$style = 'style="' . trim($style) . '"';
		}

		return <<< HTML
				<section class="am-container am-section{$class}" $style>
					$html
				</section>
HTML;

	}


}