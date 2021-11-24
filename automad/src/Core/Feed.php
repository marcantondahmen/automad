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

namespace Automad\Core;

use Automad\Engine\Processors\URLProcessor;
use Automad\System\Server;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Feed class handles the rendering of the feed.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Feed {
	/**
	 * The Automad object.
	 */
	private $Automad;

	/**
	 * The included fields.
	 */
	private $fields = array();

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
	public function get() {
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
					<title>{$fn($this->Automad->Shared->get(AM_KEY_SITENAME))}</title>
					<link>{$fn(Server::url())}</link>
					<atom:link href="{$fn(Server::url() . AM_BASE_INDEX . AM_FEED_URL)}" rel="self" type="application/rss+xml" />
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
	private function fn() {
		return function ($expression) { return $expression; };
	}

	/**
	 * Get the items output.
	 *
	 * @param array $pages
	 * @return string the rendered XML
	 */
	private function getItems(array $pages) {
		$fn = $this->fn();
		$output = '';

		foreach ($pages as $Page) {
			$this->Automad->Context->set($Page);
			$link = Server::url() . AM_BASE_INDEX . $Page->url;
			$content = $this->getPageContent($Page);
			$output .= "\n";

			$output .= <<< XML
					<item>
						<title>{$fn($Page->get(AM_KEY_TITLE))}</title>
						<link>$link</link>
						<guid isPermaLink="true">$link</guid>
						<pubDate>{$fn(Str::dateFormat($Page->getMTime(), DATE_RSS))}</pubDate>
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
	 * @param Page $Page
	 * @return string the rendered content
	 */
	private function getPageContent(Page $Page) {
		$output = '';

		foreach ($this->fields as $field) {
			if (strpos($field, '+') === 0) {
				$output .= Blocks::render(
					$Page->get($field),
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
