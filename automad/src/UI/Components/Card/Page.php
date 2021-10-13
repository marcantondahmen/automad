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

namespace Automad\UI\Components\Card;

use Automad\Core\FileSystem;
use Automad\Core\Image;
use Automad\Core\Page as CorePage;
use Automad\Core\Str;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page card component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Page {
	/**
	 * Render a page card.
	 *
	 * @param CorePage $Page
	 * @return string The HTML of the card
	 */
	public static function render(CorePage $Page) {
		$link = '?view=Page&url=' . urlencode($Page->get(AM_KEY_ORIG_URL));

		$path = AM_BASE_DIR . AM_DIR_PAGES . $Page->path;
		$images = FileSystem::globGrep($path . '*.*', '/(jpg|jpeg|png|gif)$/i');

		if (!empty($images)) {
			$preview = self::layout($images);
		} else {
			$preview = '<i class="uk-icon-file-text-o"></i>';
		}

		$pageTitle = htmlspecialchars($Page->get(AM_KEY_TITLE));
		$pageMTime = Str::dateFormat($Page->getMtime(), 'j. M Y');
		$pageUrl = AM_BASE_INDEX . $Page->url;
		$Text = Text::getObject();

		if ($Page->private) {
			$badge = '<span class="uk-panel-badge uk-badge">' .
					 '<i class="uk-icon-lock"></i>&nbsp;&nbsp;' .
					 $Text->page_private .
					 '</span>';
		} else {
			$badge = '';
		}

		return <<< HTML
			<div class="uk-panel uk-panel-box">
				<a 
				href="$link" 
				class="uk-panel-teaser uk-display-block"
				>
					<div class="am-cover-4by3">
						$preview
					</div>
				</a>
				<div class="uk-panel-title">$pageTitle</div>
				<div class="uk-text-small">$pageMTime</div>
				<div class="am-panel-bottom">
					<div class="am-panel-bottom-left">
						<a 
						href="$link" 
						title="$Text->btn_edit_page"  
						class="am-panel-bottom-link"
						data-uk-tooltip
						>
							<i class="uk-icon-file-text-o"></i>
						</a>
						<a 
						href="$pageUrl" 
						title="$Text->btn_inpage_edit" 
						class="am-panel-bottom-link"
						data-uk-tooltip
						>
							<i class="uk-icon-bookmark-o"></i>
						</a>
					</div>
				</div>
				$badge
			</div>
		HTML;
	}

	/**
	 * Layout preview images.
	 *
	 * @param array $images
	 * @return string The generated HTML
	 */
	private static function layout(array $images) {
		$count = count($images);
		$wFull = 320;
		$hFull = 240;

		$html = '<ul class="uk-grid uk-grid-collapse">';

		if ($count == 1) {
			$html .= self::thumbnail($images[0], $wFull, $hFull, '1-1');
		}

		if ($count == 2) {
			$html .= self::thumbnail($images[0], $wFull/2, $hFull, '1-2');
			$html .= self::thumbnail($images[1], $wFull/2, $hFull, '1-2');
		}

		if ($count == 3) {
			$html .= self::thumbnail($images[0], $wFull, $hFull/2, '1-1');
			$html .= self::thumbnail($images[1], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[2], $wFull/2, $hFull/2, '1-2');
		}

		if ($count == 4) {
			$html .= self::thumbnail($images[0], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[1], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[2], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[3], $wFull/2, $hFull/2, '1-2');
		}

		if ($count == 5) {
			$html .= self::thumbnail($images[0], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[1], $wFull/2, $hFull/2, '1-2');
			$html .= self::thumbnail($images[2], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[3], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[4], $wFull/3, $hFull/2, '1-3');
		}

		if ($count >= 6) {
			$html .= self::thumbnail($images[0], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[1], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[2], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[3], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[4], $wFull/3, $hFull/2, '1-3');
			$html .= self::thumbnail($images[5], $wFull/3, $hFull/2, '1-3');
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Generate thumbnail for page grid.
	 *
	 * @param string $file
	 * @param float $w
	 * @param float $h
	 * @param string $gridW (uk-width-* suffix)
	 * @return string The generated markup
	 */
	private static function thumbnail(string $file, float $w, float $h, string $gridW) {
		$img = new Image($file, $w, $h, true);

		return '<li class="uk-width-' . $gridW . '"><img src="' . AM_BASE_URL . $img->file . '" /></li>';
	}
}
