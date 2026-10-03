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

use Automad\Ai\Mcp\Card;
use Automad\Ai\Mcp\Server;
use Nyholm\Psr7\Factory\Psr17Factory;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The MCP controller class. Authenticates and gates requests to the MCP endpoint,
 * then delegates the actual MCP JSON-RPC protocol handling to Automad\Ai\Mcp\Server.
 * Also serves the server card that makes the MCP endpoint discoverable by MCP clients and
 * a human readable version of it for browsers requesting the MCP endpoint.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class McpController {
	/**
	 * Handle a request to the MCP endpoint.
	 *
	 * @return string
	 */
	public static function handleRequest(): string {
		$method = $_SERVER['REQUEST_METHOD'] ?? '';

		if ($method !== 'POST') {
			return self::handleNonPostRequest($method);
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
	 * Serve the dynamically generated server card used for discovering the MCP endpoint.
	 *
	 * @return string
	 */
	public static function serverCard(): string {
		$method = $_SERVER['REQUEST_METHOD'] ?? '';

		if (!in_array($method, array('GET', 'HEAD', 'OPTIONS'), true)) {
			http_response_code(405);

			return '';
		}

		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, OPTIONS');

		if ($method === 'OPTIONS') {
			http_response_code(204);

			return '';
		}

		header('Content-Type: application/json');
		header('Cache-Control: public, max-age=3600');

		return Card::json();
	}

	/**
	 * Handle a non-POST request to the MCP endpoint. Browsers get the human readable server card,
	 * everything else (including clients asking for a server-sent event stream that is not offered)
	 * gets a 405 response.
	 *
	 * @param string $method
	 * @return string
	 */
	private static function handleNonPostRequest(string $method): string {
		$accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');

		if (
			in_array($method, array('GET', 'HEAD'), true) &&
			str_contains($accept, 'text/html') &&
			!str_contains($accept, 'text/event-stream')
		) {
			header('Content-Type: text/html; charset=utf-8');

			return $method === 'HEAD' ? '' : Card::html();
		}

		header('Allow: POST');
		http_response_code(405);

		return '';
	}
}
