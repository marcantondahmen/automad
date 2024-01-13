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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Processors;

use Automad\System\Asset;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The mail address processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class MailAddressProcessor {
	private static bool $hasMail = false;

	/**
	 * Obfuscate all stand-alone eMail addresses matched in $str.
	 * Addresses in links are ignored.
	 *
	 * @param string $str
	 * @return string The processed string
	 */
	public static function obfuscate(string $str): string {
		if (!AM_MAIL_OBFUSCATION_ENABLED) {
			return $str;
		}

		$str = preg_replace_callback(
			'/<body.+<\/body>/s',
			array(self::class, 'processBody'),
			$str
		);

		if (self::$hasMail) {
			$str = str_replace('</head>', Asset::css('dist/mail/main.bundle.css', false) . '</head>', $str);
			$str = str_replace('</body>', Asset::js('dist/mail/main.bundle.js', false) . '</body>', $str);
		}

		return $str;
	}

	/**
	 * Add span tags that represent the @ char and dots.
	 *
	 * @param string $email
	 * @return string
	 */
	private static function addTags(string $email): string {
		return str_replace(array('@', '.'), array('<span class="am-at"></span>', '<span class="am-dot"></span>'), $email);
	}

	/**
	 * Encrypt an email address using a given key with a simple xor algorithm.
	 * Based on https://github.com/sathoro/php-xor-cipher/blob/master/XORCipher.php
	 *
	 * @param string $str
	 * @param string $key
	 * @return string
	 */
	private static function encrypt(string $str, string $key): string {
		$keyChars = array_map('ord', str_split($key));
		$keyCount = count($keyChars);
		$strChars = array_map('ord', str_split($str));
		$strCount = count($strChars);

		$output = '';

		for ($i = 0; $i < $strCount; $i++) {
			$output .= chr($strChars[$i] ^ $keyChars[$i % $keyCount]);
		}

		return base64_encode($key . $output);
	}

	/**
	 * Generate a key the is used for encryption.
	 *
	 * @param string $email
	 * @return string
	 */
	private static function generateKey(string $email): string {
		return substr(md5($email), 0, 8);
	}

	/**
	 * The function that is used to process the mody in the regex matches matched in the obfuscate method.
	 *
	 * @param array $matches
	 * @return string
	 */
	private static function processBody(array $matches): string {
		$regexEmail = '[~\w_\.\+\-]+@[\w\.\-]+\.[a-zA-Z]{2,}';
		$body = $matches[0] ?? '';

		$body = preg_replace_callback(
			'/href="mailto:(' . $regexEmail . ')"/is',
			function ($matches) {
				$key = self::generateKey($matches[1]);
				$encoded = self::encrypt($matches[1], $key);
				self::$hasMail = true;

				return 'href="#" data-eml="' . $encoded . '" data-key="' . $key . '"';
			},
			$body
		);

		$body = preg_replace_callback(
			'/(<a[^>]*>.+?<\/a>|(' . $regexEmail . '))/is',
			function ($matches) {
				if (empty($matches[2])) {
					return $matches[0];
				}

				$key = self::generateKey($matches[2]);
				$encoded = self::encrypt($matches[2], $key);
				$label = self::addTags($matches[2]);
				self::$hasMail = true;

				return '<a href="#" data-eml="' . $encoded . '" data-key="' . $key . '">' . $label . '</a>';
			},
			$body
		);

		$body = preg_replace_callback(
			'/' . $regexEmail . '/',
			function ($matches) {
				self::$hasMail = true;

				return self::addTags($matches[0]);
			},
			$body
		);

		return $body;
	}
}
