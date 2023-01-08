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
 * Copyright (c) 2018-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\Core\FileSystem;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Theme type is a custom data type that stores all meta data of an installed theme.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2021 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Theme {
	/**
	 * The theme author.
	 */
	public $author;

	/**
	 * The theme description.
	 */
	public $description = '';

	/**
	 * The theme license.
	 */
	public $license = '';

	/**
	 * A multidimensional mask array.
	 */
	public $masks = array();

	/**
	 * The theme name.
	 */
	public $name;

	/**
	 * The theme path.
	 */
	public $path;

	/**
	 * The theme readme path.
	 */
	public $readme = '';

	/**
	 * The templates array.
	 */
	public $templates = array();

	/**
	 * The tooltips array.
	 */
	public $tooltips = array();

	/**
	 * The theme version.
	 */
	public $version = '';

	/**
	 * The constructor.
	 *
	 * @param string $themeJSON
	 * @param array $composerInstalled
	 */
	public function __construct(string $themeJSON, array $composerInstalled) {
		$json = false;
		$path = Str::stripStart(dirname($themeJSON), AM_BASE_DIR . AM_DIR_PACKAGES . '/');

		$defaults = array(
			'name' => $path,
			'description' => false,
			'author' => false,
			'version' => false,
			'license' => false,
			'masks' => array(),
			'tooltips' => array(),
			'readme' => 'https://github.com/marcantondahmen/automad/tree/master/packages/' . $path . '#readme'
		);

		// Get Composer version.
		if (array_key_exists($path, $composerInstalled)) {
			$package = array_intersect_key(
				$composerInstalled[$path],
				array_flip(array('version', 'support'))
			);

			$package['readme'] = $package['support']['source'] . '#readme';
		} else {
			$package = array();
		}

		// Decode JSON file.
		if (is_readable($themeJSON)) {
			$json = @json_decode(file_get_contents($themeJSON), true);
		}

		if (!is_array($json)) {
			$json = array();
		}

		// Get templates.
		$templates = FileSystem::glob(dirname($themeJSON) . '/*.php');

		// Remove the 'page not found' template from the array of templates.
		$templates = array_filter($templates, function ($file) {
			return false === in_array(basename($file), array(AM_PAGE_NOT_FOUND_TEMPLATE . '.php'));
		});

		// Reindex array in order to force correct JSON encoding.
		$templates = array_values($templates);

		$data = array_merge(
			$defaults,
			$json,
			$package
		);

		$this->author = $data['author'];
		$this->description = $data['description'];
		$this->license = $data['license'];
		$this->masks = $data['masks'];
		$this->name = $data['name'];
		$this->path = $path;
		$this->readme = $data['readme'];
		$this->templates = $templates;
		$this->tooltips = $data['tooltips'];
		$this->version = $data['version'];
	}

	/**
	 * Get the UI mask (page or shared) for hiding variables in the dashboard.
	 *
	 * @param string $mask "page" or "shared"
	 * @return array The mask array
	 */
	public function getMask(string $mask) {
		if (array_key_exists($mask, $this->masks)) {
			return $this->masks[$mask];
		}

		return array();
	}

	/**
	 * Return the tooltip for the requested variable name (key in the data array).
	 *
	 * @param string $key
	 * @return string The tooltip text
	 */
	public function getTooltip(string $key) {
		if (array_key_exists($key, $this->tooltips)) {
			return $this->tooltips[$key];
		}

		return '';
	}
}
