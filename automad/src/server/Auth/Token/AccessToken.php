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
	 * The prefix that makes issued tokens recognizable as Automad access tokens.
	 */
	private const PREFIX = 'am_';

	/**
	 * The number of leading token characters (including the prefix) that are stored
	 * as a non-secret preview in order to identify a token. Keep this short, since every
	 * additional character reveals more of the actual secret.
	 */
	private const PREVIEW_LENGTH = 6;

	/**
	 * Issue a new access token and persist its hash. The raw token is only ever
	 * returned here — only its hash and a short, non-secret preview are persisted,
	 * so it can't be recovered afterwards.
	 *
	 * @param string $name
	 * @param string $description
	 * @return string
	 */
	public static function issue(string $name, string $description = ''): string {
		$accessToken = self::PREFIX . bin2hex(random_bytes(32));

		$AccessTokenConfig = AccessTokenConfig::load();

		$AccessTokenConfig->addToken(array(
			'id' => bin2hex(random_bytes(16)),
			'name' => $name,
			'description' => $description,
			'preview' => substr($accessToken, 0, self::PREVIEW_LENGTH),
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
