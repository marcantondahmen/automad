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

class Section extends Paragraph {


	/**
	 *	Render a section editor block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {

		$json = json_encode($data->content);
		$html = Core\Blocks::render($json, $Automad);
		$style = '';
		$classes = array();

		if (!empty($data->justify)) {
			$classes[] = "am-justify-{$data->justify}";
		}

		if (!empty($data->gap)) {
			$style .= " --am-flex-gap: {$data->gap};";
		}

		if (!empty($data->minBlockWidth)) {
			$style .= " --am-flex-min-block-width: {$data->minBlockWidth};";
		}

		if (!empty($data->style)) {

			if (!empty($data->style->card)) {
				$classes[] = 'am-card';
			}

			if (!empty($data->style->backgroundImage)) {
				$style .= " background-image: url('{$data->style->backgroundImage}');"; 
			}

			if (!empty($data->style->overflowHidden)) {
				$style .= ' overflow: hidden;';
			}

			if (!empty($data->style->matchRowHeight)) {
				$style .= ' height: 100%;';
			}

			if (!empty($data->style->shadow)) {
				$style .= ' box-shadow: var(--am-section-shadow);';
			}

			foreach(array(
				'backgroundColor',
				'backgroundBlendMode',
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

		$class = self::classAttr($classes);

		return <<< HTML
				<am-section $class $style>
					$html
				</am-section>
HTML;

	}


}