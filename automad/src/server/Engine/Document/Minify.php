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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Document;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The minify helper class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Minify {
	/**
	 * Minify custom CSS.
	 *
	 * @param string $css
	 * @return string
	 */
	public static function css(string $css): string {
		return trim(
			/** @var string */
			preg_replace(
				'/(\s(?=\s)|(?<=})\s|(?<={)\s|\s(?={)|\s(?=})|(?<=;)\s|\s(?=;)|(?<=:)\s|(?<=,)\s)/s',
				'',
				preg_replace(
					'/\s+/s',
					' ',
					preg_replace('/\/\*.*?\*\//s', '', $css) ?? ''
				) ?? ''
			) ?? ''
		);
	}
}
