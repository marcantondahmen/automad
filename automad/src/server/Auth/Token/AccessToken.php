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

namespace Automad\Auth\Token;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The access token class. Handles issuing new access tokens and verifying the
 * Bearer access token carried by the current request against the issued tokens,
 * separating token based auth from the logic of the controllers that use it.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class AccessToken {
	/**
	 * Issue a new access token and persist its hash. The raw token is only ever
	 * returned here — only its hash is persisted, so it can't be recovered afterwards.
	 *
	 * @param string $name
	 * @return string
	 */
	public static function issue(string $name): string {
		$accessToken = bin2hex(random_bytes(32));

		$AccessTokenConfig = AccessTokenConfig::load();

		$AccessTokenConfig->addToken(array(
			'id' => bin2hex(random_bytes(16)),
			'name' => $name,
			'tokenHash' => hash('sha256', $accessToken),
			'createdAt' => time()
		));

		$AccessTokenConfig->save();

		return $accessToken;
	}

	/**
	 * Verify the Bearer access token carried by the current request.
	 *
	 * @return bool the matching token record, or null if the request doesn't carry a valid token
	 */
	public static function verifyRequest(): bool {
		$token = self::getBearerToken();

		if (!$token) {
			return false;
		}

		return !empty(AccessTokenConfig::load()->findTokenByHash(hash('sha256', $token)));
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
}
