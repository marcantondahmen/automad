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
 * The image block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Image extends AbstractBlock {
	/**
	 * Render an image block.
	 *
	 * @param object $data
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	public static function render(object $data, Automad $Automad) {
		$class = self::classAttr();

		$img = "<img src=\"{$data->url}\" />";
		$caption = '';

		if (!empty($data->caption)) {
			$caption = "<figcaption>$data->caption</figcaption>";
		}

		if (!empty($data->link)) {
			$img = "<a href=\"{$data->link}\">$img</a>";
		}

		return <<< HTML
			<am-img $class>
				<figure>
					$img
					$caption
				</figure>
			</am-img>
		HTML;
	}
}
