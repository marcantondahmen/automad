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

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Auth\Token\AccessToken;
use Automad\Auth\Token\AccessTokenConfig;
use Automad\Core\Request;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The access token controller. Used by the dashboard's system section to issue and
 * revoke access tokens, e.g. for connecting to this installation's MCP server.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class AccessTokenController {
	/**
	 * Issue a new access token and return it. The raw token is only ever available in
	 * this response — only its hash is persisted, so it can't be recovered afterwards.
	 *
	 * @return Response
	 */
	public static function addToken(): Response {
		$Response = new Response();
		$name = trim(strval(Request::post('name')));

		if (empty($name)) {
			return $Response->setError(Text::get('systemAccessTokensAddTokenValidationError'));
		}

		$accessToken = AccessToken::issue($name);

		return $Response->setData(array('accessToken' => $accessToken));
	}

	/**
	 * Get the list of issued tokens, without exposing any token hashes.
	 *
	 * @return Response
	 */
	public static function getTokens(): Response {
		$Response = new Response();

		$tokens = array_map(function ($token) {
			return array(
				'id' => $token['id'],
				'name' => $token['name'],
				'createdAt' => date('c', $token['createdAt'])
			);
		}, AccessTokenConfig::load()->tokens);

		return $Response->setData(array('tokens' => array_values($tokens)));
	}

	/**
	 * Revoke a token.
	 *
	 * @return Response
	 */
	public static function revoke(): Response {
		$Response = new Response();
		$id = Request::post('id');

		if (empty($id)) {
			return $Response;
		}

		$AccessTokenConfig = AccessTokenConfig::load();
		$AccessTokenConfig->removeToken($id);
		$AccessTokenConfig->save();

		return $Response;
	}
}
