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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\System;

use Automad\UI\Components\Form\Select;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The cache system setting component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Cache {
	/**
	 * Renders the cache component.
	 *
	 * @return string The rendered HTML
	 */
	public static function render() {
		$Text = Text::getObject();

		if (AM_CACHE_ENABLED) {
			$enabled = 'checked';
		} else {
			$enabled = '';
		}

		$monitor = Select::render(
			'cache[monitor-delay]',
			array(
				'1 min' => 60,
				'2 min' => 120,
				'5 min' => 300
			),
			AM_CACHE_MONITOR_DELAY,
			Text::get('sys_cache_monitor')
		);

		$lifetime = Select::render(
			'cache[lifetime]',
			array(
				'1 h' => 3600,
				'6 h' => 21600,
				'12 h' => 43200
			),
			AM_CACHE_LIFETIME,
			Text::get('sys_cache_lifetime')
		);

		if ($tmp = FileSystem::getTmpDir()) {
			$purge = <<< HTML
				<!-- Purge Cache -->
				<p>$Text->sys_cache_purge_info</p>
				<form data-am-controller="Cache::purge">
					<button type="submit" class="uk-button uk-button-success uk-button-large">
						$Text->sys_cache_purge
						&nbsp;<i class="uk-icon-angle-right"></i>
						&nbsp;<span class="uk-badge">$tmp</span>
					</button>
				</form>
			HTML;
		}

		return <<< HTML
			<p>$Text->sys_cache_info</p>
			<!-- Cache Enable / Settings -->
			<form 
			class="uk-form uk-form-stacked" 
			data-am-controller="Config::update" 
			data-am-auto-submit
			>
				<!-- Cache Enable -->
				<input type="hidden" name="type" value="cache" />		
				<label 
				class="am-toggle-switch-large" 
				data-am-toggle="#am-cache-settings, #am-cache-actions"
				>
					$Text->sys_cache_enable
					<input 
					type="checkbox" 
					name="cache[enabled]" 
					value="on"
					$enabled 
					/>
				</label>
				<!-- Cache Settings -->
				<div id="am-cache-settings" class="am-toggle-container">
					<!-- Cache Monitor Delay -->
					<p class="uk-margin-large-top">$Text->sys_cache_monitor_info</p>
					$monitor
					<!-- Cache Lifetime -->
					<p class="uk-margin-large-top">$Text->sys_cache_lifetime_info</p>
					$lifetime
				</div>	
			</form>
			<div id="am-cache-actions" class="am-toggle-container uk-margin-large-top">
				<!-- Clear Cache -->
				<p>$Text->sys_cache_clear_info</p>	
				<form data-am-controller="Cache::clear">
					<button type="submit" class="uk-button uk-button-success uk-button-large uk-margin-bottom">
						<i class="uk-icon-refresh"></i>&nbsp;
						$Text->sys_cache_clear
					</button>
				</form>	
				$purge
			</div>
		HTML;
	}
}
