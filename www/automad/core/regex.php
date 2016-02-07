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
 *	Copyright (c) 2016 by Marc Anton Dahmen
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
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2016 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Regex {

	
	/**
	 *	The character class to be used within a regex matching all allowed characters for variable names within .txt files.
	 */
	
	public static $charClassTextFileVariables = '[\w\.\-]';


	/**
	 *	The character class to be used within a regex matching all allowed characters for all kine of variable names (content in .txt files, system variables ( :var ) and query string items ( ?var )).
	 */

	public static $charClassAllVariables = '[:\?\w\.\-]';
		

	/**
	 * 	The outer statement marker helps to distinguish all outer wrapping statements from the inner statements.
	 */
	
	public static $outerStatementMarker = '#';


	/**
	 *	Return the regex to match any kind of Automad markup such as variables, toolbox methods, includes, extensions, snippets, loops and conditions.
	 *
	 *	@return The markup regex
	 */
	
	public static function markup() {
		
		$var = Regex::contentVariable();	
		$statementOpen = preg_quote(AM_DEL_STATEMENT_OPEN);
		$statementClose = preg_quote(AM_DEL_STATEMENT_CLOSE);
		
		// The subpatterns don't include the wrapping delimiter: "{@ subpattern @}".
		$statementSubpatterns['include'] = '(?P<file>[\w\/\-\.]+\.php)';
		
		$statementSubpatterns['call'] = '(?P<call>[\w\-]+)\s*(?P<options>\{.*?\})?';
		
		$statementSubpatterns['snippet'] = 	Regex::$outerStatementMarker . '\s*' . //Note the additional preparsed marker!
							'snippet\s+(?P<snippet>[\w\-]+)' .
							'\s*' . $statementClose . 
							'(?P<snippetSnippet>.*?)' . 
							$statementOpen . Regex::$outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
		
		$statementSubpatterns['with'] = Regex::$outerStatementMarker . '\s*' . // Note the additional preparsed marker!
						'with\s+(?P<with>' .
							'"[^"]*"|' . "'[^']*'|" . $var . '|prev|next' .
						')' . 
						'\s*' . $statementClose . 
						'(?P<withSnippet>.*?)' . 	
						'(?:' . $statementOpen . Regex::$outerStatementMarker . '\s*else\s*' . $statementClose . '(?P<withElseSnippet>.*?)' . ')?' . // Note the additional preparsed marker!	
						$statementOpen . Regex::$outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
		
		$statementSubpatterns['loop'] = Regex::$outerStatementMarker . '\s*' .	// Note the additional preparsed marker!
						'foreach\s+in\s+(?P<foreach>' . 
							'pagelist|' . 
							'filters|' . 
							'tags|' . 
							'filelist|' .
							'"[^"]*"|' . "'[^']*'|" . $var . 		
						')' . 
						'\s*' . $statementClose . 
						'(?P<foreachSnippet>.*?)' . 
						'(?:' . $statementOpen . Regex::$outerStatementMarker . '\s*else\s*' . $statementClose . '(?P<foreachElseSnippet>.*?)' . ')?'. // Note the additional preparsed marker!
						$statementOpen . Regex::$outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
		
		$statementSubpatterns['condition'] = 	Regex::$outerStatementMarker . '\s*' .	// Note the additional preparsed marker!
							'if\s+(?P<if>' .  
								'(?P<ifBoolean>' .
									'(?P<ifNot>!)?' . '(?<ifVar>' . $var . ')' .
								')|' . 	
								'(?P<ifComparison>' . 
									'(?P<ifLeft>' . 
										'(?P<ifLeftQuote>[\'"])(?P<ifLeftQuotedString>.*?[^\\\\])?\k<ifLeftQuote>' . '|(?<ifLeftVar>' . $var . ')|(?P<ifLeftNumber>[\d\.]+)' . 
									')' .	
									'\s*(?P<ifOperator>!?=|>=?|<=?)\s*' . 		
									'(?P<ifRight>' . 
										'(?P<ifRightQuote>[\'"])(?P<ifRightQuotedString>.*?[^\\\\])?\k<ifRightQuote>' . '|(?<ifRightVar>' . $var . ')|(?P<ifRightNumber>[\d\.]+)' .
									')' . 
								')' .				
							')' . 	
							'\s*' . $statementClose . 
							'(?P<ifSnippet>.*?)' . 
							'(?:' . $statementOpen . Regex::$outerStatementMarker . '\s*else\s*' . $statementClose . '(?P<ifElseSnippet>.*?)' . ')?' . // Note the additional preparsed marker!	
							$statementOpen . Regex::$outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
		
		// (variable | statements)		
		return '((?P<var>' . $var . ')|' . $statementOpen . '\s*(?:' . implode('|', $statementSubpatterns) . ')\s*' . $statementClose . ')'; 
			
	}


	/**
	 *	Return the regex for the string functions of content variables.     
	 *	Like: | name (parameters)
	 *
	 *	@param string $namedReferencePrefix
	 *	@return The regex to match functions and their parameters
	 */
	
	public static function stringFunction($namedReferencePrefix = false) {
		
		if ($namedReferencePrefix) {
			$name = 	'?P<' . $namedReferencePrefix . 'Name>';
			$parameters =	'?P<' . $namedReferencePrefix . 'Parameters>';
		} else {
			$name = '';
			$parameters = '';
		}
		
		// Parameter pattern. Quoted strings (double or single quotes are allowed) or boolean/number values.
		$regexParameter = '\s*(?:"(?:[^"\\\\]|\\\\.)*"|\'(?:[^\'\\\\]|\\\\.)*\'|\w*)\s*';
		
		return	'\|' . 
			// Function name.
			'\s*(' . $name . '[\w\-]+)\s*' .
			// Parameters. 
			'(\(' . 
				'(' . $parameters . $regexParameter . '(?:,' . $regexParameter . ')*?)' . 
			'\)\s*)?';
			
	}
	
	
	/**
	 *	Return the regex for content variables. A prefix can be defined to create named backreferences for each capturing group.
	 *	Like: {[ var | function1 (...) | function2 (...) | ... ]}
	 *
	 *	@param string $namedReferencePrefix
	 *	@return The regex to match variables.
	 */
	
	public static function contentVariable($namedReferencePrefix = false) {
		
		if ($namedReferencePrefix) {
			$name = 	'?P<' . $namedReferencePrefix . 'Name>';
			$functions =	'?P<' . $namedReferencePrefix . 'Functions>';
		} else {
			$name = '';
			$functions = '';
		}
		
		return 	preg_quote(AM_DEL_VAR_OPEN) . '\s*(' . $name . Regex::$charClassAllVariables . '+)\s*' . '(' . $functions . '(' . Regex::stringFunction() . ')*)' . preg_quote(AM_DEL_VAR_CLOSE);
		
	}
	
	
}


?>