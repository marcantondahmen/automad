<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\Core\Automad;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The buttons block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Buttons extends AbstractBlock {
	/**
	 * Render a buttons block.
	 *
	 * @param object $data
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(object $data, Automad $Automad) {
		$defaults = array(
			'primaryText' => '',
			'primaryLink' => '',
			'primaryStyle' => (object) array(),
			'secondaryText' => '',
			'secondaryLink' => '',
			'secondaryStyle' => (object) array(),
			'alignment' => 'left'
		);

		$options = array_merge($defaults, (array) $data);
		$data = (object) $options;
		$html = '';

		foreach (array('primary', 'secondary') as $item) {
			if (trim($data->{$item . 'Text'}) && trim($data->{$item . 'Link'})) {
				$text = htmlspecialchars_decode($data->{$item . 'Text'});
				$styleObj = $data->{$item . 'Style'};
				$style = '';
				$link = $data->{$item . 'Link'};
				$class = '';

				foreach ($styleObj as $key => $value) {
					if ($key != 'class') {
						$style .= '--am-button-' .
						strtolower(preg_replace('/([A-Z])/', '-$1', $key)) .
						": $value; ";
					}
				}

				if ($style) {
					$style = trim($style);
					$style = 'style="' . $style . '"';
				}

				if (!empty($styleObj->class)) {
					$class = " {$styleObj->class}";
				}

				$html .= <<< HTML
					<a 
					href="$link"
					class="am-button{$class}"
					$style
					>
						$text
					</a>
				HTML;
			}
		}

		if ($html) {
			$classes = array();

			if ($data->alignment == 'center') {
				$classes[] = 'am-center';
			}

			$class = self::classAttr($classes);

			$html = "<am-buttons $class>$html</am-buttons>";
		}

		return $html;
	}
}
