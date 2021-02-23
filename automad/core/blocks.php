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
	 * 	True if the page contains a block variable.
	 */

	private static $pageHasBlocks = false;


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

		// If no block was rendered before, just return $str.
		if (!self::$pageHasBlocks) {
			return $str;
		}

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
		
		self::$pageHasBlocks = true;
		$gridOpen = false;
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

				$blockIsGridItem = (!empty($block->data->columnSpan) && empty($block->data->stretched));

				if (!$gridOpen && $blockIsGridItem) {
					$html .= '<section class="am-block-grid">';
					$gridOpen = true;
				}

				if ($gridOpen && !$blockIsGridItem) {
					$html .= '</section>';
					$gridOpen = false;
				}

				$blockHtml = call_user_func_array(
					'\\Automad\\Blocks\\' . $block->type . '::render',
					array($block->data, $Automad)
				);

				// Stretch block.
				if (!empty($block->data->stretched)) {
					$blockHtml = <<< HTML
								<div 
								class="am-stretched" 
								style="width: 100%; max-width: 100%;"
								>
									$blockHtml
								</div>
HTML;
				}

				// Apply grid.
				if ($blockIsGridItem) {

					$class = '';

					foreach (array('columnSpan', 'columnStart') as $key) {

						if (isset($block->data->{$key})) {
							$prefix = strtolower(preg_replace('/([A-Z])/', '-$1', $key));
							$value = $block->data->{$key};
							$class .= " am-block-{$prefix}-{$value}";
						}

					}

					$class = trim($class);

					$blockHtml = <<< HTML
								<div class="{$class}">
									$blockHtml
								</div>
HTML;
				}

				$html .= $blockHtml;

			} catch (\Exception $e) {

				continue;
				
			}

		}

		if ($gridOpen) {
			$html .= '</section>';
		}

		return $html;

	}
	

}
