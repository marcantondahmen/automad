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
 * Copyright (c) 2021-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\Engine\Processors\URLProcessor;
use Automad\Models\Page;
use Automad\Models\Selection;
use Automad\System\Fields;
use Automad\System\Server;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Feed class handles the rendering of the feed.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Feed {
	/**
	 * The Automad object.
	 */
	private Automad $Automad;

	/**
	 * The included fields.
	 */
	private array $fields = array();

	/**
	 * The constructor.
	 *
	 * @param Automad $Automad
	 * @param array $fields
	 */
	public function __construct(Automad $Automad, array $fields) {
		$this->Automad = $Automad;
		$this->fields = $fields;
	}

	/**
	 * Get the main feed output.
	 *
	 * @return string the rendered XML
	 */
	public function get(): string {
		$fn = $this->fn();
		$Selection = new Selection($this->Automad->getCollection());
		$items = $this->getItems($Selection->getSelection());

		return <<< XML
			<?xml version="1.0" encoding="UTF-8"?>
			<rss 
			version="2.0" 
			xmlns:atom="http://www.w3.org/2005/Atom" 
			xmlns:content="http://purl.org/rss/1.0/modules/content/"
			>
				<channel>
					<title>{$fn($this->Automad->Shared->get(Fields::SITENAME))}</title>
					<link>{$fn(AM_SERVER)}</link>
					<atom:link href="{$fn(AM_SERVER . AM_BASE_INDEX . AM_FEED_URL)}" rel="self" type="application/rss+xml" />
					<description>
						{$fn(Str::stripTags(Str::findFirstParagraph($this->getPageContent($this->Automad->getPage('/')))))}
					</description>
					<generator>https://automad.org</generator>
					<lastBuildDate>{$fn(date(DATE_RSS, Cache::readSiteMTime()))}</lastBuildDate>
					$items
				</channel>
			</rss>
			XML;
	}

	/**
	 * The fn helper that enables rendering expressions in heredoc strings.
	 *
	 * @return callable The function
	 */
	private function fn(): callable {
		return function (mixed $expression): string { return $expression; };
	}

	/**
	 * Get the items output.
	 *
	 * @param array $pages
	 * @return string the rendered XML
	 */
	private function getItems(array $pages): string {
		$fn = $this->fn();
		$output = '';

		foreach ($pages as $Page) {
			$this->Automad->Context->set($Page);
			$link = AM_SERVER . AM_BASE_INDEX . $Page->url;
			$content = $this->getPageContent($Page);
			$output .= "\n";

			$output .= <<< XML
					<item>
						<title>{$fn($Page->get(Fields::TITLE))}</title>
						<link>$link</link>
						<guid isPermaLink="true">$link</guid>
						<pubDate>{$fn(Str::dateFormat($Page->get(Fields::TIME_LAST_MODIFIED), DATE_RSS))}</pubDate>
						<description>
							{$fn(Str::stripTags(Str::findFirstParagraph($content)))}
						</description>
						<content:encoded><![CDATA[{$fn($content)}]]></content:encoded>
					</item>
			XML;
		}

		return $output;
	}

	/**
	 * Render item content.
	 *
	 * @param Page|null $Page
	 * @return string the rendered content
	 */
	private function getPageContent(?Page $Page): string {
		$output = '';

		if (!$Page) {
			return $output;
		}

		foreach ($this->fields as $field) {
			if (strpos($field, '+') === 0) {
				$output .= Blocks::render(
					$Page->get($field, true),
					$this->Automad
				);
			} else {
				$output .= Str::markdown($Page->get($field));
			}
		}

		$output = URLProcessor::resolveUrls($output, 'relativeUrlToBase', array($Page));
		$output = URLProcessor::resolveUrls($output, 'absoluteUrlToDomain');

		return $output;
	}
}
