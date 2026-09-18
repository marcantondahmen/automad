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

use Automad\Auth\Token\AccessToken;
use Automad\System\Ai\Mcp\Server;
use Nyholm\Psr7\Factory\Psr17Factory;

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
	 * 401 challenge otherwise. Only POST requests carrying a JSON-RPC message are supported. For
	 * handshake-era clients, server-initiated SSE streams and explicit session termination (DELETE)
	 * are not implemented. The modern, stateless era (see Automad\System\Ai\Mcp\Server) has no
	 * session lifecycle to terminate, so this limitation does not apply to it.
	 *
	 * @return string
	 */
	public static function render(): string {
		if (!AccessToken::verifyRequest()) {
			return self::unauthorized();
		}

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
