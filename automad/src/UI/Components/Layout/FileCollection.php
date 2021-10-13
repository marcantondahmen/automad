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

use Automad\UI\Components\Grid\Files;
use Automad\UI\Components\Modal\CopyResized;
use Automad\UI\Components\Modal\EditFileInfo;
use Automad\UI\Components\Modal\Import;
use Automad\UI\Components\Modal\Upload;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file collection layout.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileCollection {
	/**
	 * Render the file collection layout.
	 *
	 * @param array $files
	 * @param string $url
	 * @param string $modalTitle
	 * @return string the rendered file collection layout.
	 */
	public static function render(array $files, string $url, string $modalTitle) {
		$fn = function ($expression) {
			return $expression;
		};

		if ($files) {
			$html = <<< HTML
				<ul class="uk-grid">
					<li class="uk-width-2-3 uk-width-medium-1-1">
						<div class="uk-button-group">
							<a 
							href="#am-upload-modal" 
							class="uk-button uk-button-success" 
							data-uk-modal="{bgclose: false, keyboard: false}"
							>
								<i class="uk-icon-upload"></i>&nbsp;
								{$fn(Text::get('btn_upload'))}
							</a>	
							<a 
							href="#am-import-modal" 
							class="uk-button uk-button-success" 
							data-uk-modal
							>
								<span class="uk-hidden-small"><i class="uk-icon-cloud-download"></i>&nbsp;</span>
								{$fn(Text::get('btn_import'))}
							</a>
						</div>&nbsp;
						<button 
						class="uk-button uk-button-link uk-hidden-small" 
						data-am-submit="FileCollection::edit"
						>
							<i class="uk-icon-remove"></i>&nbsp;
							{$fn(Text::get('btn_remove_selected'))}
						</button>
					</li>
					<li class="uk-width-1-3 uk-visible-small">
						<div class="am-icon-buttons uk-text-right">
							<button 
							class="uk-button" 
							title="{$fn(Text::get('btn_remove_selected'))}"
							data-am-submit="FileCollection::edit"
							data-uk-tooltip
							>
								<i class="uk-icon-remove"></i>
							</button>
						</div>
					</li>
				</ul>
				{$fn(Files::render($files))}
				{$fn(CopyResized::render($url))}
				{$fn(EditFileInfo::render($modalTitle, $url))}
			HTML;
		} else {
			$html = <<< HTML
				<div class="uk-button-group">
					<a 
					href="#am-upload-modal" 
					class="uk-button uk-button-success uk-button-large" 
					data-uk-modal="{bgclose: false, keyboard: false}"
					>
						<i class="uk-icon-upload"></i>&nbsp;&nbsp;{$fn(Text::get('btn_upload'))}
					</a>
					<a 
					href="#am-import-modal" 
					class="uk-button uk-button-success uk-button-large" 
					data-uk-modal
					>
						<i class="uk-icon-cloud-download"></i>&nbsp;
						{$fn(Text::get('btn_import'))}
					</a>
				</div>
			HTML;
		}

		$html .= Upload::render($url);
		$html .= Import::render($url);

		return $html;
	}
}
