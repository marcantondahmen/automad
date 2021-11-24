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

namespace Automad\Engine\Processors;

use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Core\Debug;
use Automad\Core\FileUtils;
use Automad\Core\Image;
use Automad\Engine\Collections\AssetCollection;
use Automad\System\Server;
use Automad\UI\InPage;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The post-processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PostProcessor {
	/**
	 * The Automad instance.
	 */
	private $Automad;

	/**
	 * A boolean variable that contains the headless state
	 */
	private $headless;

	/**
	 * The InPage instance.
	 */
	private $InPage;

	/**
	 * The post-processor constructor.
	 *
	 * @param Automad $Automad
	 * @param InPage $InPage
	 * @param bool $headless
	 */
	public function __construct(
		Automad $Automad,
		InPage $InPage,
		bool $headless
	) {
		$this->Automad = $Automad;
		$this->InPage = $InPage;
		$this->headless = $headless;
	}

	/**
	 * Run all required post-process steps.
	 *
	 * @param string $output
	 * @return string the final output
	 */
	public function process(string $output) {
		$output = $this->createExtensionAssetTags($output);
		$output = $this->addMetaTags($output);
		$output = $this->obfuscateEmails($output);
		$output = $this->resizeImages($output);
		$output = Blocks::injectAssets($output);
		$output = $this->addCacheBustingTimestamps($output);
		$output = URLProcessor::resolveUrls($output, 'absoluteUrlToRoot');
		$output = $this->InPage->createUI($output);

		return $output;
	}

	/**
	 * Find all locally hosted assests and append a timestamp in order to avoid serving outdated files.
	 *
	 * @param string $str
	 * @return string the processed output
	 */
	private function addCacheBustingTimestamps(string $str) {
		$extensions = implode('|', FileUtils::allowedFileTypes());

		return preg_replace_callback(
			'#(?<=")/[/\w\-\.]+\.(?:' . $extensions . ')(?=")#is',
			function ($matches) {
				$file = AM_BASE_DIR . $matches[0];

				if (strpos($file, AM_DIR_CACHE . '/') !== false || !is_readable($file)) {
					return $matches[0];
				}

				return $matches[0] . '?m=' . filemtime($file);
			},
			$str
		);
	}

	/**
	 * Add meta tags to the head of $str.
	 *
	 * @param string $str
	 * @return string The meta tag
	 */
	private function addMetaTags(string $str) {
		$meta = '<meta name="Generator" content="Automad ' . AM_VERSION . '">';

		if (AM_FEED_ENABLED) {
			$sitename = $this->Automad->Shared->get(AM_KEY_SITENAME);
			$meta .= '<link rel="alternate" type="application/rss+xml" title="' . $sitename . ' | RSS" href="' . Server::url() . AM_BASE_INDEX . AM_FEED_URL . '">';
		}

		return str_replace('<head>', '<head>' . $meta, $str);
	}

	/**
	 * Create the HTML tags for each file in the asset collection and prepend them to the closing </head> tag.
	 *
	 * @param string $str
	 * @return string The processed string
	 */
	private function createExtensionAssetTags(string $str) {
		$assets = AssetCollection::get();
		Debug::log($assets, 'Assets');

		$html = '';

		if (isset($assets['.css'])) {
			foreach ($assets['.css'] as $file) {
				$html .= '<link rel="stylesheet" href="' . $file . '" />';
				Debug::log($file, 'Created tag for');
			}
		}

		if (isset($assets['.js'])) {
			foreach ($assets['.js'] as $file) {
				$html .= '<script type="text/javascript" src="' . $file . '"></script>';
				Debug::log($file, 'Created tag for');
			}
		}

		// Prepend all items ($html) to the closing </head> tag.
		return str_replace('</head>', $html . '</head>', $str);
	}

	/**
	 * Obfuscate all stand-alone eMail addresses matched in $str.
	 * Addresses in links are ignored. In headless mode, obfuscation is disabled.
	 *
	 * @param string $str
	 * @return string The processed string
	 */
	private function obfuscateEmails(string $str) {
		if ($this->headless) {
			return $str;
		}

		$regexEmail = '[\w\.\+\-]+@[\w\-\.]+\.[a-zA-Z]{2,}';

		// The following regex matches all email links or just an email address.
		// That way it is possible to separate email addresses
		// within <a></a> tags from stand-alone ones.
		$regex = '/(<a\s[^>]*href="mailto.+?<\/a>|(?P<email>' . $regexEmail . '))/is';

		return preg_replace_callback($regex, function ($matches) {
			// Only stand-alone addresses are obfuscated.
			if (!empty($matches['email'])) {
				Debug::log($matches['email'], 'Obfuscating');

				$html = "<a href='#' " .
						"onclick='this.href=`mailto:` + this.innerHTML.split(``).reverse().join(``)' " .
						"style='unicode-bidi:bidi-override;direction:rtl'>";
				$html .= strrev($matches['email']);
				$html .= '</a>&#x200E;';

				return $html;
			} else {
				Debug::log($matches[0], 'Ignoring');

				return $matches[0];
			}
		}, $str);
	}

	/**
	 * Resize any image in the output in case it has a specified size as query string like
	 * for example "/shared/image.jpg?200x200".
	 *
	 * @param string $str
	 * @return string The processed string
	 */
	private function resizeImages(string $str) {
		return preg_replace_callback(
			'/(\/[\w\.\-\/]+(?:jpg|jpeg|gif|png))\?(\d+)x(\d+)/is',
			function ($match) {
				$file = AM_BASE_DIR . $match[1];

				if (is_readable($file)) {
					$image = new Image($file, $match[2], $match[3], true);

					return $image->file;
				}

				return $match[0];
			},
			$str
		);
	}
}
