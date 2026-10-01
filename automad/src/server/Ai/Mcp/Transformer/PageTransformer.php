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
	 * @param bool $excludeBlocks
	 * @return array
	 */
	public function toAgent(Page $Page): array {
		// Inject an alias for the origUrl called `id` that can be easily referred to.
		$data = array('id' => $Page->origUrl, '__CONTENT__' => array());

		foreach ($Page->data as $key => $value) {
			if (str_starts_with($key, '+')) {
				$data['__CONTENT__'][$key] = Blocks::toAgent(
					$value['blocks'] ?? array(),
					$this->Automad->ComponentCollection
				);
			} else {
				if (in_array($key, PageTransformer::INCLUDED_FIELDS) || !str_starts_with($key, ':')) {
					$data[$key] = $value;
				}
			}
		}

		if (($data[Fields::URL] ?? '') == $Page->origUrl || empty($data[Fields::URL])) {
			unset($data[Fields::URL]);
		}

		return $data;
	}

	/**
	 * Update a page with transformed incoming agent data.
	 *
	 * @param Page $Page
	 * @param string $id
	 * @param string $title
	 * @param string $template
	 * @param array $tags
	 * @param array $__CONTENT__
	 * @return array
	 */
	public function updateFromAgent(
		Page $Page,
		string $id,
		string $title,
		string $template,
		array $tags = array(),
		array $__CONTENT__ = array()
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
		$data[Fields::TAGS] = join(', ', $tags);

		foreach ($__CONTENT__ as $key => $blocks) {
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
