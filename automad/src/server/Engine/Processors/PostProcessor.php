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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Processors;

use Automad\Admin\InPage;
use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\FileUtils;
use Automad\Core\I18n;
use Automad\Core\Image;
use Automad\Engine\Collections\AssetCollection;
use Automad\Engine\Document\Head;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The post-processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PostProcessor {
	/**
	 * The Automad instance.
	 */
	private Automad $Automad;

	/**
	 * The InPage instance.
	 */
	private InPage $InPage;

	/**
	 * The post-processor constructor.
	 *
	 * @param Automad $Automad
	 * @param InPage $InPage
	 */
	public function __construct(
		Automad $Automad,
		InPage $InPage
	) {
		$this->Automad = $Automad;
		$this->InPage = $InPage;
	}

	/**
	 * Run all required post-process steps.
	 *
	 * @param string $output
	 * @return string the final output
	 */
	public function process(string $output): string {
		$MetaProcessor = new MetaProcessor($this->Automad);
		$MailAddressProcessor = new MailAddressProcessor();
		$SyntaxHighlightingProcessor = new SyntaxHighlightingProcessor($this->Automad);
		$ConsentProcessor = new ConsentProcessor($this->Automad);
		$CustomizationProcessor = new CustomizationProcessor($this->Automad);

		$output = $this->InPage->createUI($output);
		$output = $this->createExtensionAssetTags($output);
		$output = $this->setLanguage($output);
		$output = $this->resizeImages($output);
		$output = Blocks::injectAssets($output);
		$output = $MailAddressProcessor->obfuscate($output);
		$output = $SyntaxHighlightingProcessor->addAssets($output);
		$output = $CustomizationProcessor->addCustomizations($output);
		$output = $ConsentProcessor->addMetaTags($output);
		$output = $MetaProcessor->addMetaTags($output);
		$output = $ConsentProcessor->encodeScript($output);
		$output = $this->addCacheBustingTimestamps($output);
		$output = URLProcessor::resolveUrls($output, 'absoluteUrlToRoot');

		return $output;
	}

	/**
	 * Find all locally hosted assests and append a timestamp in order to avoid serving outdated files.
	 *
	 * @param string $str
	 * @return string the processed output
	 */
	private function addCacheBustingTimestamps(string $str): string {
		$extensions = implode('|', FileUtils::allowedFileTypes());

		return preg_replace_callback(
			'#(?<=")/[/\w\-\.]+\.(?:' . $extensions . ')(?=")#is',
			function ($matches) {
				$file = AM_BASE_DIR . $matches[0];

				if (strpos($file, AM_DIR_CACHE . '/') !== false || !is_readable($file)) {
					return $matches[0];
				}

				return $matches[0] . '?m=' . intval(filemtime($file));
			},
			$str
		) ?? '';
	}

	/**
	 * Create the HTML tags for each file in the asset collection and prepend them to the closing </head> tag.
	 *
	 * @param string $str
	 * @return string The processed string
	 */
	private function createExtensionAssetTags(string $str): string {
		$assets = AssetCollection::get();
		Debug::log($assets, 'Assets');

		$html = '';

		if (isset($assets['.css'])) {
			foreach ($assets['.css'] as $file) {
				$html .= '<link href="' . $file . '" rel="stylesheet">';
				Debug::log($file, 'Created tag for');
			}
		}

		if (isset($assets['.js'])) {
			foreach ($assets['.js'] as $file) {
				$html .= '<script type="text/javascript" src="' . $file . '"></script>';
				Debug::log($file, 'Created tag for');
			}
		}

		return Head::append($str, $html);
	}

	/**
	 * Resize any image in the output in case it has a specified size as query string like
	 * for example "/shared/image.jpg?200x200".
	 *
	 * @param string $str
	 * @return string The processed string
	 */
	private function resizeImages(string $str): string {
		return preg_replace_callback(
			'/(\/[\w\.\-\/]+(?:' . join('|', FileSystem::FILE_TYPES_IMAGE) . '))\?(\d+)x(\d+)/is',
			function ($match) {
				$file = AM_BASE_DIR . $match[1];

				if (is_readable($file)) {
					$image = new Image($file, floatval($match[2]), floatval($match[3]), true);

					return $image->file;
				}

				return $match[0];
			},
			$str
		) ?? '';
	}

	/**
	 * Set the lang attribute fot the <html> element.
	 *
	 * @param string $output
	 * @return string the updated output
	 */
	private function setLanguage(string $output): string {
		$lang = $this->Automad->Context->get()->get(Fields::LANG_CUSTOM);

		if (AM_I18N_ENABLED) {
			$lang = I18n::getLanguageFromUrl(AM_REQUEST);
		}

		if (!$lang) {
			$lang = 'en';
		}

		// Remove existing lang attribute.
		$output = preg_replace('/^(\s*(?:<[^>]+>\s*)?<html\s[^>]*)(lang="\w+")([^>]*>)/i', '$1$3', $output) ?? '';

		return preg_replace('/^(\s*(?:<[^>]+>\s*)?<html)/', '$1 lang="' . $lang . '"', $output) ?? '';
	}
}
