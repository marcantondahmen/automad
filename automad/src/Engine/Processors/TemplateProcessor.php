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

namespace Automad\Engine\Processors;

use Automad\Core\Automad;
use Automad\Core\FileSystem;
use Automad\Engine\Collections\AssetCollection;
use Automad\Engine\Collections\SnippetCollection;
use Automad\Engine\PatternAssembly;
use Automad\Engine\Runtime;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The main template processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class TemplateProcessor {
	private $AssetCollection;

	private $Automad;

	private $Runtime;

	private $SnippetCollection;

	private $featureProcessors;

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

		$this->featureProcessors = $this->initFeatureProcessors();
	}

	public function process(string $template, string $directory) {
		$output = PreProcessor::stripWhitespace($template);
		$output = PreProcessor::prepareWrappingStatements($output);

		$output = preg_replace_callback(
			'/' . PatternAssembly::markup() . '/is',
			function ($matches) use ($directory) {
				foreach ($this->featureProcessors as $processor) {
					$processed = $processor->process($matches, $directory);

					if (!empty($processed)) {
						return $processed;
					}
				}
			},
			$output
		);

		return URLProcessor::resolveUrls(
			$output,
			'relativeUrlToBase',
			array($this->Automad->Context->get())
		);
	}

	private function initFeatureProcessors() {
		$files = FileSystem::glob(__DIR__ . '/Features/*.php');

		foreach ($files as $file) {
			require_once $file;
		}

		$processorClasses = array_filter(get_declared_classes(), function ($cls) {
			return (strpos($cls, 'Engine\Processors\Features') !== false && strpos($cls, 'Abstract') === false);
		});

		$processors = array();

		foreach ($processorClasses as $cls) {
			$processors[$cls] = new $cls(
				$this->Automad,
				$this->Runtime,
				$this->AssetCollection,
				$this->SnippetCollection,
				$this->ContentProcessor
			);
		}

		return $processors;
	}
}
