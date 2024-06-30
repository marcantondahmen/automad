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
 * Copyright (c) 2014-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin;

use Automad\Core\Session;
use Automad\Core\Text;
use Automad\System\Asset;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The base for all dashboard views.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2014-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Dashboard {
	/**
	 * Render the dashboard HTML.
	 *
	 * @return string the rendered HTML
	 */
	public static function render() {
		$fn = function (mixed $expression): string {
			return $expression;
		};
		$lang = Text::get('__lang__');

		return <<< HTML
			<!DOCTYPE html>
			<html lang="$lang" class="am-ui">
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="robots" content="noindex">
				<meta name="csrf" content="{$fn(Session::getCsrfToken())}">
				<meta
					name="viewport"
					content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"
				>
				<title>Automad</title>
				{$fn(Asset::icon('dist/favicon.ico'))}
				{$fn(Asset::css('dist/admin/vendor.bundle.css'))}
				{$fn(Asset::css('dist/admin/main.bundle.css'))}
				{$fn(Asset::js('dist/admin/vendor.bundle.js'))}
				{$fn(Asset::js('dist/admin/vendor.editorjs.bundle.js'))}
				{$fn(Asset::js('dist/admin/vendor.filerobot.bundle.js'))}
				{$fn(Asset::js('dist/admin/vendor.toastui.bundle.js'))}
				{$fn(Asset::js('dist/admin/main.bundle.js'))}
			</head>
			<body>
				<am-root base-url="{$fn(AM_BASE_URL)}" base-index="{$fn(AM_BASE_INDEX)}"></am-root>
			</body>
			</html>
			HTML;
	}
}
