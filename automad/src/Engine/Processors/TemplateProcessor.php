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
	/**
	 * The main Automad instance.
	 */
	private $Automad;

	/**
	 * An array of all existing feature processor instances.
	 */
	private $featureProcessors;

	/**
	 * The Runtime instance.
	 */
	private $Runtime;

	/**
	 * The template processor constructor.
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
		$this->featureProcessors = $this->initFeatureProcessors();
	}

	/**
	 * The main template render process basically applies all feature processors to the rendered template.
	 *
	 * @param string $template
	 * @param string $directory
	 * @return string the processed template
	 */
	public function process(string $template, string $directory) {
		$output = PreProcessor::stripWhitespace($template);
		$output = PreProcessor::prepareWrappingStatements($output);

		$this->collectSnippetDefinitions($output, $directory);

		$output = $this->processMatches($output, $directory);

		return URLProcessor::resolveUrls(
			$output,
			'relativeUrlToBase',
			array($this->Automad->Context->get())
		);
	}

	/**
	 * Process a template without actually generating any output and collect all
	 * snippet definitions during render time in order to enable overriding elements
	 * on an atomic level after actually including a template to be extended.
	 *
	 * @param string $output
	 * @param string $directory
	 */
	private function collectSnippetDefinitions(string $output, string $directory) {
		$this->processMatches($output, $directory);

		// Remove the snippet definition processor in order to keep overrides during render time.
		// All snippets are already defined in the first run of processMatches().
		unset($this->featureProcessors['Automad\Engine\Processors\Features\SnippetDefinitionProcessor']);
	}

	/**
	 * Create instances of all existing feature processors and bundle them in an array.
	 *
	 * @return array the array of feature processor instances
	 */
	private function initFeatureProcessors() {
		$processors = array();

		foreach (FeatureProvider::getProcessorClasses() as $cls) {
			$processors[$cls] = new $cls(
				$this->Automad,
				$this->Runtime,
				$this->ContentProcessor
			);
		}

		return $processors;
	}

	/**
	 * Process the actual regex patterns of all features.
	 *
	 * @param string $output
	 * @param string $directory
	 * @return string the processed output
	 */
	private function processMatches(string $output, string $directory) {
		return preg_replace_callback(
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
	}
}
