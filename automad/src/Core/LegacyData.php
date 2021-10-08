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

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The LegacyData class handles the conversion of legacy block data.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class LegacyData {
	/**
	 * The actual data object that has to be tested and converted.
	 */
	private $data;

	/**
	 * The target version that has to be supported.
	 */
	private $targetVersion = '1.9.0';

	/**
	 * The legacy data converter constructor.
	 *
	 * @param object $data
	 */
	public function __construct(object $data) {
		$this->data = $data;
	}

	/**
	 * Convert legacy data such as layout information.
	 *
	 * @return object $data
	 */
	public function convert() {
		$dataVersion = '0.0.0';
		$data = $this->data;

		if (!empty($data->automadVersion)) {
			$dataVersion = $data->automadVersion;
		}

		if (version_compare($dataVersion, $this->targetVersion, '>=')) {
			return $data;
		}

		Debug::log($dataVersion, 'Converting legacy block data');

		$data = $this->convertLayout($data);
		$data = $this->convertLists($data);

		return $data;
	}

	/**
	 * Convert legacy layout properties to new tune api.
	 *
	 * @param object $data
	 * @return object $data
	 */
	private function convertLayout(object $data) {
		foreach ($data->blocks as $block) {
			if (!isset($block->tunes)) {
				$block->tunes = (object) array('layout' => (object) array());

				if (!empty($block->data->widthFraction)) {
					$block->tunes->layout->width = $block->data->widthFraction;
				}

				if (isset($block->data->stretched)) {
					$block->tunes->layout->stretched = $block->data->stretched;
				}
			}
		}

		return $data;
	}

	/**
	 * Convert legacy lists to new nested lists.
	 *
	 * @param object $data
	 * @return object $data
	 */
	private function convertLists(object $data) {
		foreach ($data->blocks as $block) {
			if ($block->type == 'lists') {
				$block->data->items = (object) array_map(function ($item) {
					if (is_string($item)) {
						return (object) array( 'content' => $item, 'items' => (object) array());
					}

					return $item;
				}, (array) $block->data->items);
			}
		}

		return $data;
	}
}
