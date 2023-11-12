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
 * Copyright (c) 2023 by Marc Anton Dahmen
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
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
abstract class Attr {
	/**
	 * Create an attribute string for id, class and style.
	 *
	 * @param object{
	 *		id?: string,
	 *		className?: string,
	 *		spacing?: object{
	 *			top?: string,
	 *			right?: string,
	 *			bottom?: string,
	 *			left?: string
	 *		}} $tunes
	 * @param array $classes
	 * @param ?array<non-empty-literal-string, string> $styles
	 * @return string
	 */
	public static function render(object $tunes, array $classes = array(), ?array $styles = null): string {
		$id = empty($tunes->id) ? '' : 'id="' . $tunes->id . '"';

		return join(' ', array_filter(array($id, self::classAttr($tunes, $classes), self::styleAttr($tunes, $styles))));
	}

	/**
	 * Return a class attribute for the wrapping block element.
	 *
	 * @param object{className?: string} $tunes
	 * @param array $custom
	 * @return string the attribute string
	 */
	private static function classAttr(object $tunes, array $custom = array()): string {
		$classes = array_merge(array('am-block'), $custom);

		if (!empty($tunes->className)) {
			$classes[] = preg_replace('/[<>]/', '', $tunes->className);
		}

		return 'class="' . join(' ', $classes) . '"';
	}

	/**
	 * Generate a spacing styles array from tunes.
	 *
	 * @param object{
	 *		spacing?: object{
	 *			top?: string,
	 *			right?: string,
	 *			bottom?: string,
	 *			left?: string
	 *		}} $tunes
	 * @return array<non-empty-literal-string, string> a styles array
	 */
	private static function getPaddingStylesFromTunes(object $tunes): array {
		$sides = array('top', 'right', 'bottom', 'left');
		$styles = array();

		foreach ($sides as $side) {
			if (!empty($tunes->spacing->$side)) {
				/** @var string */
				$styles["padding-$side"] =  preg_replace('/[<>]/', '', $tunes->spacing->$side);
			}
		}

		return $styles;
	}

	/**
	 * Return a style attribute for the wrapping block element.
	 *
	 * @param object{
	 *		spacing?: object{
	 *			top?: string,
	 *			right?: string,
	 *			bottom?: string,
	 *			left?: string
	 *		}} $tunes
	 * @param ?array<non-empty-literal-string, string> $styles
	 * @return string the styles attribute
	 */
	private static function styleAttr(object $tunes, ?array $styles = null): string {
		$styles = array_merge(self::getPaddingStylesFromTunes($tunes), $styles ?? array());

		if (empty($styles)) {
			return '';
		}

		$rules = array();

		foreach ($styles as $key => $value) {
			$value = preg_replace('/[<>]/', '', $value);
			$key = strtolower(preg_replace('/([A-Z])/', '-$1', $key));
			$rules[] = "$key: $value;";
		}

		return 'style="' . join(' ', $rules) . '"';
	}
}
