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
use Automad\Engine\Collections\AssetCollection;
use Automad\Engine\Collections\SnippetCollection;
use Automad\Engine\Processors\ContentProcessor;
use Automad\Engine\Processors\TemplateProcessor;
use Automad\Engine\Runtime;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The abstract processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
abstract class AbstractFeatureProcessors {
	protected $AssetCollection;

	protected $Automad;

	protected $ContentProcessor;

	protected $Runtime;

	protected $SnippetCollection;

	public function __construct(
		Automad $Automad,
		Runtime $Runtime,
		AssetCollection $AssetCollection,
		SnippetCollection $SnippetCollection,
		ContentProcessor $ContentProcessor
	) {
		$this->Automad = $Automad;
		$this->Runtime = $Runtime;
		$this->AssetCollection = $AssetCollection;
		$this->SnippetCollection = $SnippetCollection;
		$this->ContentProcessor = $ContentProcessor;
	}

	abstract public function process(array $matches, string $directory);

	abstract public static function syntaxPattern();

	protected function initTemplateProcessor() {
		return new TemplateProcessor(
			$this->Automad,
			$this->Runtime,
			$this->AssetCollection,
			$this->SnippetCollection,
			$this->ContentProcessor
		);
	}
}
