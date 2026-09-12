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

use Automad\System\Ai\McpConfig;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The MCP controller class. Implements a minimal MCP (Model Context Protocol) JSON-RPC
 * handshake over the Streamable HTTP transport: `initialize`, `notifications/initialized`,
 * and an empty `tools/list`. Real page CRUD tools are added in later sub-features.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class McpController {
	const PROTOCOL_VERSION = '2025-06-18';

	/**
	 * Handle a request to the MCP resource endpoint. Requires a valid, previously issued
	 * Bearer access token (created through the dashboard's MCP system section); returns a
	 * 401 challenge otherwise. Only POST requests carrying a JSON-RPC message are supported;
	 * server-initiated SSE streams and session termination are not implemented.
	 *
	 * @return string
	 */
	public static function render(): string {
		$token = self::getBearerToken();
		$tokenRecord = $token ? McpConfig::load()->findTokenByHash(hash('sha256', $token)) : null;

		if (!$tokenRecord) {
			return self::unauthorized();
		}

		if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
			http_response_code(405);

			return '';
		}

		$message = json_decode(strval(file_get_contents('php://input')), true);

		if (!is_array($message) || !isset($message['method'])) {
			return self::jsonRpcError(null, -32700, 'Parse error', 400);
		}

		$id = $message['id'] ?? null;

		// A message without an id is a notification, no response is expected.
		if (!array_key_exists('id', $message)) {
			http_response_code(202);

			return '';
		}

		switch ($message['method']) {
			case 'initialize':
				return self::jsonRpcResult($id, array(
					'protocolVersion' => self::PROTOCOL_VERSION,
					'capabilities' => array('tools' => new \stdClass()),
					'serverInfo' => array(
						'name' => 'Automad',
						'version' => AM_VERSION
					)
				));
			case 'tools/list':
				return self::jsonRpcResult($id, array('tools' => array()));
			default:
				return self::jsonRpcError($id, -32601, 'Method not found');
		}
	}

	/**
	 * Read the Bearer token from the Authorization header.
	 *
	 * @return string
	 */
	private static function getBearerToken(): string {
		$header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

		if (!$header && function_exists('getallheaders')) {
			$headers = getallheaders();
			$header = $headers['Authorization'] ?? ($headers['authorization'] ?? '');
		}

		if (preg_match('/Bearer\s+(.+)$/i', strval($header), $matches)) {
			return trim($matches[1]);
		}

		return '';
	}

	/**
	 * Encode a payload as JSON, set the response code and the JSON content type header.
	 *
	 * @param array $payload
	 * @param int $httpCode
	 * @return string
	 */
	private static function json(array $payload, int $httpCode = 200): string {
		http_response_code($httpCode);
		header('Content-Type: application/json; charset=utf-8');

		return strval(json_encode($payload));
	}

	/**
	 * Build a JSON-RPC error response.
	 *
	 * @param mixed $id
	 * @param int $code
	 * @param string $message
	 * @param int $httpCode
	 * @return string
	 */
	private static function jsonRpcError(mixed $id, int $code, string $message, int $httpCode = 200): string {
		return self::json(array(
			'jsonrpc' => '2.0',
			'id' => $id,
			'error' => array('code' => $code, 'message' => $message)
		), $httpCode);
	}

	/**
	 * Build a JSON-RPC success response.
	 *
	 * @param mixed $id
	 * @param array $result
	 * @return string
	 */
	private static function jsonRpcResult(mixed $id, array $result): string {
		return self::json(array(
			'jsonrpc' => '2.0',
			'id' => $id,
			'result' => $result
		));
	}

	/**
	 * Return a 401 response challenging the client to authenticate.
	 *
	 * @return string
	 */
	private static function unauthorized(): string {
		header('WWW-Authenticate: Bearer');

		return self::json(array('error' => 'invalid_token'), 401);
	}
}
