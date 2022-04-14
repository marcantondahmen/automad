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
 * Copyright (c) 2016-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The PatternAssembly class contains all methods to assemble regular expressions patterns.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class PatternAssembly {
	/**
	 * The character class to be used within a regex matching all allowed characters for all kine of variable names (content in .txt files, system variables ( :var ) and query string items ( ?var )).
	 */
	public static $charClassAllVariables = '[%:\?\+\w\.\-]';

	/**
	 * The character class to be used within a regex matching all allowed characters for variable names within .txt files.
	 */
	public static $charClassTextFileVariables = '[\+\w\.\-]';

	/**
	 * Logical operand "and" or "or".
	 */
	public static $logicalOperator = '(?:and|or)';

	/**
	 * Number (integer and float).
	 */
	public static $number = '\d+(?:\.\d+)?';

	/**
	 * The outer statement marker helps to distinguish all outer wrapping statements from the inner statements.
	 */
	public static $outerStatementMarker = '#';

	/**
	 * Return a regex to match a sequence of comma separated values or variables.
	 *
	 * In case $isVariableSubpattern is true, the generated value patterns have a
	 * relative reference to the wrapping main variable pattern to match variables
	 * within parameters.
	 *
	 * @param bool $isVariableSubpattern
	 * @return string The regex matching a comma separated parameter string.
	 */
	public static function csv(bool $isVariableSubpattern = false) {
		if ($isVariableSubpattern) {
			return 	self::value('(?-7)') .
					'(?:,' . self::value('(?-8)') . ')*?';
		}

		$var = self::variable();

		return 	self::value($var) . '(?:,' . self::value($var) . ')*?';
	}

	/**
	 * Return the regex pattern for a single expression.
	 *
	 * Valid expressions are:
	 *
	 * - @{var} >= 5
	 * - @{var} != "Text ..."
	 * - @{var} = 'Text'
	 * - not @{var}
	 * - !@{var}
	 *
	 * @param string|null $namedReferencePrefix
	 * @return string The regex
	 */
	public static function expression(?string $namedReferencePrefix = null) {
		if ($namedReferencePrefix) {
			$left = $namedReferencePrefix . 'Left';
			$operator = '?P<' . $namedReferencePrefix . 'Operator>';
			$right = $namedReferencePrefix . 'Right';
			$not = '?P<' . $namedReferencePrefix . 'Not>';
			$var = '?P<' . $namedReferencePrefix . 'Var>';
		} else {
			$left = false;
			$operator = '?:';
			$right = false;
			$not = '?:';
			$var = '?:';
		}

		return '(?:' . self::operand($left) . '\s*(' . $operator . '!?=|>=?|<=?)\s*' . self::operand($right) . '|(' . $not . '!|not\s+)?(' . $var . self::variable() . '))';
	}

	/**
	 * Return regex to match any temporary in-page edit button.
	 *
	 * @return string The regex
	 */
	public static function inPageEditButton() {
		return preg_quote(AM_DEL_INPAGE_BUTTON_OPEN) . '.+?' . preg_quote(AM_DEL_INPAGE_BUTTON_CLOSE);
	}

	/**
	 * Return a regex pattern to match key/value pairs in an invalid JSON string
	 * without valid quoting/escaping.
	 *
	 * @return string The generated pattern.
	 */
	public static function keyValue() {
		$key = '(?P<key>' . self::$charClassAllVariables . '+|\"' . self::$charClassAllVariables . '+\")';
		$value = '(?P<value>' . self::value(self::variable()) . ')';
		$pair = '\s*' . $key . '\s*:\s*' . $value . '\s*';

		return '(?<=(\{|,))(' . $pair . ')(?=(\}|,))';
	}

	/**
	 * Return the regex to match one operand of an expression.
	 *
	 * Valid operands are:
	 *
	 * - @{var}
	 * - "Text ..."
	 * - 'Text ...'
	 * - "Text and @{var}"
	 * - 5
	 * - 1.5
	 *
	 * @param string|null $namedReferencePrefix
	 * @return string The regex
	 */
	public static function operand(?string $namedReferencePrefix = null) {
		if ($namedReferencePrefix) {
			$doubleQuoted = '?P<' . $namedReferencePrefix . 'DoubleQuoted>';
			$singleQuoted = '?P<' . $namedReferencePrefix . 'SingleQuoted>';
			$num = '?P<' . $namedReferencePrefix . 'Number>';
			$var = '?P<' . $namedReferencePrefix . 'Var>';
		} else {
			$doubleQuoted = '?:';
			$singleQuoted = '?:';
			$num = '?:';
			$var = '?:';
		}

		return '(?:"(' . $doubleQuoted . '(?:[^"\\\\]|\\\\.)*)"|\'(' . $singleQuoted . '(?:[^\'\\\\]|\\\\.)*)\'|(' . $num . self::$number . ')|(' . $var . self::variable() . '))';
	}

	/**
	 * Return the regex for a piped string function or math operation of content variables.
	 * Like:
	 * - "| name (parameters)"
	 * - "| +5"
	 *
	 * Parameters can be strings wrapped in quotes,
	 * single words without quotes, numbers and variables.
	 * In case $isVariableSubpattern is true, relative references to the wrapping
	 * variable pattern are used to match variables.
	 *
	 * @param string|null $namedReferencePrefix
	 * @param bool $isVariableSubpattern
	 * @return string The regex to match functions and their parameters or math operations
	 */
	public static function pipe(?string $namedReferencePrefix = null, bool $isVariableSubpattern = false) {
		if ($namedReferencePrefix) {
			$function = 	'?P<' . $namedReferencePrefix . 'Function>';
			$parameters =	'?P<' . $namedReferencePrefix . 'Parameters>';
			$operator = 	'?P<' . $namedReferencePrefix . 'Operator>';
			$num = 			'?P<' . $namedReferencePrefix . 'Number>';
		} else {
			$function = '';
			$parameters = '';
			$operator = '';
			$num = '';
		}

		if ($isVariableSubpattern) {
			$subpatternMathVar = '(?-10)';
		} else {
			$subpatternMathVar = self::variable(false, '(?R)');
		}

		return	'\|(' .
				// Function name.
				'\s*(' . $function . '[\w][\w\/\-]*)\s*' .
				// Parameters.
				'(?:\(' .
				'(' . $parameters . self::csv($isVariableSubpattern) . ')?' .
				'\)\s*)?' .
				'|' .
				// Math.
				'\s*(' . $operator . '[\+\-\*\/])\s*(' .
				$num . self::$number . '|' . $subpatternMathVar .
				')\s*' .
				')';
	}

	/**
	 * Return the regex to match any kind of Automad template such as variables,
	 * toolbox methods, includes, extensions, snippets, loops and conditions.
	 *
	 * @return string The template regex
	 */
	public static function template() {
		$statementPatterns = array();

		foreach (FeatureProvider::getProcessorClasses() as $cls) {
			$statementPatterns[] = $cls::syntaxPattern();
		}

		return  '(' .
				'(?P<var>' . PatternAssembly::variable() . ')|' .
				implode('|', $statementPatterns) .
				')';
	}

	/**
	 * Return the regex to match a single function parameter (pipe).
	 *
	 * @param string $subpatternVar
	 * @return string The regex matching a function parameter.
	 */
	public static function value(string $subpatternVar) {
		// Any quoted string. Single and double quotes are allowed.
		// Use possessive quantifiers to improve performance of very long values.
		$string = '"(?:[^"\\\\]|\\\\.)*+"|\'(?:[^\'\\\\]|\\\\.)*+\'';

		return '\s*(' . $string . '|\w+|' . self::$number . '|' . $subpatternVar . ')\s*';
	}

	/**
	 * Return the regex for content variables.
	 * A prefix can be defined as the first parameter to create named backreferences for each capturing group.
	 * Like: @{var|function1(...)|function2(...)| ... }
	 *
	 * In case the pattern is used as a subpattern of the pipe() method, $pipeReference can be specified to
	 * reference the whole pipe pattern by using a relative reference or (?R).
	 *
	 * @param string|null $namedReferencePrefix
	 * @param string|null $pipeReference
	 * @return string The regex to match variables.
	 */
	public static function variable(?string $namedReferencePrefix = null, ?string $pipeReference = null) {
		if ($namedReferencePrefix) {
			$name = 		'?P<' . $namedReferencePrefix . 'Name>';
			$functions =	'?P<' . $namedReferencePrefix . 'Functions>';
		} else {
			$name = '';
			$functions = '';
		}

		if (!$pipeReference) {
			$pipeReference = self::pipe(false, true);
		}

		return 	'(' . preg_quote(AM_DEL_VAR_OPEN) .
				'\s*(' . $name . self::$charClassAllVariables . '+)\s*' .
				'(' . $functions . '(?:' . $pipeReference . ')*)' .
				preg_quote(AM_DEL_VAR_CLOSE) . ')';
	}

	/**
	 * A simplified pattern to match all used variable names in a template (UI).
	 *
	 * @return string The regex pattern.
	 */
	public static function variableKeyUI() {
		return preg_quote(AM_DEL_VAR_OPEN) . '\s*(?P<varName>' . self::$charClassTextFileVariables . '+)';
	}
}
