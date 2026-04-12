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

namespace Automad\Auth;

use Automad\Core\Messenger;
use Automad\Core\Text;
use Automad\Models\UserCollection;
use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use OTPHP\InternalClock;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The TOTP util class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class TOTP {
	const WINDOW = 15;

	/**
	 * Setup a new TOTP configuration that has to be confirmed at a second step.
	 *
	 * @param string $username
	 * @return array{ secret: string, qr: string }
	 */
	public static function setup(string $username): array {
		$issuer = $_SERVER['SERVER_NAME'] ?? '';

		$TOTP = \OTPHP\TOTP::generate(new InternalClock(), 16);
		$TOTP = $TOTP->withLabel($username);
		$TOTP = $TOTP->withIssuer($issuer ? $issuer : 'Automad');

		$secret = $TOTP->getSecret();
		$uri = $TOTP->getProvisioningUri();

		$_SESSION[Session::TOTP_SETUP_SECRET_KEY] = $secret;

		$renderer = new GDLibRenderer(400, 2);
		$writer = new Writer($renderer);
		$qr = base64_encode($writer->writeString($uri));

		return array(
			'secret' => $secret,
			'qr' => "data:image/png;base64,{$qr}"
		);
	}

	/**
	 * Confirm the setup that has been created using TOTP::setup().
	 *
	 * @param string $code
	 * @param Messenger $Messenger
	 * @return bool
	 */
	public static function confirmSetup(string $code, Messenger $Messenger): bool {
		$secret = $_SESSION[Session::TOTP_SETUP_SECRET_KEY];

		if (empty($secret)) {
			return false;
		}

		$confirmed = self::verify($secret, $code);

		if ($confirmed) {
			unset($_SESSION[Session::TOTP_SETUP_SECRET_KEY]);

			$UserCollection = new UserCollection();
			$UserCollection->setCurrentUserTotpSecret($secret);

			return $UserCollection->save($Messenger);
		}

		$Messenger->setError(Text::get('verifyTotpError'));

		return false;
	}

	/**
	 * Disable TOTP by removing the secret on the currently active account.
	 *
	 * @param Messenger $Messenger
	 * @return bool
	 */
	public static function disable(Messenger $Messenger): bool {
		$UserCollection = new UserCollection();
		$UserCollection->setCurrentUserTotpSecret('');

		return $UserCollection->save($Messenger);
	}

	/**
	 * Verify a code.
	 *
	 * @param string $secret
	 * @param string $code
	 * @return bool
	 */
	public static function verify(string $secret, string $code): bool {
		$TOTP = \OTPHP\TOTP::createFromSecret($secret);

		return $TOTP->verify($code, null, self::WINDOW);
	}
}
