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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks\Utils;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Attr class contains utilities for creating class and style attributes..
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 *
 * @psalm-import-type Tunes from \Automad\Blocks\AbstractBlock
 */
abstract class Attr {
	/**
	 * The array of IDs that are already used.
	 */
	private static array $uniqueIds = array();

	/**
	 * Create an attribute string for id, class and style.
	 *
	 * @param Tunes $tunes
	 * @param array $classes
	 * @param ?array<non-empty-literal-string, string> $styles
	 * @return string
	 */
	public static function render(array $tunes, array $classes = array(), ?array $styles = null): string {
		$id = self::uniqueId($tunes['id'] ?? '');
		$id = strlen($id) == 0 ? '' : 'id="' . $id . '"';

		return join(' ', array_filter(array($id, self::classAttr($tunes, $classes), self::styleAttr($tunes, $styles))));
	}

	/**
	 * Render a class attribute.
	 *
	 * @param array $classes
	 * @return string
	 */
	public static function renderClasses(array $classes): string {
		if (empty($classes)) {
			return '';
		}

		return 'class="' . join(' ', $classes) . '"';
	}

	/**
	 * Render a style attribute.
	 *
	 * @param array<string, string> $styles
	 * @return string
	 */
	public static function renderStyles(array $styles): string {
		if (empty($styles)) {
			return '';
		}

		$rules = array();

		foreach ($styles as $key => $value) {
			$value = preg_replace('/[<>]/', '', $value);
			$key = strtolower(preg_replace('/([A-Z])/', '-$1', $key) ?? '');
			$rules[] = "$key: $value;";
		}

		return 'style="' . join(' ', $rules) . '"';
	}

	/**
	 * Reset the array on unique IDs.
	 */
	public static function resetUniqueIds(): void {
		self::$uniqueIds = array();
	}

	/**
	 * Return a class attribute for the wrapping block element.
	 *
	 * @param Tunes $tunes
	 * @param array $custom
	 * @return string the attribute string
	 */
	private static function classAttr(array $tunes, array $custom = array()): string {
		$classes = array_merge(array('am-block'), $custom);

		if (!empty($tunes['className'])) {
			$classes[] = preg_replace('/[<>]/', '', $tunes['className']);
		}

		return self::renderClasses($classes);
	}

	/**
	 * Generate a spacing styles array from tunes.
	 *
	 * @param Tunes $tunes
	 * @return array<non-empty-literal-string, string> a styles array
	 */
	private static function getPaddingStylesFromTunes(array $tunes): array {
		$sides = array('top', 'right', 'bottom', 'left');
		$styles = array();

		foreach ($sides as $side) {
			if (!empty($tunes['spacing'][$side])) {
				/** @var string */
				$styles["padding-$side"] =  preg_replace('/[<>]/', '', $tunes['spacing'][$side]);
			}
		}

		return $styles;
	}

	/**
	 * Return a style attribute for the wrapping block element.
	 *
	 * @param Tunes $tunes
	 * @param ?array<non-empty-literal-string, string> $styles
	 * @return string the styles attribute
	 */
	private static function styleAttr(array $tunes, ?array $styles = null): string {
		$styles = array_merge(self::getPaddingStylesFromTunes($tunes), $styles ?? array());

		return self::renderStyles($styles);
	}

	/**
	 * Make sure an ID is unique.
	 *
	 * @param string $id
	 * @return string
	 */
	private static function uniqueId(string $id): string {
		if (strlen($id) == 0) {
			return '';
		}

		$base = $id;
		$suffix = 1;

		while (in_array($id, self::$uniqueIds)) {
			$id = "$base-$suffix";
			$suffix++;
		}

		self::$uniqueIds[] = $id;

		return $id;
	}
}
