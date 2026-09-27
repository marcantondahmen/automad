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

use Automad\Ai\Mcp\ResourceId;
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
	const array IGNORED_FIELDS = array(
		Fields::AUTOMAD_VERSION,
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
	 * @param string|null $id
	 * @return array
	 */
	public function toAgent(Page $Page, string|null $id = null): array {
		$id = $id ?? ResourceId::encode($Page->origUrl);
		$uri = "automad://page/$id";

		$data = array(
			'id' => $id,
			'uri' => $uri
		);

		foreach ($Page->data as $key => $value) {
			if (str_starts_with($key, '+')) {
				$data[$key] = Blocks::toAgent(
					$value['blocks'] ?? array(),
					$this->Automad->ComponentCollection
				);
			} else {
				if (!in_array($key, PageTransformer::IGNORED_FIELDS)) {
					$data[$key] = $value;
				}
			}
		}

		return $data;
	}
}
