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

namespace Automad\UI\Components\Layout;

use Automad\UI\Components\Form\SelectImage as FormSelectImage;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The image selection layout component that is the actual content of the image selection modal.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class SelectImage {
	/**
	 * Render the selection layout.
	 *
	 * @param array $pageImages
	 * @param array $sharedImages
	 * @return string the rendered selection layout
	 */
	public static function render(array $pageImages, array $sharedImages) {
		$fn = function ($expression) {
			return $expression;
		};

		return <<< HTML
			<div class="am-form-input-button uk-flex">
				<input 
				class="uk-form-controls uk-width-1-1" 
				type="text" 
				name="imageUrl"
				placeholder="URL"
				>
				<button type="button" class="uk-button uk-text-nowrap">
					<i class="uk-icon-link"></i>&nbsp;
					{$fn(Text::get('btn_link'))}
				</button>
			</div>
			<hr>
			<div class="am-select-image-resize">
				<p>{$fn(Text::get('image_options'))}</p>
				<ul class="uk-grid uk-grid-width-1-2">
					<li>
						<input 
						class="uk-form-controls uk-width-1-1" 
						type="number" 
						name="width"
						step="10"
						placeholder="{$fn(Text::get('image_width_px'))}"
						>
					</li>
					<li>
						<input 
						class="uk-form-controls uk-width-1-1" 
						type="number" 
						name="height"
						step="10"
						placeholder="{$fn(Text::get('image_height_px'))}"
						>
					</li>
				</ul>
			</div>
			{$fn(FormSelectImage::render($pageImages, Text::get('images_page'), true))}
			{$fn(FormSelectImage::render($sharedImages, Text::get('images_shared')))}
		HTML;
	}
}
