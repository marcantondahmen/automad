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

namespace Automad\Models\Search;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The search index entry class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SearchIndexEntry {
	/**
	 * @var array<string, string>
	 */
	public $fields = array();

	/**
	 * @var string|null
	 */
	public string|null $origUrl;

	/**
	 * @var string|null
	 */
	public string|null $path;

	/**
	 * The constructor method.
	 *
	 * @param string|null $origUrl
	 * @param string|null $path
	 */
	public function __construct(string|null $origUrl, string|null $path) {
		$this->origUrl = $origUrl;
		$this->path = $path;
	}

	/**
	 * Add a field and value to the entry.
	 *
	 * @param string $field
	 * @param string $content
	 */
	public function addField(string $field, string $content): void {
		$this->fields[$field] = $content;
	}
}
