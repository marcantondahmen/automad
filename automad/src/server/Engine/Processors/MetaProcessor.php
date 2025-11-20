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
 * Copyright (c) 2024-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Processors;

use Automad\App;
use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\I18n;
use Automad\Core\Str;
use Automad\Engine\Document\Head;
use Automad\Models\Page;
use Automad\Models\Shared;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The MetaProcessor class handles adding and creating open-graph information to a page header.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class MetaProcessor {
	const IMAGE_COLOR_BACKGROUND = '#0e0f11';
	const IMAGE_COLOR_TEXT = '#f4f4f6';
	const IMAGE_FONT_BOLD = AM_BASE_DIR . '/automad/dist/open-graph/Inter_700Bold.ttf';
	const IMAGE_FONT_REGULAR = AM_BASE_DIR . '/automad/dist/open-graph/Inter_500Medium.ttf';
	const IMAGE_LOGO = AM_BASE_DIR . AM_DIR_SHARED . '/open-graph-logo.png';

	/**
	 * The current page model.
	 */
	private Page $Page;

	/**
	 * The shared data model.
	 */
	private Shared $Shared;

	/**
	 * The constructor.
	 *
	 * @param Automad $Automad
	 */
	public function __construct(Automad $Automad) {
		$this->Page = $Automad->Context->get();
		$this->Shared = $Automad->Shared;
	}

	/**
	 * Add missing open-graph meta tags and generate image if needed.
	 *
	 * @param string $html
	 * @return string the HTML with including the added meta tags
	 */
	public function addMetaTags(string $html): string {
		$base = AM_SERVER . AM_BASE_INDEX;
		$content = array_merge(
			array(
				'title' => $this->convertHtml(Str::stripTags($this->Page->get(Fields::TITLE) . ' | ' . $this->Shared->get(Fields::SITENAME))),
				'description' => $this->convertHtml(Str::shorten(Str::stripTags(Str::findFirstParagraph($html)), 150))
			),
			array_filter(
				array(
					'title' => $this->convertHtml(Str::stripTags($this->Page->get(Fields::META_TITLE))),
					'description' => $this->convertHtml(Str::stripTags($this->Page->get(Fields::META_DESCRIPTION)))
				),
				'strlen'
			)
		);

		$html = $this->addIcons($html);

		if ($ogImage = $this->Page->get(Fields::OPEN_GRAPH_IMAGE)) {
			$ogImage = AM_SERVER . AM_BASE_URL . $ogImage;
		} else {
			$ogImage = $this->createOpenGraphImage();
		}

		$html = $this->addMetaTagOnce('name', 'twitter:card', 'summary_large_image', $html);
		$html = $this->addMetaTagOnce('property', 'og:url', $base . AM_REQUEST, $html);
		$html = $this->addMetaTagOnce('property', 'og:type', 'website', $html);
		$html = $this->addMetaTagOnce('property', 'og:image', $ogImage, $html);
		$html = $this->addMetaTagOnce('property', 'og:description', $content['description'], $html);
		$html = $this->addMetaTagOnce('property', 'og:title', $content['title'], $html);
		$html = $this->addMetaTagOnce('name', 'description', $content['description'], $html);
		$html = $this->addMetaTagOnce('http-equiv', 'X-UA-Compatible', 'IE=edge', $html);

		$meta = '<meta name="Generator" content="Automad ' . App::VERSION . '">';
		$meta .= '<link rel="canonical" href="' . $base . AM_REQUEST . '">';

		if (!preg_match('/\<meta[^>]+charset=/', $html)) {
			$meta .= '<meta charset="utf-8">';
		}

		if (!preg_match('/\<title\>/', $html)) {
			$meta .= '<title>' . $content['title'] . '</title>';
		}

		if (AM_FEED_ENABLED) {
			$sitename = Str::stripTags($this->Shared->get(Fields::SITENAME));
			$meta .= '<link rel="alternate" type="application/rss+xml" title="' . $sitename . ' | RSS" href="' . $base . AM_FEED_URL . '">';
		}

		if (AM_I18N_ENABLED) {
			$lang = I18n::getLanguageFromUrl(AM_REQUEST);
			$meta .= '<link rel="alternate" hreflang="' . $lang . '" href="' . $base . AM_REQUEST . '">';
		}

		$html = Head::prepend($html, $meta);

		return $html;
	}

	/**
	 * Add an icon if it is not existing.
	 *
	 * @param string $rel
	 * @param string $file
	 * @param string $html
	 * @param string $extra
	 */
	private function addIconIfExistingOnce(string $rel, string $file, string $html, string $extra = ''): string {
		if (preg_match('/\<link\b[^>]+href="[^"]*' . preg_quote($file) . '[^"]*"/s', $html)) {
			return $html;
		}

		if (!is_readable(AM_BASE_DIR . AM_DIR_SHARED . '/' . $file)) {
			return $html;
		}

		return Head::prepend($html, '<link rel="' . $rel . '" href="' . AM_DIR_SHARED . '/' . $file . '" ' . $extra . '>');
	}

	/**
	 * Add favions, touch icons etc.
	 *
	 * @param string $html
	 * @return string
	 */
	private function addIcons(string $html): string {
		$html = $this->addIconIfExistingOnce('icon', 'favicon.ico', $html, 'sizes="32x32"');
		$html = $this->addIconIfExistingOnce('icon', 'favicon.svg', $html);
		$html = $this->addIconIfExistingOnce('apple-touch-icon', 'apple-touch-icon.png', $html);

		return $html;
	}

	/**
	 * Add a missing meta tag if not existing.
	 *
	 * @param string $key
	 * @param string $name
	 * @param string $content
	 * @param string $html
	 * @return string the updated HTML
	 */
	private function addMetaTagOnce(string $key, string $name, string $content, string $html): string {
		if (preg_match('/\<meta\b[^>]+' . preg_quote($key) . '="' . preg_quote($name) . '"/s', $html) === 1) {
			return $html;
		}

		return Head::prepend($html, '<meta ' . $key . '="' . $name . '" content="' . $content . '">');
	}

	/**
	 * Convert HTML special characters to be used in title and description.
	 *
	 * @param string $html
	 * @return string
	 */
	private function convertHtml(string $html): string {
		return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, null, false);
	}

	/**
	 * Create a dynamic open-graph image.
	 *
	 * @return string Return the image URL
	 */
	private function createOpenGraphImage(): string {
		$title = $this->Page->get(Fields::TITLE);
		$sitename = $this->Shared->get(Fields::SITENAME);
		$baseDir = AM_BASE_DIR;
		$baseUrl = AM_SERVER . AM_BASE_URL;
		$customColorText = $this->Page->get(Fields::CUSTOM_OPEN_GRAPH_IMAGE_COLOR_TEXT);
		$customColorBackground = $this->Page->get(Fields::CUSTOM_OPEN_GRAPH_IMAGE_COLOR_BACKGROUND);
		$hashItems = array($baseUrl, AM_REQUEST, $title, $sitename, $customColorText, $customColorBackground);

		if (is_readable(MetaProcessor::IMAGE_LOGO)) {
			$hashItems[] = filemtime(MetaProcessor::IMAGE_LOGO);
		}

		$hash = hash('sha256', join(':', $hashItems));
		$dir = Cache::DIR_IMAGES;
		$name = "og-$hash.png";

		$url = "$baseUrl$dir/$name";
		$file = "$baseDir$dir/$name";

		if (is_readable($file)) {
			Debug::log($name, 'Using cached image');

			return $url;
		}

		Debug::log($hashItems, 'Generate new image');

		$width = 1280;
		$height = 640;
		$padding = 108;

		$image = imagecreatetruecolor($width, $height);

		if (!$image) {
			return '';
		}

		$rgbText = $this->hexToRgbColor($customColorText, MetaProcessor::IMAGE_COLOR_TEXT);
		$rgbBackground = $this->hexToRgbColor($customColorBackground, MetaProcessor::IMAGE_COLOR_BACKGROUND);

		$colorText = intval(imagecolorallocate($image, $rgbText[0] ?? 0, $rgbText[1] ?? 0, $rgbText[2] ?? 0));
		$colorBackground = intval(imagecolorallocate($image, $rgbBackground[0] ?? 0, $rgbBackground[1] ?? 0, $rgbBackground[2] ?? 0));

		imagefill($image, 0, 0, $colorBackground);

		$maxTitleLength = 100;
		$lineLength = 22;
		$shortened = '';
		$multiline = '';
		$lineCount = 0;

		while ($lineCount == 0 || $lineCount > 4) {
			$shortened = Str::shorten($title, $maxTitleLength);
			$multiline = wordwrap($shortened, $lineLength, "\n", true);
			$lineCount = intval(preg_match_all('/\n/', $multiline)) + 1;
			$maxTitleLength--;
		}

		$fontSizeTitle = $lineCount > 3 ? 43 : 50;
		$titleSpace = $lineCount > 3 ? 105 : 120;

		imagefttext(
			$image,
			25,
			0,
			$padding,
			$padding + 25,
			$colorText,
			MetaProcessor::IMAGE_FONT_BOLD,
			Str::shorten($sitename, 80) . ' —',
			array('linespacing' => 1.0)
		);

		imagefttext(
			$image,
			$fontSizeTitle,
			0,
			$padding - 3,
			$padding + $titleSpace,
			$colorText,
			MetaProcessor::IMAGE_FONT_BOLD,
			$multiline,
			array('linespacing' => 1.175)
		);

		imagefttext(
			$image,
			25,
			0,
			$padding,
			$height - $padding - 5,
			$colorText,
			MetaProcessor::IMAGE_FONT_REGULAR,
			'☀ ' . Str::shorten(preg_replace('#^https?://#', '', $baseUrl) ?? '', 40),
			array('linespacing' => 1.0)
		);

		if (is_readable(MetaProcessor::IMAGE_LOGO)) {
			$logo = imagecreatefrompng(MetaProcessor::IMAGE_LOGO);

			if ($logo) {
				imagecopyresampled(
					$image,
					$logo,
					$width - $padding - imagesx($logo),
					$height - $padding - imagesy($logo),
					0,
					0,
					imagesx($logo),
					imagesy($logo),
					imagesx($logo),
					imagesy($logo)
				);
			}
		}

		FileSystem::makeDir(dirname($file));
		imagepng($image, $file, AM_IMG_PNG_QUALITY);

		return $url;
	}

	/**
	 * Convert a hex color string into an rgb array.
	 *
	 * @param string $hex
	 * @param string $defaultHex
	 * @return array
	 */
	private function hexToRgbColor(string $hex, string $defaultHex): array {
		$format = '#%02x%02x%02x';

		/** @var array */
		$default = sscanf($defaultHex, $format);

		if (!$hex) {
			return $default;
		}

		$rgb = sscanf($hex, $format);

		if (isset($rgb[2])) {
			return $rgb;
		}

		return $default;
	}
}
