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

use Automad\Ai\Mcp\Validator\InputValidator;
use Automad\Auth\Token\AccessToken;
use Automad\Core\Str;
use Mcp\Capability\Registry;
use Mcp\Capability\Registry\ReferenceHandler;
use Mcp\Schema\Enum\ProtocolVersion;
use Mcp\Server as SdkServer;
use Mcp\Server\Handler\Request\CallToolHandler;
use Mcp\Server\Session\FileSessionStore;
use Mcp\Server\Transport\StreamableHttpTransport;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A thin wrapper around the mcp/sdk package.
 * Tools are not registered here directly,
 * but discovered by Automad\Ai\Mcp\Provider.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @see https://php.sdk.modelcontextprotocol.io/
 */
class Server {
	/**
	 * The session lifetime in seconds for authenticated clients.
	 */
	private const SESSION_TTL_AUTHENTICATED = 3600;

	/**
	 * The session lifetime in seconds for unauthenticated clients.
	 */
	private const SESSION_TTL_PUBLIC = 300;

	/**
	 * The SDK server instance.
	 */
	private SdkServer $SdkServer;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$isAuthenticated = AccessToken::verifyRequest();

		// The registry is shared with a custom tool call handler that validates the tool arguments
		// with a validator that reports errors that are readable for agents.
		// Request handlers that are added to the builder take precedence over the built-in ones.
		$Registry = new Registry();

		$Builder = SdkServer::builder()
			->setServerInfo(Server::getName(), AM_VERSION, title: Server::getTitle())
			->setSession(self::createSessionStore($isAuthenticated))
			->setModernVersions(array(ProtocolVersion::V2026_07_28))
			->setRegistry($Registry)
			->addRequestHandler(new CallToolHandler(
				$Registry,
				new ReferenceHandler(),
				new NullLogger(),
				new InputValidator()
			));

		foreach (Provider::getTools($isAuthenticated) as $Tool) {
			$Builder->addTool(
				handler: $Tool->getHandler(),
				name: $Tool->getName(),
				title: $Tool->getTitle(),
				description: $Tool->getDescription(),
				inputSchema: $Tool->getInputSchema(),
				annotations: $Tool->getAnnotations()
			);
		}

		$this->SdkServer = $Builder->build();
	}

	/**
	 * Build the server name.
	 *
	 * @return string
	 */
	public static function getName(): string {
		return Str::sanitize(self::getTitle(), true);
	}

	/**
	 * Build the human readable server title.
	 *
	 * @return string
	 */
	public static function getTitle(): string {
		$host = strval(preg_replace('/^https?:\/\//', '', AM_SERVER . AM_BASE_URL));

		return "Automad ($host)";
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

	/**
	 * Create the session store for the current authentication state. Authenticated and unauthenticated
	 * clients never share a store, so that short-lived public sessions can't evict authenticated ones
	 * and a session ID is only valid for the authentication state it was created under. This means that
	 * a client has to initialize a new session after a token was revoked or added.
	 *
	 * @param bool $isAuthenticated
	 * @return FileSessionStore
	 */
	private static function createSessionStore(bool $isAuthenticated): FileSessionStore {
		$dir = AM_DIR_TMP . '/mcp-sessions/' . ($isAuthenticated ? 'admin' : 'public');
		$ttl = $isAuthenticated ? self::SESSION_TTL_AUTHENTICATED : self::SESSION_TTL_PUBLIC;

		return new FileSessionStore($dir, $ttl);
	}
}
