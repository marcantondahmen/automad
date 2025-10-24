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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Processors;

use Automad\Engine\Document\Body;
use Automad\Engine\Document\Head;
use Automad\System\Asset;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The mail address processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class MailAddressProcessor {
	/**
	 * A boolean that tracks if the page content includes an email address.
	 */
	private bool $hasMail = false;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->hasMail = false;
	}

	/**
	 * Obfuscate all stand-alone eMail addresses matched in $str.
	 * Addresses in links are ignored.
	 *
	 * @param string $str
	 * @return string The processed string
	 */
	public function obfuscate(string $str): string {
		if (!AM_MAIL_OBFUSCATION_ENABLED) {
			return $str;
		}

		$str = preg_replace_callback(
			'/<body.+<\/body>/s',
			array($this, 'processBody'),
			$str
		) ?? '';

		if ($this->hasMail) {
			$str = Head::append($str, Asset::css('dist/build/mail/index.css', false));
			$str = Body::append($str, Asset::js('dist/build/mail/index.js', false));
		}

		return $str;
	}

	/**
	 * Add span tags that represent the @ char and dots.
	 *
	 * @param string $email
	 * @return string
	 */
	private function addTags(string $email): string {
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
	private function encrypt(string $str, string $key): string {
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
	private function generateKey(string $email): string {
		return substr(md5($email), 0, 8);
	}

	/**
	 * The function that is used to process the body in the regex matches matched in the obfuscate method.
	 *
	 * @param array $matches
	 * @return string
	 */
	private function processBody(array $matches): string {
		$regexEmail = '[~\w_\.\+\-]+@[\w\.\-]+\.[a-zA-Z]{2,}';

		/** @var string */
		$body = $matches[0] ?? '';

		$body = preg_replace_callback(
			'/href="mailto:(' . $regexEmail . ')"/is',
			function ($matches) {
				$key = $this->generateKey($matches[1]);
				$encoded = $this->encrypt($matches[1], $key);
				$this->hasMail = true;

				return 'href="#" data-eml="' . $encoded . '" data-key="' . $key . '"';
			},
			$body
		) ?? '';

		$body = preg_replace_callback(
			'/(<a\b[^>]*>.+?<\/a>|(' . $regexEmail . '))/is',
			function ($matches) {
				if (empty($matches[2])) {
					return $matches[0];
				}

				$key = $this->generateKey($matches[2]);
				$encoded = $this->encrypt($matches[2], $key);
				$label = $this->addTags($matches[2]);
				$this->hasMail = true;

				return '<a href="#" data-eml="' . $encoded . '" data-key="' . $key . '">' . $label . '</a>';
			},
			$body
		) ?? '';

		$body = preg_replace_callback(
			'/' . $regexEmail . '/',
			function ($matches) {
				$this->hasMail = true;

				return $this->addTags($matches[0]);
			},
			$body
		) ?? '';

		return $body;
	}
}
