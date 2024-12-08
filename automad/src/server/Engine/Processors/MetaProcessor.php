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
 * Copyright (c) 2024 by Marc Anton Dahmen
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
use Automad\Core\I18n;
use Automad\Core\Parse;
use Automad\Core\Str;
use Automad\Engine\Document\Head;
use Automad\Models\Page;
use Automad\Models\Shared;
use Automad\System\Fields;
use Automad\System\Server;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The MetaProcessor class handles adding and creating open-graph information to a page header.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class MetaProcessor {
	const IMAGE_FONT_BOLD = AM_BASE_DIR . '/automad/dist/fonts/open-graph/Inter_700Bold.ttf';
	const IMAGE_FONT_REGULAR = AM_BASE_DIR . '/automad/dist/fonts/open-graph/Inter_500Medium.ttf';
	const IMAGE_LOGO = AM_BASE_DIR . '/shared/open-graph-logo.png';

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

		$meta = '<meta name="Generator" content="Automad ' . App::VERSION . '">';
		$meta .= '<link rel="canonical" href="' . $base . AM_REQUEST . '" />';

		if (AM_FEED_ENABLED) {
			$sitename = $this->Shared->get(Fields::SITENAME);
			$meta .= '<link rel="alternate" type="application/rss+xml" title="' . $sitename . ' | RSS" href="' . $base . AM_FEED_URL . '">';
		}

		if (AM_I18N_ENABLED) {
			$lang = I18n::getLanguageFromUrl(AM_REQUEST);
			$meta .= '<link rel="alternate" hreflang="' . $lang . '" href="' . $base . AM_REQUEST . '" />';
		}

		$html = Head::prepend($html, $meta);

		$content = array_merge(
			array(
				'title' => $this->Page->get(Fields::TITLE) . ' | ' . $this->Shared->get(Fields::SITENAME),
				'description' => Str::shorten(Str::stripTags(Str::findFirstParagraph($html)), 150)
			),
			array_filter(
				array(
					'title' => $this->Page->get(Fields::META_TITLE),
					'description' => $this->Page->get(Fields::META_DESCRIPTION)
				),
				'strlen'
			)
		);

		$html = $this->addIfNotExisting('og:image', $this->createOpenGraphImage(), $html);
		$html = $this->addIfNotExisting('og:url', Server::getHost() . Server::getBaseUrl() . $this->Page->url, $html);
		$html = $this->addIfNotExisting('og:type', 'website', $html);
		$html = $this->addIfNotExisting('og:title', $content['title'], $html);
		$html = $this->addIfNotExisting('og:description', $content['description'], $html);
		$html = $this->addIfNotExisting('twitter:card', 'summary_large_image', $html);

		return $html;
	}

	/**
	 * Add a missing meta tag if not existing.
	 *
	 * @param string $property
	 * @param string $content
	 * @param string $html
	 * @return string the updated HTML
	 */
	private function addIfNotExisting(string $property, string $content, string $html): string {
		if ($this->hasProperty($property, $html)) {
			return $html;
		}

		return Head::append($html, '<meta property="' . $property . '" content="' . $content . '">');
	}

	/**
	 * Create a dynamic open-graph image.
	 *
	 * @return string Return the image URL
	 */
	private function createOpenGraphImage(): string {
		$title = $this->Page->get(Fields::TITLE);
		$sitename = $this->Shared->get(Fields::SITENAME);
		$hashItems = array(Server::getHost(), AM_REQUEST, $title, $sitename);

		if (is_readable(MetaProcessor::IMAGE_LOGO)) {
			$hashItems[] = filemtime(MetaProcessor::IMAGE_LOGO);
		}

		$hash = hash('sha256', join(':', $hashItems));
		$dir = Cache::DIR_IMAGES;
		$baseDir = AM_BASE_DIR;
		$baseUrl = Server::getHost() . Server::getBaseUrl();
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

		$rgbTextPrimary = Parse::csv(AM_OG_IMG_COLOR_TEXT_PRIMARY);
		$rgbTextSecondary = Parse::csv(AM_OG_IMG_COLOR_TEXT_SECONDARY);
		$rgbBackground = Parse::csv(AM_OG_IMG_COLOR_BACKGROUND);

		$colorTextPrimary = imagecolorallocate($image, $rgbTextPrimary[0] ?? 0, $rgbTextPrimary[1] ?? 0, $rgbTextPrimary[2] ?? 0);
		$colorTextSecondary = imagecolorallocate($image, $rgbTextSecondary[0] ?? 0, $rgbTextSecondary[1] ?? 0, $rgbTextSecondary[2] ?? 0);
		$colorBackground = imagecolorallocate($image, $rgbBackground[0] ?? 0, $rgbBackground[1] ?? 0, $rgbBackground[2] ?? 0);

		imagefill($image, 0, 0, $colorBackground);

		$maxTitleLength = 100;
		$lineLength = 21;
		$shortened = '';
		$multiline = '';
		$lineCount = 0;

		while ($lineCount == 0 || $lineCount > 4) {
			$shortened = Str::shorten($title, $maxTitleLength);
			$multiline = wordwrap($shortened, $lineLength, "\n", true);
			$lineCount = preg_match_all('/\n/', $multiline) + 1;
			$maxTitleLength--;
		}

		$fontSizeTitle = $lineCount > 3 ? 45 : 54;
		$titleSpace = $lineCount > 3 ? 105 : 125;

		imagefttext(
			$image,
			25,
			0,
			$padding,
			$padding + 25,
			$colorTextPrimary,
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
			$colorTextPrimary,
			MetaProcessor::IMAGE_FONT_BOLD,
			$multiline,
			array('linespacing' => 1.05)
		);

		imagefttext(
			$image,
			25,
			0,
			$padding,
			$height - $padding - 5,
			$colorTextSecondary,
			MetaProcessor::IMAGE_FONT_REGULAR,
			'☀ ' . Str::shorten(preg_replace('#^https?://#', '', Server::getHost()), 80),
			array('linespacing' => 1.0)
		);

		if (is_readable(MetaProcessor::IMAGE_LOGO)) {
			$logo = imagecreatefrompng(MetaProcessor::IMAGE_LOGO);

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

		imagepng($image, $file);

		return $url;
	}

	/**
	 * Test if a given open-graph meta tag already exists in the given HTML.
	 *
	 * @param string $property
	 * @param string $html
	 * @return bool true if the meta tag already exists
	 */
	private function hasProperty(string $property, string $html): bool {
		return preg_match('/\<meta\b[^>]+property="' . preg_quote($property) . '"/s', $html) === 1;
	}
}
