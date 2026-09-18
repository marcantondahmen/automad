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

namespace Automad\Ai\Mcp;

use Automad\System\FileSystem;
use Mcp\Schema\Enum\ProtocolVersion;
use Mcp\Server as SdkServer;
use Mcp\Server\Session\FileSessionStore;
use Mcp\Server\Transport\StreamableHttpTransport;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A thin wrapper around the mcp/sdk package, exposing Automad's MCP server as a single
 * `handle()` method that a controller can call with a PSR-7 server request. It deliberately
 * serves both MCP protocol eras from this one endpoint: the handshake era (initialize,
 * notifications/initialized, tools/list, tools/call, Mcp-Session-Id) that every real-world MCP
 * client speaks today, and the SDK's stateless "modern era" (SEP-2575, no initialize, per-request
 * `_meta` versioning, server/discover), pinned to the single revision this codebase has been
 * tested against via setModernVersions() rather than left on the SDK's open-ended default. Which
 * era answers a given request is decided per-request by the SDK's own StreamableHttpTransport;
 * Automad does not implement or influence that routing, and both eras are dispatched through the
 * exact same tool/resource/resourceTemplate handlers below - none of them need to be era-aware.
 * Sessions are persisted to disk (StreamableHttpTransport::SESSION_HEADER carries the session
 * id), since a classic PHP request doesn't live long enough to keep the session created by
 * "initialize" in memory for the following request. This is a handshake-era-only concern - the
 * modern era is stateless by design and never touches the session store - but it must stay
 * unconditional, since handshake clients depend on it to survive across separate PHP-FPM
 * requests. Tools and resources are not registered here directly, but discovered by
 * Automad\Ai\Mcp\Provider.
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
			->setSession(new FileSessionStore(FileSystem::getTmpDir() . '/mcp-sessions'))
			->setModernVersions(array(ProtocolVersion::V2026_07_28));

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
				uri: 'automad://' . $Resource->getName(),
				name: $Resource->getName(),
				title: $Resource->getTitle(),
				description: $Resource->getDescription(),
				mimeType: $Resource->getMimeType(),
				annotations: $Resource->getAnnotations()
			);
		}

		foreach (Provider::getResourceTemplates() as $ResourceTemplate) {
			$Builder->addResourceTemplate(
				handler: $ResourceTemplate->getHandler(),
				uriTemplate: 'automad://' . $ResourceTemplate->getUriTemplate(),
				name: $ResourceTemplate->getName(),
				title: $ResourceTemplate->getTitle(),
				description: $ResourceTemplate->getDescription(),
				mimeType: $ResourceTemplate->getMimeType(),
				annotations: $ResourceTemplate->getAnnotations()
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
