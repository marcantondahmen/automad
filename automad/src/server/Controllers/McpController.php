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

use Automad\Auth\AccessToken;
use Automad\System\Ai\Mcp\Server;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The MCP controller class. Authenticates and gates requests to the MCP resource endpoint,
 * then delegates the actual MCP JSON-RPC protocol handling to Automad\System\Ai\Mcp\Server.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class McpController {
	/**
	 * Handle a request to the MCP resource endpoint. Requires a valid, previously issued
	 * Bearer access token (created through the dashboard's Access Tokens system section); returns a
	 * 401 challenge otherwise. Only POST requests carrying a JSON-RPC message are supported;
	 * server-initiated SSE streams and session termination are not implemented.
	 *
	 * @return string
	 */
	public static function render(): string {
		$tokenRecord = AccessToken::verifyRequest();

		if (!$tokenRecord) {
			return self::unauthorized();
		}

		if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
			http_response_code(405);

			return '';
		}

		$Server = new Server();
		$result = $Server->handle(strval(file_get_contents('php://input')), getallheaders() ?: array());

		http_response_code($result['status']);

		foreach ($result['headers'] as $name => $value) {
			header("$name: $value");
		}

		return $result['body'];
	}

	/**
	 * Return a 401 response challenging the client to authenticate.
	 *
	 * @return string
	 */
	private static function unauthorized(): string {
		http_response_code(401);
		header('WWW-Authenticate: Bearer');
		header('Content-Type: application/json; charset=utf-8');

		return strval(json_encode(array('error' => 'invalid_token')));
	}
}
