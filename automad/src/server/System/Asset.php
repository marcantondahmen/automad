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
 * Copyright (c) 2022-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Asset handles loading and cache busting assets that are located in the automad directory.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Asset {
	/**
	 * Render a css link tag.
	 *
	 * @param string $file
	 * @param bool $addBaseUrl
	 * @return string the link tag
	 */
	public static function css(string $file, bool $addBaseUrl = true): string {
		return '<link href="' . self::link($file, $addBaseUrl) . '" rel="stylesheet">';
	}

	/**
	 * Render favicon link tags.
	 *
	 * @return string the script tag
	 */
	public static function favicons(): string {
		$html = '<link href="' . self::link('dist/favicon.svg', true) . '" rel="icon" type="image/svg+xml">';
		$html .= '<link href="' . self::link('dist/favicon.ico', true) . '" rel="alternate icon" type="image/x-icon" sizes="32x32">';

		return $html;
	}

	/**
	 * Render a script tag.
	 *
	 * @param string $file
	 * @param bool $addBaseUrl
	 * @return string the script tag
	 */
	public static function js(string $file, bool $addBaseUrl = true): string {
		return '<script src="' . self::link($file, $addBaseUrl) . '" type="module"></script>';
	}

	/**
	 * Make a path absolute.
	 *
	 * @param string $file
	 * @param bool $addBaseUrl
	 * @return string absolute path
	 */
	private static function absolute(string $file, bool $addBaseUrl): string {
		$baseUrl = '';

		if ($addBaseUrl) {
			$baseUrl = AM_BASE_URL;
		}

		return "$baseUrl/automad/$file";
	}

	/**
	 * Get the full link for a file.
	 *
	 * @param string $file
	 * @param bool $addBaseUrl
	 * @return string the link
	 */
	private static function link(string $file, bool $addBaseUrl): string {
		return self::absolute($file, $addBaseUrl) . '?m=' . self::mTime($file);
	}

	/**
	 * Get the modification time of a given file
	 *
	 * @param string $file
	 * @return int the modification time in seconds
	 */
	private static function mTime(string $file): int {
		$path = AM_BASE_DIR . '/automad/' . $file;

		return intval(filemtime($path));
	}
}
