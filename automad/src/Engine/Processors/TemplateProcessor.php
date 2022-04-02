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
	 * Note that the $collectSnippetDefinitions parameter controls whether snippets are added to the
	 * snippet collection in order to enable basic inheritance.
	 *
	 * @param string $template
	 * @param string $directory
	 * @param bool $collectSnippetDefinitions
	 * @return string the processed template
	 */
	public function process(string $template, string $directory, bool $collectSnippetDefinitions) {
		$output = PreProcessor::stripWhitespace($template);
		$output = PreProcessor::prepareWrappingStatements($output);

		$output = preg_replace_callback(
			'/' . PatternAssembly::template() . '/is',
			function ($matches) use ($directory, $collectSnippetDefinitions) {
				if (!empty($matches['var'])) {
					return $this->ContentProcessor->processVariables($matches['var'], false, true);
				}

				foreach ($this->featureProcessors as $processor) {
					$featureOutput = $processor->process($matches, $directory, $collectSnippetDefinitions);

					if (!empty($featureOutput)) {
						return $featureOutput;
					}
				}

				return '';
			},
			$output
		);

		return URLProcessor::resolveUrls(
			$output,
			'relativeUrlToBase',
			array($this->Automad->Context->get())
		);
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
}
