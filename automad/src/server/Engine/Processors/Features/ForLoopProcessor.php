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

namespace Automad\Engine\Processors\Features;

use Automad\Core\Debug;
use Automad\Engine\Delimiters;
use Automad\Engine\PatternAssembly;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The for loop processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ForLoopProcessor extends AbstractFeatureProcessor {
	/**
	 * Process `for` loops.
	 *
	 * @param array $matches
	 * @param string $directory
	 * @param bool $collectSnippetDefinitions
	 * @return string the processed string
	 */
	public function process(array $matches, string $directory, bool $collectSnippetDefinitions): string {
		if (empty($matches['forSnippet'])) {
			return '';
		}

		$start = intval($this->ContentProcessor->processVariables($matches['forStart']));
		$end = intval($this->ContentProcessor->processVariables($matches['forEnd']));
		$html = '';

		$TemplateProcessor = $this->initTemplateProcessor();

		// Save the index before any loop - the index will be overwritten when iterating over filter, tags and files and must be restored after the loop.
		$runtimeShelf = $this->Runtime->shelve();

		// The loop.
		for ($i = $start; $i <= $end; $i++) {
			// Set index variable. The index can be used as @{:i}.
			$this->Runtime->set(Fields::LOOP_INDEX, $i);
			// Parse snippet.
			Debug::log($i, 'Processing snippet in loop for index');
			$html .= $TemplateProcessor->process($matches['forSnippet'], $directory, $collectSnippetDefinitions);
		}

		// Restore index.
		$this->Runtime->unshelve($runtimeShelf);

		return $html;
	}

	/**
	 * The pattern that is used to match for loops.
	 *
	 * @return string the for loop pattern
	 */
	public static function syntaxPattern(): string {
		$statementOpen = preg_quote(Delimiters::STATEMENT_OPEN);
		$statementClose = preg_quote(Delimiters::STATEMENT_CLOSE);

		return  $statementOpen . '\s*' .
				Delimiters::OUTER_STATEMENT_MARKER . '\s*' .
				'for\s+(?P<forStart>' .
					PatternAssembly::variable() . '|' . PatternAssembly::NUMBER .
				')\s+to\s+(?P<forEnd>' .
					PatternAssembly::variable() . '|' . PatternAssembly::NUMBER .
				')\s*' . $statementClose .
				'(?P<forSnippet>.*?)' .
				$statementOpen . Delimiters::OUTER_STATEMENT_MARKER . '\s*end' .
				'\s*' . $statementClose;
	}
}
