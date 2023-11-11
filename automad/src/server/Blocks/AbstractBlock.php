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
 * Copyright (c) 2021-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Blocks;

use Automad\Core\Automad;
use Automad\Core\Image;
use Automad\Core\RemoteFile;
use Automad\Core\Resolve;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The abstract base block.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
abstract class AbstractBlock {
	/**
	 * Render a paragraph block.
	 *
	 * @param object{id: string, data: object, tunes: object} $block
	 * @param Automad $Automad
	 * @return string the rendered HTML
	 */
	abstract public static function render(object $block, Automad $Automad): string;

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
	protected static function attr(object $tunes, array $classes = array(), ?array $styles = null): string {
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
	protected static function classAttr(object $tunes, array $custom = array()): string {
		$classes = array_merge(array('am-block'), $custom);

		if (!empty($tunes->className)) {
			$classes[] = preg_replace('/[<>]/', '', $tunes->className);
		}

		return 'class="' . join(' ', $classes) . '"';
	}

	/**
	 * Return a pair of two images, the actual cached image and the tiny preload-background.
	 * The specified $file can either be a remote URL or a local path.
	 *
	 * @param string $file
	 * @param Automad $Automad
	 * @return object{image: string, preload: string}
	 */
	protected static function getPreloadableImage(string $file, Automad $Automad): object {
		if (preg_match('/\:\/\//is', $file)) {
			$RemoteFile = new RemoteFile($file);
			$file = $RemoteFile->getLocalCopy();
		} else {
			$file = Resolve::filePath($Automad->Context->get()->path, $file);
		}

		preg_match('/(\/[\w\.\-\/]+(?:jpg|jpeg|gif|png|webp))(\?(\d+)x(\d+))?/is', $file, $matches);

		$file = $matches[1];
		$width = $matches[3] ?? 0;
		$height = $matches[4] ?? 0;

		$Image = new Image($file, $width, $height, true);
		$Preload = new Image(AM_BASE_DIR . $Image->file, 20);

		return (object) array('image' => AM_BASE_URL . $Image->file, 'preload' => AM_BASE_URL . $Preload->file);
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
	protected static function styleAttr(object $tunes, ?array $styles = null): string {
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
}
