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
 *	Copyright (c) 2015 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 * 	The Template class holds all methods to render the current page using a template file.
 *	
 *	When render() is called, first the template file gets loaded by loadTemplate().
 *	The output, basically the raw template HTML (including the generated HTML by PHP in the template file) 
 *	gets stored in $output.
 *
 *	In a second step all statements and content in $output gets processed. 
 *	
 *	That way, it is possible that the template.php file can include HTML as well as PHP, while the "user-generated" content in the text files 
 *	can not have any executable code (PHP). There are no "eval" functions needed, since all the PHP gets only included from the template files,
 *	which should not be edited by users anyway.
 *
 *	In a last step, all URLs within the generated HTML get resolved to be relative to the server's root (or absolute), before $output gets returned.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2015 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Template {
	
	
	/**
	 * 	The Automad object.
	 */
	
	private $Automad;
	
	
	/**
	 * 	The outer statement marker helps to distinguish all outer wrapping statements from the inner statements.
	 */
	
	private $outerStatementMarker = '#';
	
	
	/**
	 * 	The Toolbox object.
	 */
	
	private $Toolbox;
	
	
	/**
	 *	The template file for the current page.
	 */
	
	private $template;
	
	
	/**
	 *	Define $Automad and $Page, check if the page gets redirected and get the template name. 
	 */
	
	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		$this->Toolbox = new Toolbox($Automad);
		$Page = $Automad->getCurrentPage();
		
		// Redirect page, if the defined URL variable differs from AM_REQUEST.
		if (!empty($Page->url)) {
			if ($Page->url != AM_REQUEST) {
				header('Location: ' . Resolve::url($Page, $Page->url));
				die;
			}
		}
		
		$this->template = $Page->getTemplate();
		
		Debug::log('Template: New instance created!');
		Debug::log('Template: Current Page:');
		Debug::log($Page);
		
	}
	

	/**
	 *	Add Meta tags to the head of $str.
	 *
	 *	@param string $str
	 *	@return $str
	 */
	
	private function addMetaTags($str) {
		
		$meta =  "\n\t" . '<meta name="Generator" content="Automad ' . AM_VERSION . '">';
		
		return str_replace('<head>', '<head>' . $meta, $str);
		
	}
	

	/**
	 * 	Convert legacy template syntax into the new syntax.
	 *	
	 *	@param string $str
	 *	@return The converted template
	 */

	private function convertLegacy($str) {
		
		$str = preg_replace('/@i\(\s*([\w\/\.\-]+\.php)\s*\)/s', AM_DEL_STATEMENT_OPEN . '$1' . AM_DEL_STATEMENT_CLOSE, $str);
		$str = preg_replace('/@(s|p)\(\s*(' . AM_CHARCLASS_VAR_ALL . '+)\s*\)/s', AM_DEL_VAR_OPEN . '$2' . AM_DEL_VAR_CLOSE, $str);
		$str = preg_replace('/@(t|x)\(\s*(' . AM_CHARCLASS_VAR_ALL . '+)\s*(\{.*?\})?\s*\)/s', AM_DEL_STATEMENT_OPEN . '$2$3' . AM_DEL_STATEMENT_CLOSE, $str);
		
		return $str;
		
	}


	/**
	 *	Load and buffer a template file and return its content as string. The Automad object gets passed as parameter to be available for all plain PHP within the included file.
	 *	This is basically the base method to load a template without parsing the Automad markup. It just gets the parsed PHP content.
	 *
	 *	Note that even when the it is possible to use plain PHP in a template file, all that code will be parsed first when buffering, before any of the Automad markup is getting parsed.
	 *	That means also, that is not possible to make plain PHP code really interact with any of the Automad placeholder markup.
	 *
	 *	@param string $file
	 *	@return the buffered output 
	 */

	private function loadTemplate($file) {
		
		// Provide an interface to the Automad object for the templates to be used with plain PHP.
		$Automad = $this->Automad;
		
		ob_start();
		include $file;
		$output = ob_get_contents();
		ob_end_clean();
		
		// Backwards compatibility.
		$output = $this->convertLegacy($output);
		
		// Strip comments.
		$output = preg_replace('/(' . preg_quote(AM_DEL_COMMENT_OPEN) . '.*?' . preg_quote(AM_DEL_COMMENT_CLOSE) . ')/s', '', $output);
				
		return $output;
		
	}


	/**
	 * 	Preprocess recursive statements to identify the top-level (outer) statements within a parsed string. 
	 *
	 *	@param $str
	 *	@return The preprocessed $str where all outer opening statement delimiters get an additional marker appended.
	 */

	private function preProcessRecursiveStatements($str) {
		
		$depth = 0;
		$regex = 	'/(' . 
				'(?P<begin>' . preg_quote(AM_DEL_STATEMENT_OPEN) . '\s*(?:if|foreach).*?' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')|' .
				'(?P<else>' . preg_quote(AM_DEL_STATEMENT_OPEN) . '\s*else\s*' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')|' .
				'(?P<end>' . preg_quote(AM_DEL_STATEMENT_OPEN) . '\s*end\s*' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')' .
				')/s';
		
		return 	preg_replace_callback($regex, function($match) use (&$depth) {
						
				// Convert $match to the actually needed string.
				$return = array_unique($match);
				$return = array_filter($return);				
				$return = implode($return);
				
				// Decrease depth in case the match is else or end.
				if (!empty($match['end']) || !empty($match['else'])) {
					$depth--;
				}
				
				// Append a marker to the opening delimiter in case depth === 0.
				if ($depth === 0) {
					$return = str_replace(AM_DEL_STATEMENT_OPEN, AM_DEL_STATEMENT_OPEN . $this->outerStatementMarker, $return);
				} 
				
				// Increase depth after (!) return was possible modified (in case depth === 0) in case the match is begin or else.
				if (!empty($match['begin']) || !empty($match['else'])) {
					$depth++;
				}
							
				return $return;
			
			}, $str);
		
	}


	/**
	 *	Find and replace all variables with values from either the current page data array or, if not defined there, from the site data array 
	 *	or - only those starting with a "?" - from the $_GET array.    
	 *	When matching {[ var ]} the parser will check first the page data and then the site data.      
	 *	When matching {[ ?var ]}, the parser will only check the $_GET array.      
	 *	By first checking the page data, basically all site data variables can be easily overridden by a page. 
	 *	Optionally all values can be parsed as "JSON safe", by escaping all quotes.
	 *	In case a variable is used as an option value for any method and is not part of a string, that variable doesn't need to be 
	 *	wrapped in double quotes to work within the JSON string - the double quotes get added automatically.
	 *
	 *	@param string $str
	 *	@param boolean $escape 
	 *	@return The processed $str
	 */

	private function processContent($str, $escape = false) {
		
		$subpatternVar = preg_quote(AM_DEL_VAR_OPEN) . '\s*(' . AM_CHARCLASS_VAR_ALL . '+)\s*' . preg_quote(AM_DEL_VAR_CLOSE);
		$regexContent = '/(?:' . 
				// Simple variable {[var]}
				$subpatternVar . '|' .	
				// a variable as method parameter while being not wrapped in quotes ": {[var]} ,|}"				
				'(?<=\:)\s*' . $subpatternVar . '\s*(?=,|\})' . 				
				')/s';
		
		return 	preg_replace_callback($regexContent, function($matches) use ($escape) {
					
				/*
				
				Possible items in $matches:
				
				0:	Full match
				1:	Normal variable in any other context
				2:	Variable is a method paramter without beeing wrapped in double quotes.
					The regex will match {[ var ]} only if there is a ":" before and a "," or a "}" after the variable (whitespace is allowed),
					like: {@ img { file: {[ file ]} } @}
				 
				*/
				
				// Use the last item in the array to get the requested value. If $matches[2] only exists, if $matches[1] is empty. Either [1] or [2] will return the matched key.
				// The distinction between $matches[1] and $matches[2] is only made to check, if $value must be wrapped in quotes (see below).
				$value = $this->Automad->getValue(end($matches));
				
				// In case $value will be used as option, some chars have to be escaped to work within a JSON formatted string.
				if ($escape) {
					$value = Parse::jsonEscape($value);	
				}
				
				// In case the variable is an "stand-alone" value in a JSON formatted string ($matches[2] will be defined then - regex ": {[ var ]} ,|}" ), 
				// it has to be wrapped in double quotes.
				if (!empty($matches[2])) {
					$value = '"' . $value . '"';
				}
									
				return $value;
							
			}, $str);
		
	}


	/**
	 * 	Process all statements and content within a markup string.
	 *
	 *	@param string $str
	 *	@param string $directory
	 *	@return the fully processed markup
	 */

	private function processMarkup($str, $directory) {
		
		return $this->processContent($this->processStatements($str, $directory));
		
	}


	/**
	 * 	Call Toolbox methods, call Extensions, execute statements (loops and conditions) and include template elements recursively.     
	 *	For example {@ file.php @}, {@ method{ options } @}, {@ foreach in ... @} ... {@ end @} or {@ if {[ var ]} @} ... {@ else @} ... {@ end @}.   
	 *	Inside a "foreach in pagelist" loop the context changes with each iteration and the active page in the loop becomes the current page.    
	 *	Therefore all variables of the active page in the loop can be accessed using the standard template syntax like $( var ).
	 *	Inside other loops there are special variables available to be used within a snippet: {[ :filter ]}, {[ :tag ]}, {[ :file ]} and {[ :basename ]}.
	 *
	 *	@param string $str - The string to be parsed
	 *	@param string $directory - The directory of the currently included file/template
	 *	@return the processed string	
	 */

	private function processStatements($str, $directory) {
	
		// Identify the outer statements.
		$str = $this->preProcessRecursiveStatements($str);
	
		$var = preg_quote(AM_DEL_VAR_OPEN) . '\s*' . AM_CHARCLASS_VAR_ALL . '+\s*' . preg_quote(AM_DEL_VAR_CLOSE);		
		$open = preg_quote(AM_DEL_STATEMENT_OPEN);
		$close = preg_quote(AM_DEL_STATEMENT_CLOSE);
		
		// The subpatterns don't include the wrapping delimiter: "{@ subpattern @}".
		$subpatterns['include'] = '(?P<file>[\w\/\-\.]+\.php)';
		
		$subpatterns['method'] = '(?P<method>[\w\-]+)\s*(?P<options>\{.*?\})?';
		
		$subpatterns['loop'] = 	$this->outerStatementMarker . '\s*' .	// Note the additional preparsed marker!
					'foreach\s+in\s+(?P<foreach>' . 
					'pagelist|' . 
					'filters|' . 
					'tags|' . 
					'filelist|' .
					'"(?P<foreachInDoubleQuotes>[^"]*)"|' . 
					"'(?P<foreachInSingleQuotes>[^']*)'" . 
					'|(?P<foreachInVar>' . $var . ')' .
					')' . 
					'\s*' . $close . 
					'(?P<foreachSnippet>.*?)' . 
					$open . $this->outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
		
		$subpatterns['condition'] = 	$this->outerStatementMarker . '\s*' .	// Note the additional preparsed marker!
						'if\s+(?P<condition>' . 
							
						// Boolean
						'(?P<ifNot>!)?(?<ifVar>' . $var . ')|' .
								
						// Comparison
						// Left
						'(?:"(?P<ifLeftDoubleQuotes>[^"]*)"|' . "'(?P<ifLeftSingleQuotes>[^']*)'" . '|(?P<ifLeftVar>' . $var . ')|(?P<ifLeftNumber>[\d\.]+))' .
						// !=
						'\s*(?P<ifOperator>!?=|>=?|<=?)\s*' .
						// Right
						'(?:"(?P<ifRightDoubleQuotes>[^"]*)"|' . "'(?P<ifRightSingleQuotes>[^']*)'" . '|(?P<ifRightVar>' . $var . ')|(?P<ifRightNumber>[\d\.]+))' .
						
						')' . 
						'\s*' . $close . 
						'(?P<ifSnippet>.*?)' . 
						'(?:' . $open . $this->outerStatementMarker . '\s*else\s*' . $close . '(?P<elseSnippet>.*?)' . ')?' . // Note the additional preparsed marker!	
						$open . $this->outerStatementMarker . '\s*end'; // Note the additional preparsed marker!
				
		$regexStatement = '/' . $open . '\s*(?:' . implode('|', $subpatterns) . ')\s*' . $close . '/s'; 
			
		return 	preg_replace_callback($regexStatement, function($matches) use ($directory) {	
							
				// Include
				if (!empty($matches['file'])) {
					
					Debug::log('Template: Statements: Matched include "' . $matches['file'] . '"');
					$file = $directory . '/' . $matches['file'];
				
					if (file_exists($file)) {
						Debug::log('Template: Statements: Including "' . $file . '"');	
						return $this->processMarkup($this->loadTemplate($file), dirname($file));
					} else {
						Debug::log('Template: Statements: File "' . $file . '" not found!');
					}
						
				}
				
				// Method (Toolbox or extension)
				if (!empty($matches['method'])) {
					
					$method = $matches['method'];
					Debug::log('Template: Statements: Matched method "' . $method . '"');
					
					// Check if options exist.
					if (isset($matches['options'])) {
						// Parse the options JSON and also find and replace included variables within the JSON string.
						$options = Parse::jsonOptions($this->processContent($matches['options'], true));
					} else {
						$options = array();
					}
					
					// Call method.
					if (method_exists($this->Toolbox, $method)) {
						// Try calling a matching toolbox method. 
						Debug::log('Template: Statements: Calling method: "' . $method . '" and passing the following options: ' . "\n" . var_export($options, true));	
						return $this->Toolbox->$method($options);
					} else {
						// Try extension, if no toolbox method was found.
						Debug::log('Template: Statements: Method "' . $method . '" is not a core method. Will look for a matching extension ...');
						return Extension::call($method, $options, $this->Automad);
					}
					
				}
				
				// Foreach loop
				if (!empty($matches['foreach'])) {
						
					$snippet = $matches['foreachSnippet'];
					$html = '';
				
					if ($matches['foreach'] == 'pagelist') {
						
						// Pagelist
						
						// Get pages.
						$pages = $this->Automad->getListing()->getPages();
					
						// Save context.
						$context = $this->Automad->getContext();
					
						foreach (array_keys($pages) as $url) {
							Debug::log('Template: Statements: Processing snippet in loop for page "' . $url . '"');
							// Set context to the current page in the loop.
							$this->Automad->setContext($url);
							// Parse snippet.
							$html .= $this->processMarkup($snippet, $directory);
						}
		
						// Restore context.
						$this->Automad->setContext($context);
							
					} else if ($matches['foreach'] == 'filters') {
						
						// Filters (tags of the pages in the listing)
						// Each filter can be used as $( :filter ) within a snippet.
						
						foreach ($this->Automad->getListing()->getTags() as $filter) {
							Debug::log('Template: Statements: Processing snippet in loop for filter "' . $filter . '"');
							// Store current filter in the data array to be picked up by templateVariables().
							$this->Automad->getCurrentPage()->data[AM_KEY_FILTER] = $filter;
							$html .= $this->processMarkup($snippet, $directory);
						}
						
						unset($this->Automad->getCurrentPage()->data[AM_KEY_FILTER]);
							
					} else if ($matches['foreach'] == 'tags') {

						// Tags (of the current page)	
						// Each tag can be used as $( :tag ) within a snippet.

						foreach ($this->Automad->getCurrentPage()->tags as $tag) {
							Debug::log('Template: Statements: Processing snippet in loop for tag "' . $tag . '"');
							// Store current tag in the data array to be picked up by templateVariables().
							$this->Automad->getCurrentPage()->data[AM_KEY_TAG] = $tag;
							$html .= $this->processMarkup($snippet, $directory);
						}
						
						unset($this->Automad->getCurrentPage()->data[AM_KEY_TAG]);
	
					} else {
						
						// Files
						// The file path and the basename can be used like $( :file ) and $( :basename ) within a snippet.
						
						if ($matches['foreach'] == 'filelist') {
							// Use files from filelist.
							$files = $this->Automad->getFilelist()->getFiles();
						} else {
							// Merge and parse given glob pattern within any kind of quotes or from a variable value. 
							// Only one of the following matches is not an empty string, but all three are always defined. Therefore they can simply get parsed as one concatenated string. 
							$files = Parse::fileDeclaration($this->processContent($matches['foreachInDoubleQuotes'] . $matches['foreachInSingleQuotes'] . $matches['foreachInVar']), $this->Automad->getCurrentPage(), true);
						}
						
						foreach ($files as $file) {
							Debug::log('Template: Statements: Processing snippet in loop for file "' . $file . '"');
							// Store current filename and its basename in the data array to be picked up by templateVariables().
							$this->Automad->getCurrentPage()->data[AM_KEY_FILE] = $file;
							$this->Automad->getCurrentPage()->data[AM_KEY_BASENAME] = basename($file);
							$html .= $this->processMarkup($snippet, $directory);
						}
						
						unset($this->Automad->getCurrentPage()->data[AM_KEY_FILE]);
						unset($this->Automad->getCurrentPage()->data[AM_KEY_BASENAME]);
							
					}
					
					return $html;
					
				}
				
				// Condition
				if (!empty($matches['condition'])) {
								
					$ifSnippet = $matches['ifSnippet'];
					$elseSnippet = '';
					
					if (!empty($matches['elseSnippet'])) {
						$elseSnippet = $matches['elseSnippet'];
					} 
					
					if (!empty($matches['ifVar'])) {
					
						// Boolean condition.
						
						// Get the value of the given variable.
						$ifVar = $this->processContent($matches['ifVar']);
						
						// If EMPTY NOT == NOT EMPTY Value.
						if (empty($matches['ifNot']) == !empty($ifVar)) {
							Debug::log('Template: Statements: Evaluating boolean condition: "' . $matches['ifNot'] . '$(' . $matches['ifVar'] . ')" > TRUE');
							return $this->processMarkup($ifSnippet, $directory);
						} else {
							Debug::log('Template: Statements: Evaluating boolean condition: "' . $matches['ifNot'] . '$(' . $matches['ifVar'] . ')" > FALSE');
							return $this->processMarkup($elseSnippet, $directory);
						}
					
					} else {
						
						// Comparison.
						
						// Parse both sides of the condition. Again, all possible matches for each side can get merged in to one string, since there will be only one item for left/right not empty.
						$left = $this->processContent($matches['ifLeftDoubleQuotes'] . $matches['ifLeftSingleQuotes'] . $matches['ifLeftVar'] . $matches['ifLeftNumber']);
						$right = $this->processContent($matches['ifRightDoubleQuotes'] . $matches['ifRightSingleQuotes'] . $matches['ifRightVar'] . $matches['ifRightNumber']);
						
						// Build the expression.
						switch ($matches['ifOperator']) {
							
							case '=':
								$expression = ($left == $right);
								break;
							
							case '!=':
								$expression = ($left != $right);
								break;
								
							case '>':
								$expression = ($left > $right);
								break;
								
							case '>=':
								$expression = ($left >= $right);
								break;
								
							case '<':
								$expression = ($left < $right);
								break;
								
							case '<=':
								$expression = ($left <= $right);
								break;
							
						}
						
						// Evaluate the expression.
						if ($expression) {
							Debug::log('Template: Statements: Evaluating condition: "' . $left . $matches['ifOperator'] . $right . '" is TRUE');
							return $this->processMarkup($ifSnippet, $directory);
						} else {
							Debug::log('Template: Statements: Evaluating condition: "' . $left . $matches['ifOperator'] . $right . '" is FALSE');
							return $this->processMarkup($elseSnippet, $directory);
						}
						
					}
						
				}
				
			}, $str);
		
	}
	
		
	/**
	 *	Find all links/URLs in $str and resolve the matches according to their type.
	 *	
	 *	@param string $str
	 *	@return $str
	 */
	
	private function resolveUrls($str) {
		
		$Page = $this->Automad->getCurrentPage();
		
		// action, href and src
		$str = 	preg_replace_callback('/(action|href|src)="(.+?)"/', function($match) use ($Page) {
				return $match[1] . '="' . Resolve::url($Page, $match[2]) . '"';
			}, $str);
				
		// Inline styles (like background-image)
		$str = 	preg_replace_callback('/url\(\'(.+?)\'\)/', function($match) use ($Page) {
				return 'url(\'' . Resolve::url($Page, $match[1]) . '\')';
			}, $str);
	
		return $str;
		
	}
	
	
	/**
	 *	Obfuscate all eMail addresses matched in $str.
	 *	
	 *	@param string $str
	 *	@return $str
	 */
	
	private function obfuscateEmails($str) {
		
		return 	preg_replace_callback('/(?<!mailto:)\b([\w\d\._\+\-]+@([a-zA-Z_\-\.]+)\.[a-zA-Z]{2,6})/', function($matches) {
				
				Debug::log('Template: Obfuscating: ' . $matches[1]);
					
				$html = '<a href="#" onclick="this.href=\'mailto:\'+ this.innerHTML.split(\'\').reverse().join(\'\')" style="unicode-bidi:bidi-override;direction:rtl">';
				$html .= strrev($matches[1]);
				$html .= "</a>&#x200E;";
		
				return $html;
					
			}, $str);
						
	}
		
	
	/**
	 * 	Render the current page.
	 *
	 *	@return The fully rendered HTML for the current page.
	 */
	
	public function render() {
		
		Debug::log('Template: Render template: ' . $this->template);
		
		$output = $this->loadTemplate($this->template);
		$output = $this->processMarkup($output, dirname($this->template));
		$output = Extension::createAssetTags($output);
		$output = $this->addMetaTags($output);
		$output = $this->resolveUrls($output);	
		$output = $this->obfuscateEmails($output);
	
		return $output;	
		
	}	
		
	
}


?>
