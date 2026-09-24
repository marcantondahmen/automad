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
 * A thin wrapper around the mcp/sdk package.
 * Tools and resources are not registered here directly,
 * but discovered by Automad\Ai\Mcp\Provider.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Server {
	/**
	 * The SDK server instance.
	 */
	private SdkServer $SdkServer;

	/**
	 * The constructor.
	 */
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
