<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI\Components\System;
use Automad\GUI\Components as Components;
use Automad\GUI\Text as Text;
use Automad\GUI\FileSystem as FileSystem;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The cache system setting component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Cache {


	/**
	 * 	Renders the cache component.
	 * 
	 *	@return string The rendered HTML
	 */

	public static function render() {

		$Text = Text::getObject();

		if (AM_CACHE_ENABLED) { 
			$enabled = 'checked'; 
		} else {
			$enabled = '';
		}

		$monitor = Components\Form\Select::render(
			'cache[monitor-delay]',
			array(
				'1 min' => 60,
				'2 min' => 120,
				'5 min' => 300
			),
			AM_CACHE_MONITOR_DELAY,
			Text::get('sys_cache_monitor')
		); 

		$lifetime = Components\Form\Select::render(
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
					<form data-am-handler="purge_cache">
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
				data-am-handler="update_config" 
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
					<form data-am-handler="clear_cache">
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