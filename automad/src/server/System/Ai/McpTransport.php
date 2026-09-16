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

namespace Automad\System\Ai;

use Mcp\Server\Transport\BaseTransport;
use Symfony\Component\Uid\Uuid;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A single-request bridge between a classic PHP request/response cycle and the mcp/sdk's
 * transport-driven, session based protocol handler. Unlike the SDK's own StreamableHttpTransport,
 * this class works with plain strings and arrays instead of PSR-7 objects, since Automad doesn't
 * otherwise depend on PSR-7. It only supports the synchronous request/response exchange needed for
 * the classic MCP handshake (initialize, notifications/initialized, tools/list, tools/call); it does
 * not support SSE or server-initiated requests (sampling/elicitation), since none of Automad's tools
 * need them.
 *
 * @extends BaseTransport<array{status: int, body: string, headers: array<string, string>}>
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class McpTransport extends BaseTransport {
	const SESSION_HEADER = 'Mcp-Session-Id';

	private string|null $immediateResponse = null;

	private int|null $immediateStatusCode = null;

	/**
	 * @param string $body
	 * @param array $headers
	 */
	public function __construct(private readonly string $body, private readonly array $headers) {
		parent::__construct();
	}

	/**
	 * Process the request and return a plain response shape (status, body, headers).
	 * Called once by Mcp\Server::run(), which also connects this transport to the protocol handler.
	 *
	 * @return array{status: int, body: string, headers: array<string, string>}
	 */
	public function listen(): mixed {
		try {
			$this->sessionId = self::sessionId($this->headers);
		} catch (\InvalidArgumentException) {
			return self::response(400, array(
				'error' => self::SESSION_HEADER . ' header must be a valid UUID.'
			));
		}

		$this->handleMessage($this->body, $this->sessionId);

		if ($this->immediateResponse !== null) {
			return array(
				'status' => $this->immediateStatusCode ?? 200,
				'body' => $this->immediateResponse,
				'headers' => array('Content-Type' => 'application/json; charset=utf-8')
			);
		}

		if ($this->sessionFiber !== null) {
			return self::response(501, array(
				'error' => 'Server-initiated requests are not supported.'
			));
		}

		return $this->outgoingResponse();
	}

	/**
	 * Capture the response the protocol handler produces immediately (used for protocol level
	 * errors that happen before a session is established, e.g. a parse error).
	 *
	 * @param string $data
	 * @param array $context
	 */
	public function send(string $data, array $context): void {
		$this->immediateResponse = $data;
		$this->immediateStatusCode = $context['status_code'] ?? 200;
	}

	/**
	 * Build the response from the messages the protocol handler queued for the current session,
	 * e.g. the result of an "initialize" or "tools/list" request. Returns an empty 202 response
	 * for a notification, which never produces a queued message.
	 *
	 * @return array{status: int, body: string, headers: array<string, string>}
	 */
	private function outgoingResponse(): array {
		$outgoingMessages = $this->getOutgoingMessages($this->sessionId);

		if (empty($outgoingMessages)) {
			return array('status' => 202, 'body' => '', 'headers' => array());
		}

		$messages = array_column($outgoingMessages, 'message');
		$body = count($messages) === 1 ? $messages[0] : ('[' . implode(',', $messages) . ']');
		$headers = array('Content-Type' => 'application/json; charset=utf-8');

		if ($this->sessionId) {
			$headers[self::SESSION_HEADER] = $this->sessionId->toRfc4122();
		}

		return array('status' => 200, 'body' => $body, 'headers' => $headers);
	}

	/**
	 * Build a JSON error response.
	 *
	 * @param int $status
	 * @param array $payload
	 * @return array{status: int, body: string, headers: array<string, string>}
	 */
	private static function response(int $status, array $payload): array {
		return array(
			'status' => $status,
			'body' => strval(json_encode($payload)),
			'headers' => array('Content-Type' => 'application/json; charset=utf-8')
		);
	}

	/**
	 * Read the session id carried by the request headers, if any.
	 *
	 * @param array $headers
	 * @return Uuid|null
	 */
	private static function sessionId(array $headers): Uuid|null {
		foreach ($headers as $name => $value) {
			if (strcasecmp(strval($name), self::SESSION_HEADER) === 0) {
				$value = trim(strval($value));

				return $value ? Uuid::fromString($value) : null;
			}
		}

		return null;
	}
}
