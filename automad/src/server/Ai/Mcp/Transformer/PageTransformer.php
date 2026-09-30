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

		if ($data[Fields::URL] == $data[Fields::ORIG_URL]) {
			unset($data[Fields::URL]);
		}

		return $data;
	}
}
