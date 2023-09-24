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
 * Copyright (c) 2018-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Str;
use Automad\Models\Page;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Theme type is a custom data type that stores all meta data of an installed theme.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2018-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Theme {
	/**
	 * The theme author.
	 */
	public string $author;

	/**
	 * The theme description.
	 */
	public string $description = '';

	/**
	 * The theme license.
	 */
	public string $license = '';

	/**
	 * A multidimensional mask array.
	 */
	public array $masks = array();

	/**
	 * The theme name.
	 */
	public string $name;

	/**
	 * The theme path.
	 */
	public string $path;

	/**
	 * The theme readme url.
	 */
	public string $readme = '';

	/**
	 * The templates array.
	 */
	public array $templates = array();

	/**
	 * The tooltips array.
	 */
	public array $tooltips = array();

	/**
	 * The theme version.
	 */
	public string $version = '';

	/**
	 * The constructor.
	 *
	 * @param string $themeJson
	 * @param array $composerInstalled
	 */
	public function __construct(string $themeJson, array $composerInstalled) {
		$json = false;
		$path = Str::stripStart(dirname($themeJson), AM_BASE_DIR . AM_DIR_PACKAGES . '/');
		$package = Package::getContainingPackage(dirname($themeJson));

		$defaults = array(
			'name' => $path,
			'description' => false,
			'author' => false,
			'version' => false,
			'license' => false,
			'masks' => array(),
			'tooltips' => array(),
			'readme' => ''
		);

		// Get Composer version and readme URL.
		if (!empty($package)) {
			$packageName = $package['name'];

			if (array_key_exists($packageName, $composerInstalled)) {
				$package = array_intersect_key(
					$composerInstalled[$packageName],
					array_flip(array('version', 'support'))
				);

				$package['readme'] = $package['support']['source'] . '#readme';
			} else {
				$package = array();
			}
		}

		// Decode JSON file.
		$json = FileSystem::readJson($themeJson);

		// Get templates.
		$templates = FileSystem::glob(dirname($themeJson) . '/*.php');

		// Remove the 'page not found' template from the array of templates.
		$templates = array_filter($templates, function ($file) {
			return false === in_array(basename($file), array(Page::TEMPLATE_NAME_404 . '.php'));
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
