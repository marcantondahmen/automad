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

use Automad\App;
use Automad\Core\FileSystem;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The login rate limiter class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @psalm-type LoginRateLimiterData = array{
 *		attempts: array<int>,
 *		blockedUntil: int
 * }
 */
class LoginRateLimiter {
	const BLOCK_TIME = 900;
	const MAX_ATTEMPTS = 5;
	const WINDOW = 600;

	/**
	 * Register a failed attempt.
	 *
	 * @param string $username
	 */
	public static function registerFailure(string $username): void {
		$file = self::getFile($username);
		$data = self::read($file);

		$now = time();

		$attempts = self::filterAttempts($data['attempts']);

		$attempts[] = $now;

		if (count($attempts) >= self::MAX_ATTEMPTS) {
			$data['blockedUntil'] = $now + self::BLOCK_TIME;
			$attempts = array();
		}

		$data['attempts'] = $attempts;

		self::write($file, $data);
	}

	/**
	 * Reset on successful login.
	 *
	 * @param string $username
	 */
	public static function reset(string $username): void {
		$file = self::getFile($username);

		if (file_exists($file)) {
			unlink($file);
		}
	}

	/**
	 * Check if user is currently blocked.
	 *
	 * @param string $username
	 */
	public static function verifyAccess(string $username): void {
		$file = self::getFile($username);
		$data = self::read($file);

		if ($data['blockedUntil'] > time()) {
			App::exit(Text::get('signInRateLimitError'), '', 429);
		};
	}

	/**
	 * Filter attempts that are older than the window.
	 *
	 * @param array<int> $attempts
	 * @return array<int>
	 */
	private static function filterAttempts(array $attempts): array {
		$now = time();

		return array_values(array_filter($attempts, function ($timestamp) use ($now) {
			return ($now - $timestamp) <= self::WINDOW;
		}));
	}

	/**
	 * Get rate limit data file path for a given user.
	 *
	 * @param string $username
	 * @return string
	 */
	private static function getFile(string $username): string {
		$hash = sha1($username);

		return AM_LOGIN_RATE_LIMITER_PATH . "/$hash.json";
	}

	/**
	 * Read login rate limit data from a user file.
	 *
	 * @param string $file
	 * @return LoginRateLimiterData
	 */
	private static function read(string $file): array {
		$fallback = array(
			'attempts' => array(),
			'blockedUntil' => 0
		);

		if (!is_readable($file)) {
			return $fallback;
		}

		/** @var LoginRateLimiterData */
		$data = FileSystem::readJson($file, true);

		return array_merge(
			$fallback,
			$data
		);
	}

	/**
	 * Write data to file.
	 *
	 * @param string $file
	 * @param array $data
	 */
	private static function write(string $file, array $data): void {
		FileSystem::writeJson($file, $data);
	}
}
