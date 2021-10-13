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

use Automad\Core\FileUtils;
use Automad\Core\Image;
use Automad\Core\Str;
use Automad\UI\Utils\FileSystem;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file card component.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class File {
	/**
	 * Render a file card.
	 *
	 * @param string $file
	 * @param string $id
	 * @return string The HTML of the card
	 */
	public static function render(string $file, string $id) {
		$data = (object) self::getFileData($file);
		$preview = self::getPreview($file);
		$jsonData = json_encode($data);
		$title = basename($file);
		$caption = Str::shorten(htmlspecialchars_decode($data->caption), 100);
		$mTime = date('M j, Y H:i', filemtime($file));
		$clipboard = Str::stripStart($file, AM_BASE_DIR);
		$basename = basename($file);
		$resize = '';
		$Text = Text::getObject();

		if ($caption) {
			$caption = <<< HTML
				<div class="uk-text-small uk-text-truncate uk-hidden-small">
					<i class="uk-icon-comment-o uk-icon-justify"></i>&nbsp;
					$caption
				</div>
			HTML;
		}

		if (FileUtils::fileIsImage($file)) {
			$resize = <<< HTML
				<li>
					<a href="#am-copy-resized-modal"
					data-uk-modal
					>
						<i class="uk-icon-crop"></i>&nbsp;
						$Text->btn_copy_resized
					</a>
				</li>
			HTML;
		}

		return <<< HTML
			<div 
			id="$id" 
			class="uk-panel uk-panel-box" 
			data-am-file-info='$jsonData'
			>
				$preview
				<div 
				class="uk-panel-title" 
				title="$title" 
				>
					$title
				</div>
				$caption
				<div class="uk-text-small uk-text-truncate uk-hidden-small">
					<i class="uk-icon-calendar-o uk-icon-justify"></i>&nbsp;
					$mTime
				</div>
				<div class="am-panel-bottom">
					<div class="am-panel-bottom-left">
						<div data-uk-dropdown="{mode:'click'}">
							<div class="am-panel-bottom-link">
								<i class="uk-icon-ellipsis-v"></i>
							</div>
							<div class="uk-dropdown uk-dropdown-small">
								<ul class="uk-nav uk-nav-dropdown">
									<li>
										<a 
										href="#am-edit-file-info-modal" 
										data-uk-modal 
										>
											<i class="uk-icon-pencil"></i>&nbsp;
											$Text->btn_edit_file_info
										</a>
									</li>
									$resize
									<li>
										<a href="#" data-am-clipboard="$clipboard">
											<i class="uk-icon-link"></i>&nbsp;
											$Text->btn_copy_url_clipboard
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="am-panel-bottom-right">
						<label 
						class="am-toggle-checkbox am-panel-bottom-link" 
						data-am-toggle="#$id"
						>
							<input type="checkbox" name="delete[]" value="$basename" />
						</label>
					</div>
				</div>
			</div>
		HTML;
	}

	/**
	 * Get file data and resized images.
	 *
	 * @param string $file
	 * @return array The file data array
	 */
	private static function getFileData(string $file) {
		$data = array(
			'img' => false,
			'filename' => basename($file),
			'caption' => htmlspecialchars(FileUtils::caption($file)),
			'extension' => htmlspecialchars(FileSystem::getExtension($file)),
			'download' => AM_BASE_URL . Str::stripStart($file, AM_BASE_DIR)
		);

		if (FileUtils::fileIsImage($file)) {
			$imgModal = new Image($file, 1600, 1200, false);

			$data['img'] = array(
				'src' => AM_BASE_URL . $imgModal->file,
				'width' => $imgModal->width,
				'height' => $imgModal->height,
				'originalWidth' => $imgModal->originalWidth,
				'originalHeight' => $imgModal->originalHeight
			);
		}

		return $data;
	}

	/**
	 * Get file preview.
	 *
	 * @param string $file
	 * @return string The generated HTML of the preview
	 */
	private static function getPreview(string $file) {
		if (FileUtils::fileIsImage($file)) {
			$imgPanel = new Image($file, 320, 240, true);
			$url = AM_BASE_URL . $imgPanel->file;

			$preview = <<< HTML
				<img src="$url" width="$imgPanel->width" height="$imgPanel->height" />
				<div class="uk-panel-badge uk-badge"> 
					$imgPanel->originalWidth 
					<i class="uk-icon-times"></i>
					$imgPanel->originalHeight
				</div>
			HTML;
		} else {
			$preview = '<i class="uk-icon-file-o am-files-icon-' . FileSystem::getExtension($file) . '"></i>';
		}

		return <<< HTML
			<a 
			href="#am-edit-file-info-modal" 
			class="uk-panel-teaser uk-display-block" 
			data-uk-modal
			>
				<div class="am-cover-4by3">
					$preview
				</div>
			</a>
		HTML;
	}
}
