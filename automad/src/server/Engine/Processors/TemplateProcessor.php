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

namespace Automad\Engine\Processors;

use Automad\Admin\InPage;
use Automad\Core\Automad;
use Automad\Engine\FeatureProvider;
use Automad\Engine\PatternAssembly;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The main template processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class TemplateProcessor {
	/**
	 * A flag the can be used to configure whether snippet definitions are registered when processing templates.
	 */
	public static bool $registerSnippets = true;

	/**
	 * The main Automad instance.
	 */
	private Automad $Automad;

	/**
	 * The content processor instance.
	 */
	private ContentProcessor $ContentProcessor;

	/**
	 * An array of all existing feature processor instances.
	 */
	private array $featureProcessors;

	/**
	 * The template processor constructor.
	 *
	 * @param Automad $Automad
	 * @param ContentProcessor $ContentProcessor
	 */
	public function __construct(
		Automad $Automad,
		ContentProcessor $ContentProcessor
	) {
		$this->Automad = $Automad;
		$this->ContentProcessor = $ContentProcessor;
		$this->featureProcessors = $this->initFeatureProcessors();
	}

	/**
	 * Create a template processor instance.
	 *
	 * @param Automad $Automad
	 * @param InPage|null $InPage
	 * @return TemplateProcessor
	 */
	public static function create(Automad $Automad, InPage|null $InPage = null): TemplateProcessor {
		$InPage ??= new InPage($Automad);

		$ContentProcessor = new ContentProcessor(
			$Automad,
			$InPage
		);

		return new TemplateProcessor(
			$Automad,
			$ContentProcessor
		);
	}

	/**
	 * The main template render process basically applies all feature processors to the rendered template.
	 * Note that the TemplateProcessor::$registerSnippets parameter controls whether snippets are added to the
	 * snippet collection in order to enable basic inheritance.
	 *
	 * @param string $template
	 * @param string $directory
	 * @return string the processed template
	 */
	public function process(string $template, string $directory): string {
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

				return '';
			},
			$output
		) ?? '';

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
	private function initFeatureProcessors(): array {
		$processors = array();

		foreach (FeatureProvider::getProcessorClasses() as $cls) {
			$processors[$cls] = new $cls(
				$this->Automad,
				$this->ContentProcessor
			);
		}

		return $processors;
	}
}
