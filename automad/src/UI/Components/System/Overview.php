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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\System;

use Automad\UI\Utils\Text;
use Automad\UI\Utils\URLHashes;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The system settings overview component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Overview {
	/**
	 * Renders the overview component.
	 *
	 * @return string The rendered HTML
	 */
	public static function render() {
		$Text = Text::getObject();
		$hashes = URLHashes::get();
		$fn = function ($expression) {
			return $expression;
		};

		return <<< HTML
			<div class="am-system-overview uk-grid">
				{$fn(self::item($Text->sys_cache, $hashes->system->cache, 'rocket', 'cache', 3))}
				{$fn(self::item($Text->sys_update, $hashes->system->update, 'refresh', 'update', 3))}
				{$fn(self::item($Text->sys_feed, $hashes->system->feed, 'rss', 'feed'))}
				{$fn(self::item($Text->sys_headless, $hashes->system->headless, 'headless', 'headless'))}
				{$fn(self::item($Text->sys_debug, $hashes->system->debug, 'bug', 'debug'))}
				{$fn(self::item($Text->sys_user, $hashes->system->users, 'user'))}
				{$fn(self::item($Text->sys_language, $hashes->system->language, 'flag'))}
				{$fn(self::item($Text->sys_config, $hashes->system->config, 'file-text-o'))}
			</div>
		HTML;
	}

	/**
	 * Render a single grid item.
	 *
	 * @param string $title
	 * @param string $hash
	 * @param string $icon
	 * @param string $status
	 * @param int $size
	 * @return string the rendered item
	 */
	private static function item(string $title, string $hash, string $icon, string $status = '', int $size = 2) {
		if ($status) {
			$status = <<< HTML
				<div class="uk-text-small uk-margin-small-top">
					<span data-am-status="$status">
						<i class="uk-icon-circle-o-notch uk-icon-spin uk-icon-justify"></i>
					</span>
				</div>
			HTML;
		}

		return <<< HTML
			<div class="uk-margin-small-bottom uk-width-medium-$size-6">
				<a href="#$hash" class="uk-panel uk-panel-box uk-display-block uk-height-1-1">
					<i class="uk-icon-$icon uk-icon-small"></i>
					<div class="uk-panel-title uk-padding-bottom-remove">
						$title
					</div>
					$status
				</a>
			</div>
		HTML;
	}
}
