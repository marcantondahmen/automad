<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\GUI\Components\Card;
use Automad\GUI\FileSystem as FileSystem;
use Automad\GUI\Text as Text;
use Automad\Core as Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The file card component. 
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class File {


	/**
	 *	Get file preview.
	 *	
	 *	@param string $file
	 *	@return string The generated HTML of the preview
	 */

	private static function getPreview($file) {

		if (Core\Parse::fileIsImage($file)) {

			$imgPanel = new Core\Image($file, 320, 240, true);
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


	/**
	 *	Get file data and resized images.
	 *	
	 *	@param string $file
	 *	@return array The file data array
	 */

	private static function getFileData($file) {

		$data = array(
			'img' => false, 
			'filename' => basename($file), 
			'caption' => htmlspecialchars(Core\Parse::caption($file)), 
			'extension' => htmlspecialchars(FileSystem::getExtension($file)),
			'download' => AM_BASE_URL . Core\Str::stripStart($file, AM_BASE_DIR) 
		);

		if (Core\Parse::fileIsImage($file)) { 
			
			$imgModal = new Core\Image($file, 1600, 1200, false);
	
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
	 *	Render a file card.
	 *	
	 *	@param string $file
	 *	@param string $id
	 *	@return string The HTML of the card
	 */

	public static function render($file, $id) {

		$data = (object) self::getFileData($file);	
		$preview = self::getPreview($file);
		$jsonData = json_encode($data);
		$title = basename($file);
		$caption = Core\Str::shorten(htmlspecialchars_decode($data->caption), 100);
		$mTime = date('M j, Y H:i', filemtime($file));
		$clipboard = Core\Str::stripStart($file, AM_BASE_DIR);
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

		if (Core\Parse::fileIsImage($file)) {

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


}