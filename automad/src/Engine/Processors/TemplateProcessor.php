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
use Automad\Engine\Collections\SnippetCollection;
use Automad\Engine\FeatureProvider;
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
	private $Automad;

	private $featureProcessors;

	private $Runtime;

	private $SnippetCollection;

	public function __construct(
		Automad $Automad,
		Runtime $Runtime,
		SnippetCollection $SnippetCollection,
		ContentProcessor $ContentProcessor
	) {
		$this->Automad = $Automad;
		$this->Runtime = $Runtime;
		$this->SnippetCollection = $SnippetCollection;
		$this->ContentProcessor = $ContentProcessor;

		$this->featureProcessors = $this->initFeatureProcessors();
	}

	public function process(string $template, string $directory) {
		$output = PreProcessor::stripWhitespace($template);
		$output = PreProcessor::prepareWrappingStatements($output);

		$output = preg_replace_callback(
			'/' . PatternAssembly::template() . '/is',
			function ($matches) use ($directory) {
				if (!empty($matches['var'])) {
					return $this->ContentProcessor->processVariables($matches['var'], false, true);
				}

				foreach ($this->featureProcessors as $processor) {
					$featureOutput = $processor->process($matches, $directory);

					if (!empty($featureOutput)) {
						return $featureOutput;
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
		$processors = array();

		foreach (FeatureProvider::getProcessorClasses() as $cls) {
			$processors[$cls] = new $cls(
				$this->Automad,
				$this->Runtime,
				$this->SnippetCollection,
				$this->ContentProcessor
			);
		}

		return $processors;
	}
}
