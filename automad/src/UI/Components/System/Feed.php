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

use Automad\Core\Parse;
use Automad\System\Server;
use Automad\System\ThemeCollection;
use Automad\UI\Components\Form\Field;
use Automad\UI\Utils\Keys;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The feed system setting component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Feed {
	/**
	 * Renders the feed component.
	 *
	 * @return string The rendered HTML
	 */
	public static function render() {
		$Text = Text::getObject();
		$feedUrl = Server::url() . AM_BASE_INDEX . AM_FEED_URL;
		$keys = self::getKeys();
		$keysConfig = Parse::csv(AM_FEED_FIELDS);

		$keysUsed = array_filter($keysConfig, function ($key) use ($keys) {
			return in_array($key, $keys);
		});

		$keysUnused = array_filter($keys, function ($key) use ($keysConfig) {
			return !in_array($key, $keysConfig);
		});

		$selectionUsed = self::selection($keysUsed, $Text->sys_feed_fields_info_used);
		$selectionUnused = self::selection($keysUnused, $Text->sys_feed_fields_info_unused);

		if (AM_FEED_ENABLED) {
			$enabled = 'checked';
		} else {
			$enabled = '';
		}

		return <<< HTML
			<p>$Text->sys_feed_info</p>
			<form class="uk-form" data-am-controller="Config::update" data-am-auto-submit>
				<input type="hidden" name="type" value="feed" />
				<label class="am-toggle-switch-large" data-am-toggle=".am-feed-settings">
					$Text->sys_feed_enable
					<input 
					type="checkbox" 
					name="feed" 
					value="on"
					$enabled
					/>
				</label>
				<div class="am-feed-settings am-toggle-container uk-margin-top">
					<p>$Text->sys_feed_url</p>
					<div class="am-form-input-button uk-flex">
						<input 
						class="uk-form-controls uk-width-1-1"
						type="text"
						value="$feedUrl"
						disabled>
						<button class="uk-button" title="$Text->btn_copy_url_clipboard" data-am-clipboard="$feedUrl" data-uk-tooltip>
							<i class="uk-icon-clone"></i>
						</button>
					</div>
					<p class="uk-margin-top">$Text->sys_feed_fields</p>
					$selectionUsed
				</div>
			</form>
			<div class="am-feed-settings am-toggle-container">
				<p class="uk-text-center uk-text-muted">
					↓↑
				</p>
				$selectionUnused
			</div>
		HTML;
	}

	/**
	 * Get all relevant keys from all themes.
	 *
	 * @return array the keys array
	 */
	private static function getKeys() {
		$ThemeCollection = new ThemeCollection();
		$keys = array();

		foreach ($ThemeCollection->getThemes() as $Theme) {
			foreach ($Theme->templates as $file) {
				$keys = array_merge($keys, Keys::inTemplate($file));
			}
		}

		$keys = array_unique($keys);
		$keys = array_filter($keys, function ($key) {
			return preg_match('/^(\+|text)/', $key);
		});

		return $keys;
	}

	/**
	 * Generate a sortable selection markup.
	 *
	 * @param array $keys
	 * @param string $info
	 * @return string the rendered HTML
	 */
	private static function selection(array $keys, string $info) {
		$html = '';

		foreach ($keys as $key) {
			$label = Field::labelFromKey($key);

			$html .= <<< HTML
				<div class="am-feed-field uk-flex uk-flex-space-between">
					<input type="hidden" name="fields[]" value="$key">
					<span>$label</span>
					<span><i class="am-u-icon-bars"></i></span>
				</div>
			HTML;
		}

		return <<< HTML
			<div 
			class="am-feed-field-container" 
			data-info="$info"
			data-am-feed-fields
			>
				$html
			</div>
		HTML;
	}
}
