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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Ai\Mcp\Transformer;

use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Core\Value;
use Automad\Models\Page;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The transformer class for page objects.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageTransformer {
	const array INCLUDED_FIELDS = array(
		Fields::TIME_CREATED,
		Fields::TIME_LAST_MODIFIED
	);

	/**
	 * The Automad instance.
	 */
	private Automad $Automad;

	/**
	 * The constructor.
	 *
	 * @param Automad $Automad
	 */
	public function __construct(Automad $Automad) {
		$this->Automad = $Automad;
	}

	/**
	 * Return the common base meta data for a page.
	 *
	 * @param $Page
	 * @return array
	 */
	public function baseData(Page $Page): array {
		return array(
			'id' => $Page->origUrl,
			'url' => AM_SERVER . $Page->origUrl,
			'title' => $Page->get(Fields::TITLE),
			'parent' => $Page->parentUrl,
			'lastModified' => $Page->get(Fields::TIME_LAST_MODIFIED),
			'template' => $Page->get(Fields::TEMPLATE),
			'publicationState' => $Page->isPublished() ? 'published' : 'draft',
			'private' => $Page->private,
			'hidden' => $Page->hidden,
			'tags' => $Page->tags
		);
	}

	/**
	 * Transform a page object into an agent optimized representation.
	 *
	 * @param Page $Page
	 * @param bool $blocksToString
	 * @return array
	 */
	public function toAgent(Page $Page, bool $blocksToString = false): array {
		// Inject an alias for the origUrl called `id` that can be easily referred to.
		$data = array(
			...$this->baseData($Page),
			'date' => $Page->get(Fields::DATE),
			'content' => array(),
			'meta' => array()
		);

		$transform = $blocksToString ? 'toString' : 'toAgent';
		$excludeFromMeta = array(
			Fields::HIDDEN,
			Fields::PRIVATE,
			Fields::URL,
			Fields::TITLE,
			Fields::DATE,
			Fields::TAGS
		);

		foreach ($Page->data as $key => $value) {
			if (str_starts_with($key, '+')) {
				$data['content'][$key] = Blocks::$transform(
					$value['blocks'] ?? array(),
					$this->Automad->ComponentCollection
				);
			} else {
				if ((
					in_array($key, PageTransformer::INCLUDED_FIELDS) ||
						preg_match('/^[a-z]/i', $key)
				) && !empty($value) && !in_array($key, $excludeFromMeta)
				) {
					$data['meta'][$key] = $value;
				}
			}
		}

		return $data;
	}

	/**
	 * Update a page with transformed incoming agent data.
	 *
	 * All values are optional. A value that is `null` keeps the current value of the page.
	 * An explicit empty value (`''`, `false` or `[]`) clears the related field, except for the title.
	 * Only content fields that are part of `$content` are replaced, all other content fields are kept.
	 *
	 * @param Page $Page
	 * @param string|null $title
	 * @param string|null $template
	 * @param string|null $theme
	 * @param string|null $date
	 * @param bool|null $private
	 * @param bool|null $hidden
	 * @param array|null $tags
	 * @param array|null $content
	 * @return array
	 */
	public function updateFromAgent(
		Page $Page,
		?string $title = null,
		?string $template = null,
		?string $theme = null,
		?string $date = null,
		?bool $private = null,
		?bool $hidden = null,
		?array $tags = null,
		?array $content = null
	): array {
		$data = array_filter(
			$Page->data,
			fn ($value, $key): bool => (in_array($key, PageTransformer::INCLUDED_FIELDS) || preg_match('/^[^:]/', $key)) && !empty($value),
			ARRAY_FILTER_USE_BOTH
		);

		if (($data[Fields::URL] ?? '') == $Page->origUrl || empty($data[Fields::URL])) {
			unset($data[Fields::URL]);
		}

		// A page must always keep a title, therefore an empty title is ignored.
		if (!empty($title)) {
			$data[Fields::TITLE] = $title;
		}

		if ($template !== null) {
			$data[Fields::TEMPLATE] = $template;
		}

		if ($theme !== null) {
			$data[Fields::THEME] = $theme;
		}

		if ($date !== null) {
			$data[Fields::DATE] = $date;
		}

		if ($private !== null) {
			$data[Fields::PRIVATE] = $private;
		}

		if ($hidden !== null) {
			$data[Fields::HIDDEN] = $hidden;
		}

		if ($tags !== null) {
			$data[Fields::TAGS] = join(', ', $tags);
		}

		$data = array_filter($data, fn ($value) => !empty($value));

		foreach ($content ?? array() as $key => $blocks) {
			$originalBlocks = array();

			if (array_key_exists($key, $Page->data)) {
				// Forward existing blocks from data store for merging tunes
				// and other complex details that are not exposed to MCP.
				$originalField = Value::asEditorArray($Page->data[$key]);
				$originalBlocks = $originalField['blocks'] ?? array();
			}

			$data[$key] = array('blocks' => Blocks::fromAgent($blocks, $originalBlocks));
		}

		return $data;
	}
}
