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
use Automad\Engine\Document\Body;
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
		$output = $this->addDebugIndicator($output);
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
	 * Whenever debugging is enabled, add a debug indicator to the rendered output.
	 *
	 * @param string $output
	 * @return string
	 */
	private function addDebugIndicator(string $output): string {
		if (!AM_DEBUG_ENABLED) {
			return $output;
		}

		$dashboard = AM_PAGE_DASHBOARD;

		$html = <<< HTML
		<a 
			href="$dashboard/system?section=debug"
			title="Debugging is currently enabled and can be disabled in the system settings"
			style="
				position: fixed; 
				z-index: 9000;
				display: flex; 
				justify-content: center;
				align-items: center;
				bottom: 25px; 
				left: 25px; 
				width: 48px;
				height: 48px;
				border-radius: 100%;
				color: hsl(220 9 96);
				background-color: hsl(220 9 6 / 0.48);
				border: 1px solid hsl(220 9 96 / 0.14);
				backdrop-filter: blur(9px);
				box-shadow:
					0 5px 20px rgba(0, 0, 0, 0.12),
					0 22px 40px -10px rgba(0, 0, 0, 0.45);
			"
		>
			<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
				<path d="M4.978.855a.5.5 0 1 0-.956.29l.41 1.352A5 5 0 0 0 3 6h10a5 5 0 0 0-1.432-3.503l.41-1.352a.5.5 0 1 0-.956-.29l-.291.956A5 5 0 0 0 8 1a5 5 0 0 0-2.731.811l-.29-.956z"/>
				<path d="M13 6v1H8.5v8.975A5 5 0 0 0 13 11h.5a.5.5 0 0 1 .5.5v.5a.5.5 0 1 0 1 0v-.5a1.5 1.5 0 0 0-1.5-1.5H13V9h1.5a.5.5 0 0 0 0-1H13V7h.5A1.5 1.5 0 0 0 15 5.5V5a.5.5 0 0 0-1 0v.5a.5.5 0 0 1-.5.5zm-5.5 9.975V7H3V6h-.5a.5.5 0 0 1-.5-.5V5a.5.5 0 0 0-1 0v.5A1.5 1.5 0 0 0 2.5 7H3v1H1.5a.5.5 0 0 0 0 1H3v1h-.5A1.5 1.5 0 0 0 1 11.5v.5a.5.5 0 1 0 1 0v-.5a.5.5 0 0 1 .5-.5H3a5 5 0 0 0 4.5 4.975"/>
			</svg>
		</a>
		HTML;

		return Body::append($output, $html);
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
