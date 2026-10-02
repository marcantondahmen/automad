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
use Automad\Core\Str;
use Nyholm\Psr7\Factory\Psr17Factory;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The MCP controller class. Authenticates and gates requests to the MCP resource endpoint,
 * then delegates the actual MCP JSON-RPC protocol handling to Automad\Ai\Mcp\Server.
 * Also serves the server card that makes the MCP endpoint discoverable by MCP clients.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class McpController {
	/**
	 * Build the server card as described in SEP-1649.
	 *
	 * @see https://github.com/modelcontextprotocol/modelcontextprotocol/issues/1649
	 * @return array
	 */
	public static function getServerCard(): array {
		$host = strval(preg_replace('#^https?://#i', '', strval(AM_SERVER)));

		return array(
			'$schema' => 'https://static.modelcontextprotocol.io/schemas/mcp-server-card/v1.json',
			'version' => '1.0',
			'protocolVersion' => '2025-11-25',
			'serverInfo' => array(
				'name' => 'automad-' . Str::sanitize($host, true),
				'title' => "Automad ($host)",
				'version' => AM_VERSION
			),
			'transport' => array(
				'type' => 'streamable-http',
				'endpoint' => AM_BASE_URL . AM_MCP_SERVER_URL
			),
			'capabilities' => array(
				'tools' => new \stdClass(),
				'resources' => new \stdClass()
			),
			'authentication' => array(
				'required' => false,
				'schemes' => array('bearer')
			),
			'tools' => array('dynamic'),
			'resources' => array('dynamic')
		);
	}

	/**
	 * Handle a request to the MCP resource endpoint.
	 *
	 * @return string
	 */
	public static function handleRequest(): string {
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

		return strval(json_encode(self::getServerCard(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}
}
