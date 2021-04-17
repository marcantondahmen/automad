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
 *	Copyright (c) 2020-2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Blocks class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Blocks {
	

	/**
	 * 	Multidimensional array of collected extension assets grouped by type (CSS/JS).
	 */

	public static $extensionAssets = array();


	/**	
	 * 	Inject block assets into the header of a page.
	 * 	
	 *	@return string the processed HTML
	 */

	public static function injectAssets($str) {

		$versionSanitized = Str::sanitize(AM_VERSION);
		$css = AM_BASE_URL . '/automad/blocks/dist/blocks.min.css?v=' . $versionSanitized;
		$js = AM_BASE_URL . '/automad/blocks/dist/blocks.min.js?v=' . $versionSanitized;

		$assets = <<< HTML
					<link href="$css" rel="stylesheet">
					<script type="text/javascript" src="$js"></script>
HTML;

		// Check if there is already any other script tag and try to prepend all assets as first items.
		if (preg_match('/\<(script|link).*\<\/head\>/is', $str)) {
			return preg_replace('/(\<(script|link).*\<\/head\>)/is', $assets . "\n$1", $str);
		} else {
			return str_replace('</head>', $assets . "\n</head>", $str);
		}

	}

	
	/**	
	 * 	Render blocks created by the EditorJS block editor.
	 * 	
	 *	@param string $json
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($json, $Automad) {
		
		$flexOpen = false;
		$data = json_decode($json);
		$html = '';

		if (!is_object($data)) {
			return false;
		}

		if (!isset($data->blocks)) {
			return false;
		}

		foreach ($data->blocks as $block) {

			try {

				$blockIsFlexItem = (!empty($block->data->widthFraction) && empty($block->data->stretched));

				if (!$flexOpen && $blockIsFlexItem) {
					$html .= '<am-flex>';
					$flexOpen = true;
				}

				if ($flexOpen && !$blockIsFlexItem) {
					$html .= '</am-flex>';
					$flexOpen = false;
				}

				$blockHtml = call_user_func_array(
					'\\Automad\\Blocks\\' . $block->type . '::render',
					array($block->data, $Automad)
				);

				// Stretch block.
				if (!empty($block->data->stretched)) {
					$blockHtml = "<am-stretched>$blockHtml</am-stretched>";
				} else if (!empty($block->data->widthFraction)) {
					$widthFraction = str_replace('/', '-', $block->data->widthFraction);
					$blockHtml = "<am-{$widthFraction}>$blockHtml</am-{$widthFraction}>";
				}

				$html .= $blockHtml;

			} catch (\Exception $e) {

				continue;
				
			}

		}

		if ($flexOpen) {
			$html .= '</am-flex>';
		}

		return $html;

	}
	

}
