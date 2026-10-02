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
 * The tranformer class for page objects.
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
	 * Transform a page object into an agent optimized representation.
	 *
	 * @param Page $Page
	 * @param bool $blocksToString
	 * @return array
	 */
	public function toAgent(Page $Page, bool $blocksToString = false): array {
		// Inject an alias for the origUrl called `id` that can be easily referred to.
		$data = array(
			'id' => $Page->origUrl,
			'parent' => $Page->parentUrl,
			'url' => AM_SERVER . $Page->origUrl,
			'title' => $Page->get(Fields::TITLE),
			'publicationState' => $Page->isPublished() ? 'published' : 'draft',
			'private' => $Page->private,
			'hidden' => $Page->hidden,
			'content' => array(),
			'meta' => array()
		);

		$transform = $blocksToString ? 'toString' : 'toAgent';
		$excludeFromMeta = array(Fields::HIDDEN, Fields::PRIVATE, Fields::URL, Fields::TITLE);

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
	 * @param Page $Page
	 * @param string $title
	 * @param string $template
	 * @param string $date
	 * @param bool $private
	 * @param bool $hidden
	 * @param array $tags
	 * @param array $content
	 * @return array
	 */
	public function updateFromAgent(
		Page $Page,
		string $title,
		string $template,
		string $date = '',
		bool $private = false,
		bool $hidden = false,
		array $tags = array(),
		array $content = array()
	): array {
		$data = array_filter(
			$Page->data,
			fn ($value, $key): bool => (in_array($key, PageTransformer::INCLUDED_FIELDS) || preg_match('/^[a-z]/i', $key)) && !empty($value),
			ARRAY_FILTER_USE_BOTH
		);

		if (($data[Fields::URL] ?? '') == $Page->origUrl || empty($data[Fields::URL])) {
			unset($data[Fields::URL]);
		}

		$data[Fields::TITLE] = $title;
		$data[Fields::DATE] = $date;
		$data[Fields::PRIVATE] = $private;
		$data[Fields::HIDDEN] = $hidden;
		$data[Fields::TAGS] = join(', ', $tags);

		foreach ($content as $key => $blocks) {
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
