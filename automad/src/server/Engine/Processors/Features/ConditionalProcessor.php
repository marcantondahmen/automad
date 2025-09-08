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

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The conditional processor.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ConditionalProcessor extends AbstractFeatureProcessor {
	/**
	 * Process `if` and `if ... else` statements.
	 *
	 * @param array $matches
	 * @param string $directory
	 * @return string the processed string
	 */
	public function process(array $matches, string $directory): string {
		if (empty($matches['if'])) {
			return '';
		}

		$ifSnippet = $matches['ifSnippet'];
		$ifElseSnippet = '';

		if (!empty($matches['ifElseSnippet'])) {
			$ifElseSnippet = $matches['ifElseSnippet'];
		}

		// Match each part of a logically combined expression separately.
		preg_match_all(
			'/(?P<operator>^|' . PatternAssembly::LOGICAL_OPERATOR . '\s+)' .
			PatternAssembly::expression('expression') . '/is',
			trim($matches['if']),
			$parts,
			PREG_SET_ORDER
		);

		$result = true;

		// Process each part and merge the partial result with the final result.
		foreach ($parts as $part) {
			$partialResult = true;

			// Separate comparisons from boolean expressions and get a partial result.
			if (!empty($part['expressionOperator'])) {
				// Comparison.

				// Merge default keys with $part to make sure each key exists in $part without testing.
				$part = array_merge(
					array(
						'expressionLeftDoubleQuoted' => '',
						'expressionLeftSingleQuoted' => '',
						'expressionLeftNumber' => '',
						'expressionLeftVar' => '',
						'expressionRightDoubleQuoted' => '',
						'expressionRightSingleQuoted' => '',
						'expressionRightNumber' => '',
						'expressionRightVar' => ''
					),
					$part
				);

				// Parse both sides of the expression. All possible matches for each side can get merged in to one string,
				// since there will be only one item for left/right not empty.
				$left = $this->ContentProcessor->processVariables(
					stripslashes($part['expressionLeftDoubleQuoted']) .
					stripslashes($part['expressionLeftSingleQuoted']) .
					$part['expressionLeftNumber'] .
					$part['expressionLeftVar']
				);

				$right = $this->ContentProcessor->processVariables(
					stripslashes($part['expressionRightDoubleQuoted']) .
					stripslashes($part['expressionRightSingleQuoted']) .
					$part['expressionRightNumber'] .
					$part['expressionRightVar']
				);

				// Build and evaluate the expression.
				switch ($part['expressionOperator']) {
					case '=':
						$partialResult = ($left == $right);

						break;
					case '!=':
						$partialResult = ($left != $right);

						break;
					case '>':
						$partialResult = ($left > $right);

						break;
					case '>=':
						$partialResult = ($left >= $right);

						break;
					case '<':
						$partialResult = ($left < $right);

						break;
					case '<=':
						$partialResult = ($left <= $right);

						break;
				}
			} else {
				// Boolean.

				// Get the value of the given variable.
				$expressionVar = $this->ContentProcessor->processVariables($part['expressionVar']);

				// If EMPTY NOT == NOT EMPTY Value.
				$partialResult = (empty($part['expressionNot']) == !empty($expressionVar));
			}

			// Combine results based on logical operator - note that for the first part,
			// the operator will be empty of course.
			switch (strtolower(trim($part['operator']))) {
				case '':
					$result = $partialResult;

					break;
				case 'and':
					$result = ($result && $partialResult);

					break;
				case 'or':
					$result = ($result || $partialResult);

					break;
			}
		}

		// Process snippet depending on $result.
		$TemplateProcessor = $this->initTemplateProcessor();

		if ($result) {
			Debug::log('TRUE', 'Evaluating condition: if ' . $matches['if']);

			return $TemplateProcessor->process($ifSnippet, $directory);
		}

		Debug::log('FALSE', 'Evaluating condition: if ' . $matches['if']);

		return $TemplateProcessor->process($ifElseSnippet, $directory);
	}

	/**
	 * The pattern that is used to match conditionals in a template string.
	 *
	 * @return string the regex pattern for conditionals
	 */
	public static function syntaxPattern(): string {
		$statementOpen = preg_quote(Delimiters::STATEMENT_OPEN);
		$statementClose = preg_quote(Delimiters::STATEMENT_CLOSE);

		return  $statementOpen . '\s*' .
				Delimiters::OUTER_STATEMENT_MARKER . '\s*' .
				'if\s+(?P<if>' . PatternAssembly::expression() .
				'(\s+' . PatternAssembly::LOGICAL_OPERATOR . '\s+' . PatternAssembly::expression() . ')*)' .
				'\s*' . $statementClose .
				'(?P<ifSnippet>.*?)' .
				'(?:' . $statementOpen . Delimiters::OUTER_STATEMENT_MARKER .
					'\s*else\s*' .
				$statementClose . '(?P<ifElseSnippet>.*?)' . ')?' .
				$statementOpen . Delimiters::OUTER_STATEMENT_MARKER . '\s*end' .
				'\s*' . $statementClose;
	}
}
