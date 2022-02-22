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

namespace Automad\UI\Autocomplete;

use Automad\Core\Automad;
use Automad\Core\Selection;
use Automad\UI\Utils\SwitcherSections;
use Automad\UI\Utils\Text;

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
	 * Generate the autocomplete object for the jumpbar.
	 *
	 * @param Automad $Automad
	 * @return array
	 */
	public static function render(Automad $Automad) {
		$values = array();

		$values = array_merge($values, self::search());
		$values = array_merge($values, self::inPage());
		$values = array_merge($values, self::settings());
		$values = array_merge($values, self::shared());
		$values = array_merge($values, self::packages());
		$values = array_merge($values, self::pages($Automad->getCollection()));

		return $values;
	}

	/**
	 * Generate autocomplete items for the in-page edit mode.
	 *
	 * @return array the generated items
	 */
	private static function inPage() {
		return array(
			array(
				'external' => AM_BASE_INDEX,
				'value' => Text::get('inPageEdit'),
				'title' => Text::get('inPageEdit'),
				'subtitle' => '',
				'icon' => 'house'
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
				'target' => 'packages',
				'value' => Text::get('packagesTitle'),
				'title' => Text::get('packagesTitle'),
				'subtitle' => '',
				'icon' => 'box-seam'
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
			$item['target'] = 'page?url=' . urlencode($Page->origUrl);
			$item['value'] = $Page->get(AM_KEY_TITLE) . ' ' . $Page->origUrl;
			$item['title'] = $Page->get(AM_KEY_TITLE);
			$item['subtitle'] = $Page->origUrl;
			$item['icon'] = 'file-earmark-text';

			if ($Page->get(AM_KEY_PRIVATE)) {
				$item['icon'] = 'file-earmark-lock2';
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
				'target' => 'search',
				'value' => Text::get('searchTitle'),
				'title' => Text::get('searchTitle'),
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
		$sysUrl = 'system?section=';
		$sections = SwitcherSections::get();

		return array(
			array(
				'target' => $sysUrl . $sections->system->cache,
				'value' => Text::get('systemTitle') . ' ' . Text::get('systemCache'),
				'title' => Text::get('systemCache'),
				'subtitle' => '',
				'icon' => 'lightning'
			),
			array(
				'target' => $sysUrl . $sections->system->users,
				'value' => Text::get('systemTitle') . ' ' . Text::get('systemUsers'),
				'title' => Text::get('systemUsers'),
				'subtitle' => '',
				'icon' => 'people'
			),
			array(
				'target' => $sysUrl . $sections->system->update,
				'value' => Text::get('systemTitle') . ' ' . Text::get('systemUpdate'),
				'title' => Text::get('systemUpdate'),
				'subtitle' => '',
				'icon' => 'arrow-repeat'
			),
			array(
				'target' => $sysUrl . $sections->system->feed,
				'value' => Text::get('systemTitle') . ' ' . Text::get('systemRssFeed'),
				'title' => Text::get('systemRssFeed'),
				'subtitle' => '',
				'icon' => 'rss'
			),
			array(
				'target' => $sysUrl . $sections->system->language,
				'value' => Text::get('systemTitle') . ' ' . Text::get('systemLanguage'),
				'title' => Text::get('systemLanguage'),
				'subtitle' => '',
				'icon' => 'flag'
			),
			array(
				'target' => $sysUrl . $sections->system->headless,
				'value' => Text::get('systemTitle') . ' ' . Text::get('systemHeadless'),
				'title' => Text::get('systemHeadless'),
				'subtitle' => '',
				'icon' => 'cloud'
			),
			array(
				'target' => $sysUrl . $sections->system->debug,
				'value' => Text::get('systemTitle') . ' ' . Text::get('systemDebug'),
				'title' => Text::get('systemDebug'),
				'subtitle' => '',
				'icon' => 'bug'
			),
			array(
				'target' => $sysUrl . $sections->system->config,
				'value' => Text::get('systemTitle') . ' ' . Text::get('systemConfigFile'),
				'title' => Text::get('systemConfigFile'),
				'subtitle' => '',
				'icon' => 'file-earmark-code'
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
				'target' => 'shared',
				'value' => Text::get('sharedTitle') . ' shared',
				'title' => Text::get('sharedTitle'),
				'subtitle' => '',
				'icon' => 'file-earmark-medical'
			)
		);
	}
}
