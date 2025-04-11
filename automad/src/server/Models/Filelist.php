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
 * Copyright (c) 2015-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models;

use Automad\Core\Debug;
use Automad\Core\FileUtils;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Filelist object represents a set of files based on a file pattern depending on the current context.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2015-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Filelist {
	/**
	 * The Context.
	 */
	private Context $Context;

	/**
	 * The options array.
	 */
	private array $options = array(
		'glob' => '*.jpg, *.jpeg, *.png, *.gif, *.webp',
		'sort' => 'asc'
	);

	/**
	 * The constructor.
	 *
	 * @param Context $Context
	 */
	public function __construct(Context $Context) {
		$this->Context = $Context;
	}

	/**
	 * Configure the filelist.
	 *
	 * @param array $options
	 */
	public function config(array $options): void {
		$this->options = array_merge($this->options, $options);
	}

	/**
	 * Return the files array.
	 *
	 * Note that the returned filelist depends on the current context.
	 * Changing the context will change the filelist as long as the glob pattern is relative.
	 *
	 * @return array The array of matched files.
	 */
	public function getFiles(): array {
		// Find files.
		$files = FileUtils::fileDeclaration($this->options['glob'], $this->Context->get(), true);

		// Sort files.
		switch ($this->options['sort']) {
			case 'asc':
				sort($files);

				break;

			case 'desc':
				rsort($files);

				break;
		}

		Debug::log($files);

		return $files;
	}
}
