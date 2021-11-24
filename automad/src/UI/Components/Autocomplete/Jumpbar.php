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

namespace Automad\UI\Components\Autocomplete;

use Automad\Core\Automad;
use Automad\Core\Selection;
use Automad\Core\Str;
use Automad\UI\Response;
use Automad\UI\Utils\Text;
use Automad\UI\Utils\URLHashes;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The autocomplete JSON data for jump bar component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Jumpbar {
	/**
	 * Return a JSON formatted string to be used as autocomplete infomation in a jump field.
	 *
	 * The collected data consists of all page titles, URLs and all available tags.
	 *
	 * @param Automad $Automad
	 * @return Response the response object
	 */
	public static function render(Automad $Automad) {
		$Response = new Response();
		$values = array();

		$values = array_merge($values, self::search());
		$values = array_merge($values, self::inPage());
		$values = array_merge($values, self::settings());
		$values = array_merge($values, self::shared());
		$values = array_merge($values, self::packages());
		$values = array_merge($values, self::pages($Automad->getCollection()));

		$Response->setAutocomplete($values);

		return $Response;
	}

	/**
	 * Generate autocomplete items for the in-page edit mode.
	 *
	 * @return array the generated items
	 */
	private static function inPage() {
		return array(
			array(
				'url' => AM_BASE_INDEX,
				'value' => Text::get('btn_inpage_edit'),
				'title' => Text::get('btn_inpage_edit'),
				'subtitle' => '',
				'icon' => 'bookmark-o'
			)
		);
	}

	/**
	 * Generate autocomplete items for packages.
	 *
	 * @return array the generated items
	 */
	private static function packages() {
		return array(
			array(
				'url' => AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=Packages',
				'value' => Text::get('packages_title'),
				'title' => Text::get('packages_title'),
				'subtitle' => '',
				'icon' => 'download'
			)
		);
	}

	/**
	 * Generate autocomplete items for pages.
	 *
	 * @param array $pages
	 * @return array the generated items
	 */
	private static function pages(array $pages) {
		$Selection = new Selection($pages);
		$Selection->sortPages(AM_KEY_MTIME . ' desc');

		$items = array();

		foreach ($Selection->getSelection(false) as $Page) {
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
	 * Generate autocomplete items for search.
	 *
	 * @return array the generated items
	 */
	private static function search() {
		return array(
			array(
				'url' => AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=Search',
				'value' => Text::get('search_title'),
				'title' => Text::get('search_title'),
				'subtitle' => '',
				'icon' => 'search'
			)
		);
	}

	/**
	 * Generate autocomplete items for settings.
	 *
	 * @return array the generated items
	 */
	private static function settings() {
		$sysUrl = AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=System#';
		$hashes = URLHashes::get();

		return array(
			array(
				'url' => $sysUrl . $hashes->system->cache,
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_cache'),
				'title' => Text::get('sys_cache'),
				'subtitle' => '',
				'icon' => 'rocket'
			),
			array(
				'url' => $sysUrl . $hashes->system->users,
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_user'),
				'title' => Text::get('sys_user'),
				'subtitle' => '',
				'icon' => 'user'
			),
			array(
				'url' => $sysUrl . $hashes->system->update,
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_update'),
				'title' => Text::get('sys_update'),
				'subtitle' => '',
				'icon' => 'refresh'
			),
			array(
				'url' => $sysUrl . $hashes->system->feed,
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_feed'),
				'title' => Text::get('sys_feed'),
				'subtitle' => '',
				'icon' => 'rss'
			),
			array(
				'url' => $sysUrl . $hashes->system->language,
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_language'),
				'title' => Text::get('sys_language'),
				'subtitle' => '',
				'icon' => 'flag'
			),
			array(
				'url' => $sysUrl . $hashes->system->headless,
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_headless'),
				'title' => Text::get('sys_headless'),
				'subtitle' => '',
				'icon' => 'headless'
			),
			array(
				'url' => $sysUrl . $hashes->system->debug,
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_debug'),
				'title' => Text::get('sys_debug'),
				'subtitle' => '',
				'icon' => 'bug'
			),
			array(
				'url' => $sysUrl . $hashes->system->config,
				'value' => Text::get('sys_title') . ' ' . Text::get('sys_config'),
				'title' => Text::get('sys_config'),
				'subtitle' => '',
				'icon' => 'file-text-o'
			)
		);
	}

	/**
	 * Generate autocomplete items for shared.
	 *
	 * @return array the generated items
	 */
	private static function shared() {
		return array(
			array(
				'url' => AM_BASE_INDEX . AM_PAGE_DASHBOARD . '?view=Shared',
				'value' => Text::get('shared_title') . ' shared',
				'title' => Text::get('shared_title'),
				'subtitle' => '',
				'icon' => 'files-o'
			)
		);
	}
}
