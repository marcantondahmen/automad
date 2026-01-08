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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models\Search;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A wrapper class for all results for a given data file.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class FileResults {
	/**
	 * The the count of results in a file.
	 */
	public int $count;

	/**
	 * The array of `FieldResults`.
	 *
	 * @see FieldResults
	 * @var array<FieldResults>
	 */
	public array $fieldResultsArray;

	/**
	 * The page directory path or null for shared data.
	 */
	public ?string $path;

	/**
	 * The page URL or null for shared data.
	 */
	public ?string $url;

	/**
	 * Initialize a new field results instance.
	 *
	 * @see FieldResults
	 * @param array<FieldResults> $fieldResultsArray
	 * @param string|null $path
	 * @param string|null $url
	 */
	public function __construct(array $fieldResultsArray, ?string $path, ?string $url) {
		$this->fieldResultsArray = $fieldResultsArray;
		$this->path = $path;
		$this->url = $url;
		$this->count = 0;

		foreach ($fieldResultsArray as $fieldResults) {
			$this->count += count($fieldResults->matches);
		}
	}
}
