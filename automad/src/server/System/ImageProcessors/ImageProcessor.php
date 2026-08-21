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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\System\ImageProcessors;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The image processor interface.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
interface ImageProcessor {
	/**
	 * The resize function.
	 *
	 * @param string $path
	 * @param string $output
	 * @param int $newWidth
	 * @param int $newHeight
	 * @param int $originalWidth
	 * @param int $originalHeight
	 * @param int $requestedWidth
	 * @param int $requestedHeight
	 * @param bool $crop
	 * @return bool
	 */
	public function resize(
		string $path,
		string $output,
		int $newWidth,
		int $newHeight,
		int $originalWidth,
		int $originalHeight,
		int $requestedWidth,
		int $requestedHeight,
		bool $crop
	): bool;
}
