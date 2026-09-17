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

namespace Automad\System\Ai\Mcp;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * Encodes and decodes page slugs into URL-safe resource ids for use in MCP resource URIs.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ResourceId {
	/**
	 * Decode a URL-safe resource id back into the original page slug.
	 *
	 * @param string $id
	 * @return string
	 */
	public static function decode(string $id): string {
		return base64_decode(
			strtr($id, '-_', '+/') . str_repeat('=', (4 - strlen($id) % 4) % 4)
		);
	}

	/**
	 * Encode a page slug or any other handle into a URL-safe resource id.
	 *
	 * @param string $handle
	 * @return string
	 */
	public static function encode(string $handle): string {
		return rtrim(strtr(base64_encode($handle), '+/', '-_'), '=');
	}
}
