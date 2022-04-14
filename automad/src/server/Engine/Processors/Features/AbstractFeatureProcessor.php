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

namespace Automad\Engine\Processors\Features;

use Automad\Core\Automad;
use Automad\Engine\Processors\ContentProcessor;
use Automad\Engine\Processors\TemplateProcessor;
use Automad\Engine\Runtime;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The abstract feature processor class. All feature processors based on this class must implement
 * a `process()` and a static `syntaxPattern()` method.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
abstract class AbstractFeatureProcessor {
	/**
	 * The main Automad instance.
	 */
	protected $Automad;

	/**
	 * The content processor instance.
	 */
	protected $ContentProcessor;

	/**
	 * The Runtime instance.
	 */
	protected $Runtime;

	/**
	 * The feature processor constructor.
	 *
	 * @param Automad $Automad
	 * @param Runtime $Runtime
	 * @param ContentProcessor $ContentProcessor
	 */
	public function __construct(
		Automad $Automad,
		Runtime $Runtime,
		ContentProcessor $ContentProcessor
	) {
		$this->Automad = $Automad;
		$this->Runtime = $Runtime;
		$this->ContentProcessor = $ContentProcessor;
	}

	/**
	 * The actual processor that is used to process a template substring
	 * that matches the pattern returned by the `syntaxPattern()` method.
	 *
	 * @param array $matches
	 * @param string $directory
	 * @param bool $collectSnippetDefinitions
	 */
	abstract public function process(array $matches, string $directory, bool $collectSnippetDefinitions);

	/**
	 * The actual pattern that is used to trigger the processor.
	 */
	abstract public static function syntaxPattern();

	/**
	 * Create a new instance of the template processor.
	 *
	 * @return TemplateProcessor the template processor instance
	 */
	protected function initTemplateProcessor() {
		return new TemplateProcessor(
			$this->Automad,
			$this->Runtime,
			$this->ContentProcessor
		);
	}
}
