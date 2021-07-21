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
 *	Copyright (c) 2020-2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\UI\Components\Autocomplete;

use Automad\Core\Str;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The autocomplete JSON data for jump bar component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class JumpBar {


	/**
	 *	Return a JSON formatted string to be used as autocomplete infomation in a jump field.
	 *	
	 *	The collected data consists of all page titles, URLs and all available tags.
	 *
	 *	@param object $Automad
	 *	@return string The JSON encoded autocomplete data
	 */
	
	public static function render($Automad) {
		
		$output = array();
		$values = array();
	
		$values = array_merge($values, self::search());
		$values = array_merge($values, self::inPage());
		$values = array_merge($values, self::settings());
		$values = array_merge($values, self::shared());
		$values = array_merge($values, self::packages());
		$values = array_merge($values, self::pages($Automad->getCollection()));

		$output['autocomplete'] = $values;
		
		return $output;
		
	}


	/**
	 *	Generate autocomplete items for pages.
	 *
	 *	@param array $pages
	 *	@return array the generated items
	 */

	private static function pages($pages) {

		$items = array();

		foreach ($pages as $Page) {
			$item = array();
			$item['url'] = AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=Page&url=' . urlencode($Page->origUrl);
			$item['value'] = $Page->get(AM_KEY_TITLE) . ' ' . $Page->origUrl;
			$item['title'] = $Page->get(AM_KEY_TITLE);
			$item['subtitle'] = $Page->origUrl;
			$item['icon'] = 'file-text-o';

			if ($Page->get(AM_KEY_PRIVATE)) {
				$item['icon'] = 'lock';
			}

			$items[] = $item;
		}

		return $items;
	}


	/**
	 *	Generate autocomplete items for the in-page edit mode.
	 *
	 *	@return array the generated items
	 */

	private static function inPage() {

		return array(
			[
				'url' => AM_BASE_INDEX,
				'value' => Text::get('btn_inpage_edit'),
				'title' => Text::get('btn_inpage_edit'),
				'subtitle' => '',
				'icon' => 'bookmark-o'
			]
		);
	}


	/**
	 *	Generate autocomplete items for settings.
	 *
	 *	@return array the generated items
	 */

	private static function settings() {

		$sysUrl = AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=System#';

		return array(
			[
				'url' => $sysUrl . Str::sanitize(Text::get('sys_cache')),
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_cache'),
				'title' => Text::get('sys_cache'),
				'subtitle' => '',
				'icon' => 'rocket'
			],
			[
				'url' => $sysUrl . Str::sanitize(Text::get('sys_user')),
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_user'),
				'title' => Text::get('sys_user'),
				'subtitle' => '',
				'icon' => 'user'
			],
			[
				'url' => $sysUrl . Str::sanitize(Text::get('sys_update')),
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_update'),
				'title' => Text::get('sys_update'),
				'subtitle' => '',
				'icon' => 'refresh'
			],
			[
				'url' => $sysUrl . Str::sanitize(Text::get('sys_language')),
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_language'),
				'title' => Text::get('sys_language'),
				'subtitle' => '',
				'icon' => 'flag'
			],
			[
				'url' => $sysUrl . Str::sanitize(Text::get('sys_headless')),
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_headless'),
				'title' => Text::get('sys_headless'),
				'subtitle' => '',
				'icon' => 'cloud'
			],
			[
				'url' => $sysUrl . Str::sanitize(Text::get('sys_debug')),
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_debug'),
				'title' => Text::get('sys_debug'),
				'subtitle' => '',
				'icon' => 'bug'
			]
		);

	}


	/**
	 *	Generate autocomplete items for search.
	 *
	 *	@return array the generated items
	 */

	private static function search() {

		return array(
			[
				'url' => AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=Search',
				'value' => Text::get('jumpbar_search'),
				'title' => Text::get('jumpbar_search'),
				'subtitle' => '',
				'icon' => 'search'
			]
		);
	}


	/**
	 *	Generate autocomplete items for shared.
	 *
	 *	@return array the generated items
	 */

	private static function shared() {

		return array(
			[
				'url' => AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=Shared',
				'value' => Text::get('shared_title') . ' shared',
				'title' => Text::get('shared_title'),
				'subtitle' => '',
				'icon' => 'files-o'
			]
		);

	}


	/**
	 *	Generate autocomplete items for packages.
	 *
	 *	@return array the generated items
	 */

	private static function packages() {

		return array(
			[
				'url' => AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=Packages',
				'value' => Text::get('packages_title'),
				'title' => Text::get('packages_title'),
				'subtitle' => '',
				'icon' => 'download'
			]
		);

	}
	

}