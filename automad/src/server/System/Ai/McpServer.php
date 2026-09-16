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

use Automad\System\Ai\Mcp\Provider;
use Automad\System\FileSystem;
use Mcp\Server as SdkServer;
use Mcp\Server\Session\FileSessionStore;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A thin wrapper around the mcp/sdk package, exposing Automad's MCP server as a single
 * `handle()` method that a controller can call with a raw request body and headers. Internally
 * it drives the SDK's classic MCP protocol (initialize, notifications/initialized, tools/list,
 * tools/call) through McpTransport, a small transport bridge working with plain strings and
 * arrays instead of PSR-7 objects. Sessions are persisted to disk (McpTransport::SESSION_HEADER
 * carries the session id), since a classic PHP request doesn't live long enough to keep the
 * session created by "initialize" in memory for the following request. Tools and resources are
 * not registered here directly, but discovered by Automad\System\Ai\Mcp\Provider.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class McpServer {
	private SdkServer $SdkServer;

	public function __construct() {
		$host = strval(preg_replace('/^https?:\/\//', '', AM_SERVER . AM_BASE_URL));
		$name = "Automad ($host)";

		$Builder = SdkServer::builder()
			->setServerInfo($name, AM_VERSION, title: $name)
			->setSession(new FileSessionStore(FileSystem::getTmpDir() . '/mcp-sessions'));

		foreach (Provider::getTools() as $Tool) {
			$Builder->addTool(
				handler: $Tool->getHandler(),
				name: $Tool->getName(),
				title: $Tool->getTitle(),
				description: $Tool->getDescription(),
				annotations: $Tool->getAnnotations()
			);
		}

		foreach (Provider::getResources() as $Resource) {
			$Builder->addResource(
				handler: $Resource->getHandler(),
				uri: $Resource->getUri(),
				name: $Resource->getName(),
				title: $Resource->getTitle(),
				description: $Resource->getDescription(),
				mimeType: $Resource->getMimeType(),
				annotations: $Resource->getAnnotations()
			);
		}

		$this->SdkServer = $Builder->build();
	}

	/**
	 * Handle a raw MCP JSON-RPC request and return a plain response shape (status, body, headers)
	 * for the controller to translate into an actual HTTP response.
	 *
	 * @param string $body
	 * @param array $headers
	 * @return array{status: int, body: string, headers: array<string, string>}
	 */
	public function handle(string $body, array $headers): array {
		/** @var array{status: int, body: string, headers: array<string, string>} $result */
		$result = $this->SdkServer->run(new McpTransport($body, $headers));

		return $result;
	}
}
