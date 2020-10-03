<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2016-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 * 	The Regex class holds all methods relating regular expressions.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2016-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Regex {

	
	/**
	 *	The character class to be used within a regex matching all allowed characters for variable names within .txt files.
	 */
	
	public static $charClassTextFileVariables = '[\+\w\.\-]';


	/**
	 *	The character class to be used within a regex matching all allowed characters for all kine of variable names (content in .txt files, system variables ( :var ) and query string items ( ?var )).
	 */

	public static $charClassAllVariables = '[%:\?\+\w\.\-]';
		

	/**
	 *	Logical operand "and" or "or".
	 */

	public static $logicalOperator = '(?:and|or)';

	
	/**
	 *	Number (integer and float).
	 */

	public static $number = '\d+(?:\.\d+)?';


	/**
	 * 	The outer statement marker helps to distinguish all outer wrapping statements from the inner statements.
	 */
	
	public static $outerStatementMarker = '#';


	/**
	 *	Return a regex to match a sequence of comma separated values or variables.    
	 *	
	 *	In case $isVariableSubpattern is true, the generated value patterns have a 
	 *	relative reference to the wrapping main variable pattern to match variables 
	 *	within parameters.
	 * 
	 *	@param boolean $isVariableSubpattern
	 *	@return string The regex matching a comma separated parameter string.
	 */
	
	public static function csv($isVariableSubpattern = false) {
		
		if ($isVariableSubpattern) {
			return 	self::value('(?-7)') .
			 		'(?:,' . self::value('(?-8)') . ')*?';
		} else {
			$var = self::variable();
			return 	self::value($var) .
			 		'(?:,' . self::value($var) . ')*?';
		}
			
	}


	/**
	 *	Return the regex pattern for a single expression.   
	 *	    
	 *	Valid expressions are:    
	 *	
	 *	-	@{var} >= 5
	 *	-	@{var} != "Text ..."
	 *	-	@{var} = 'Text'
	 *	-	not @{var}
	 *	-	!@{var}
	 *	
	 *	@param string $namedReferencePrefix
	 *	@return string The regex
	 */
	
	public static function expression($namedReferencePrefix = false) {
		
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
		
		return '(?:' . Regex::operand($left) . '\s*(' . $operator . '!?=|>=?|<=?)\s*' . Regex::operand($right) . '|(' . $not . '!|not\s+)?(' . $var . Regex::variable() . '))';
		
	}

	
	/**
	 *	Return regex to match any temporary in-page edit button.
	 *      
	 *	@return string The regex
	 */
	
	public static function inPageEditButton() {
		
		return preg_quote(AM_DEL_INPAGE_BUTTON_OPEN) . '.+?' . preg_quote(AM_DEL_INPAGE_BUTTON_CLOSE);
		
	}
	
	
	/**
	 *	Return the regex to match any kind of Automad markup such as variables, toolbox methods, includes, extensions, snippets, loops and conditions.
	 *
	 *	@return string The markup regex
	 */
	
	public static function markup() {
		
		$var = Regex::variable();	
		$statementOpen = preg_quote(AM_DEL_STATEMENT_OPEN);
		$statementClose = preg_quote(AM_DEL_STATEMENT_CLOSE);
		
		// The subpatterns don't include the wrapping delimiter <@ subpattern @>.
		$statementSubpatterns['include'] = 	'(?P<file>[\w\/\-\.]+\.[a-z0-9]{2,5})';
		
		$statementSubpatterns['call'] = 	'(?P<call>[\w\/\-]+)\s*(?P<callOptions>\{.*?\})?';
		
		$statementSubpatterns['snippet'] = 	Regex::$outerStatementMarker . '\s*' . //Note the additional preparsed marker!
											'snippet\s+(?P<snippet>[\w\-]+)' .
											'\s*' . $statementClose . 
											'(?P<snippetSnippet>.*?)' . 
											$statementOpen . Regex::$outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
		
		$statementSubpatterns['with'] = 	Regex::$outerStatementMarker . '\s*' . // Note the additional preparsed marker!
											'with\s+(?P<with>' .
												'"[^"]*"|' . "'[^']*'|" . $var . '|prev|next' .
											')' . 
											'\s*(?P<withOptions>\{.*?\})?' .
											'\s*' . $statementClose . 
											'(?P<withSnippet>.*?)' . 	
											'(?:' . $statementOpen . Regex::$outerStatementMarker . '\s*else\s*' . $statementClose . '(?P<withElseSnippet>.*?)' . ')?' . // Note the additional preparsed marker!	
											$statementOpen . Regex::$outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
		
		$statementSubpatterns['for'] =		Regex::$outerStatementMarker . '\s*' . 	// Note the additional preparsed marker!
											'for\s+(?P<forStart>' . Regex::variable() . '|' . Regex::$number . ')\s+to\s+(?P<forEnd>' . Regex::variable() . '|' . Regex::$number . ')' . 
											'\s*' . $statementClose .
											'(?P<forSnippet>.*?)' .
											$statementOpen . Regex::$outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
		
		$statementSubpatterns['foreach'] = 	Regex::$outerStatementMarker . '\s*' .	// Note the additional preparsed marker!
											'foreach\s+in\s+(?P<foreach>' . 
												'pagelist|' . 
												'filters|' . 
												'tags|' . 
												'filelist|' .
												'"[^"]*"|' . "'[^']*'|" . $var . 		
											')' . 
											'\s*(?P<foreachOptions>\{.*?\})?' .
											'\s*' . $statementClose . 
											'(?P<foreachSnippet>.*?)' . 
											'(?:' . $statementOpen . Regex::$outerStatementMarker . '\s*else\s*' . $statementClose . '(?P<foreachElseSnippet>.*?)' . ')?'. // Note the additional preparsed marker!
											$statementOpen . Regex::$outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
		
		$statementSubpatterns['condition'] = 	Regex::$outerStatementMarker . '\s*' .	// Note the additional preparsed marker!
												'if\s+(?P<if>' . Regex::expression() . '(\s+' . Regex::$logicalOperator . '\s+' . Regex::expression() . ')*)' . 	
												'\s*' . $statementClose . 
												'(?P<ifSnippet>.*?)' . 
												'(?:' . $statementOpen . Regex::$outerStatementMarker . '\s*else\s*' . $statementClose . '(?P<ifElseSnippet>.*?)' . ')?' . // Note the additional preparsed marker!	
												$statementOpen . Regex::$outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
		
		// (variable | statements)		
		return '((?P<var>' . $var . ')|' . $statementOpen . '\s*(?:' . implode('|', $statementSubpatterns) . ')\s*' . $statementClose . ')'; 
			
	}


	/**
	 *	Return the regex to match one operand of an expression.   
	 *	     
	 *	Valid operands are:
	 *
	 *	- @{var}
	 *	- "Text ..."
	 *	- 'Text ...'
	 *	- "Text and @{var}"
	 *	- 5
	 *	- 1.5
	 *	
	 *	@param string $namedReferencePrefix
	 *	@return string The regex
	 */

	public static function operand($namedReferencePrefix = false) {
		
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
		
		return '(?:"(' . $doubleQuoted . '(?:[^"\\\\]|\\\\.)*)"|\'(' . $singleQuoted . '(?:[^\'\\\\]|\\\\.)*)\'|(' . $num . Regex::$number . ')|(' . $var . Regex::variable() . '))';
		
	}

	
	/**
	 * 	Return a regex pattern to match key/value pairs in an invalid JSON string 
	 * 	without valid quoting/escaping.
	 * 	 
	 *	@return string The generated pattern.
	 */
	
	public static function keyValue() {
		
		$key = '(?P<key>' . self::$charClassAllVariables . '+|\"' . self::$charClassAllVariables . '+\")';
		$value = '(?P<value>' . self::value(self::variable()) . ')';
		$pair = '\s*' . $key . '\s*:\s*' . $value . '\s*';
		
		return '(?<=(\{|,))(' . $pair . ')(?=(\}|,))';
		
	} 

	
	/**
	 *	Return the regex for a piped string function or math operation of content variables.     
	 *	Like: 
	 *	- "| name (parameters)" 
	 *	- "| +5"
	 *
	 * 	Parameters can be strings wrapped in quotes, 
	 * 	single words without quotes, numbers and variables.
	 * 	In case $isVariableSubpattern is true, relative references to the wrapping 
	 * 	variable pattern are used to match variables.
	 *
	 *	@param string $namedReferencePrefix
	 *	@param boolean $isVariableSubpattern
	 *	@return string The regex to match functions and their parameters or math operations
	 */
	
	public static function pipe($namedReferencePrefix = false, $isVariableSubpattern = false) {
		
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
				$num . Regex::$number . '|' . $subpatternMathVar .
				')\s*' . 
				')';
				
	}
	
	
	/**
	 *	Return the regex to match a single function parameter (pipe).
	 *	
	 *	@param string $subpatternVar
	 *	@return string The regex matching a function parameter.
	 */

	public static function value($subpatternVar) {
		
		// Any quoted string. Single and double quotes are allowed.
		// Use possessive quantifiers to improve performance of very long values.
		$string = '"(?:[^"\\\\]|\\\\.)*+"|\'(?:[^\'\\\\]|\\\\.)*+\'';
		
		return '\s*(' . $string . '|\w+|' . self::$number . '|' . $subpatternVar . ')\s*';
		
	}
	
	
	/**
	 *	Return the regex for content variables. 
	 *	A prefix can be defined as the first parameter to create named backreferences for each capturing group. 
	 *	Like: @{var|function1(...)|function2(...)| ... }
	 *
	 * 	In case the pattern is used as a subpattern of the pipe() method, $pipeReference can be specified to 
	 * 	reference the whole pipe pattern by using a relative reference or (?R).
	 *
	 *	@param string $namedReferencePrefix
	 *	@param string $pipeReference
	 *	@return string The regex to match variables.
	 */
	
	public static function variable($namedReferencePrefix = false, $pipeReference = false) {
		
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
	 * 	A simplified pattern to match all used variable names in a template (GUI).
	 * 	
	 *	@return string The regex pattern.
	 */
	
	public static function variableKeyGUI() {
		
		return preg_quote(AM_DEL_VAR_OPEN) . '\s*(?P<varName>' . self::$charClassTextFileVariables . '+)';
		
	}
	
	
}
