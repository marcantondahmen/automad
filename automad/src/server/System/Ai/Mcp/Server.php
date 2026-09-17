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

use Automad\System\FileSystem;
use Mcp\Server as SdkServer;
use Mcp\Server\Session\FileSessionStore;
use Mcp\Server\Transport\StreamableHttpTransport;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A thin wrapper around the mcp/sdk package, exposing Automad's MCP server as a single
 * `handle()` method that a controller can call with a PSR-7 server request. Internally it
 * drives the SDK's classic MCP protocol (initialize, notifications/initialized, tools/list,
 * tools/call) through the SDK's own StreamableHttpTransport. Sessions are persisted to disk
 * (StreamableHttpTransport::SESSION_HEADER carries the session id), since a classic PHP
 * request doesn't live long enough to keep the session created by "initialize" in memory for
 * the following request. Tools and resources are not registered here directly, but discovered
 * by Automad\System\Ai\Mcp\Provider.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Server {
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
				uri: 'automad://' . strval($Resource->getName()),
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
	 * Handle an MCP JSON-RPC request carried by a PSR-7 server request and return the PSR-7
	 * response for the controller to emit.
	 *
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface {
		/** @var ResponseInterface $response */
		$response = $this->SdkServer->run(new StreamableHttpTransport($request));

		return $response;
	}
}
