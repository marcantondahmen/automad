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

namespace Automad\Controllers;

use Automad\Ai\Mcp\Server;
use Nyholm\Psr7\Factory\Psr17Factory;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The MCP controller class. Authenticates and gates requests to the MCP resource endpoint,
 * then delegates the actual MCP JSON-RPC protocol handling to Automad\Ai\Mcp\Server.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class McpController {
	/**
	 * Handle a request to the MCP resource endpoint.
	 *
	 * @return string
	 */
	public static function render(): string {
		if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
			http_response_code(405);

			return '';
		}

		$Psr17Factory = new Psr17Factory();
		$https = strval($_SERVER['HTTPS'] ?? '');
		$scheme = $https !== '' && $https !== 'off' ? 'https' : 'http';
		$uri = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/');

		$Request = $Psr17Factory->createServerRequest('POST', $uri, $_SERVER);

		foreach (getallheaders() ?: array() as $name => $value) {
			$Request = $Request->withHeader($name, $value);
		}

		$Request = $Request->withBody($Psr17Factory->createStream(strval(file_get_contents('php://input'))));

		$Server = new Server();
		$Response = $Server->handle($Request);

		http_response_code($Response->getStatusCode());

		foreach ($Response->getHeaders() as $name => $values) {
			foreach ($values as $value) {
				header("$name: $value", false);
			}
		}

		return strval($Response->getBody());
	}
}
