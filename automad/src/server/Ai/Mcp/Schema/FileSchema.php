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

namespace Automad\Ai\Mcp\Schema;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file schema.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class FileSchema {
	/**
	 * The file import schema.
	 *
	 * @return array
	 */
	public static function import(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad File Import Schema',
			'description' => 'The Automad file import input schema.',
			'type' => 'object',
			'properties' => array(
				'url' => array(
					'type' => 'string',
					'description' => <<< TXT
						The full remote URL of the file that will be imported, for example
						`https://example.com/images/photo.jpg`.
						TXT
				),
				'page' => array(
					'type' => 'string',
					'description' => <<< TXT
						The ID of the page the file is imported to. The ID is the local absolute URL
						path of the page like for example `/about` or `/work/project`.
						If omitted, the file is imported to the shared files.
						TXT
				)
			),
			'required' => array('url')
		);
	}
}
