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
 *	Copyright (c) 2014 by Marc Anton Dahmen
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
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Template {
	
	
	/**
	 * 	The Automad object.
	 */
	
	private $Automad;
	
	
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
		if ($Page->data[AM_KEY_URL] != AM_REQUEST) {
			header('Location: ' . Resolve::url($Page, $Page->url));
			die;
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
		
		ob_start();
		@include $file;
		$output = ob_get_contents();
		ob_end_clean();
		
		// Backwards compatibility.
		$output = str_replace(array('@s(', '@p(', '@i(', '@t(', '@x('), array('$(', '$(', '@(', '@(', '@('), $output);
		
		return $output;
		
	}


	/**
	 * 	Preprocess snippet delimiters. 
	 *	
	 *	To enable recursive snippets, first $str has to be preparsed to identify the outer wrapping snippet (depth 0).    
	 *	The outer wrapping delimiters [[ ... ]] get doubled [[[[ ... ]]]] to be easily matched in the later regex. 
	 *
	 *	@param $str
	 *	@return the preprocessed $str where all outer snippet delimiters are doubled to be easily identified.
	 */

	private function preProcessSnippetDelimiters($str) {
		
		$depth = 0;
		
		return 	preg_replace_callback('/(' . preg_quote(AM_SNIPPET_OPEN) . '|' . preg_quote(AM_SNIPPET_CLOSE) . ')/s', function($match) use (&$depth) {
			
				$delimiter = $match[1];
				
				if ($delimiter == AM_SNIPPET_OPEN) {
			
					if ($depth === 0) {
						$delimiter .= $delimiter;
					}
				
					$depth++;
					
				} else {
				
					$depth--;
				
					if ($depth === 0) {
						$delimiter .= $delimiter;
					}
				
				}
			
				return $delimiter;
			
			}, $str);
		
	}


	/**
	 *	Find and replace all variables with values from either the current page data array or, if not defined there, from the site data array 
	 *	or - only those starting with a "?" - from the $_REQUEST array.    
	 *	When matching $( var ) the parser will check first the page data and then the site data.      
	 *	When matching $( ?var ), the parser will only check the $_REQUEST array.      
	 *	By first checking the page data, basically all site data variables can be easily overridden by a page. 
	 *	Optionally all values can be parsed as "JSON safe", by escaping all quotes.
	 *	In case a variable is used as an option value for any method and is not within a string, that variable doesn't need to be 
	 *	wrapped in double quotes to work within the JSON string - the double quotes get added automatically.
	 *
	 *	@param string $str
	 *	@param boolean $escape 
	 *	@return The processed $str
	 */

	private function processContent($str, $escape = false) {
		
		return 	preg_replace_callback(AM_REGEX_VAR, function($matches) use ($escape) {
					
				/*
				
				Possible items in $matches:
				
				0:	Full match
				1:	Normal variable in any other context
				2:	Variable is a method paramter without beeing wrapped in double quotes.
					The regex will match $(var) only if there is a ":" before and a "," or a "}" after the variable (whitespace is allowed),
					like: @( img { file: @(file) })
				 
				*/
				
				// Use the last item in the array to get the requested value. If $matches[2] only exists, if $matches[1] is empty. Either [1] or [2] will return the matched key.
				// The distinction between $matches[1] and $matches[2] is only made to check, if $value must be wrapped in quotes (see below).
				$value = $this->Automad->getValue(end($matches));
				
				// In case $value will be used as option, some chars have to be escaped to work within a JSON formatted string.
				if ($escape) {
					$value = Parse::jsonEscape($value);	
				}
				
				// In case the variable is an "stand-alone" value in a JSON formatted string ($matches[2] will be defined then - regex ": $(var) ,|}" ), 
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
	 *	For example @(file.php), @(method{ options }), @(foreach in ... [[ ... ]]) or @(if !$(var) [[ ... ]] else [[ ... ]]).   
	 *	Inside a "foreach in list" loop the context changes with each iteration and the active page in the loop becomes the current page.    
	 *	Therefore all variables of the active page in the loop can be accessed using the standard template syntax like $( var ).
	 *	Inside other loops there are special variables available to be used within a snippet: $( :filter ), $( :tag ), $( :file ) and $( :basename ).
	 *
	 *	@param string $str - The string to be parsed
	 *	@param string $directory - The directory of the currently included file/template
	 *	@return the processed string	
	 */

	private function processStatements($str, $directory) {
	
		// Identify the outer snippet delimiters.
		$str = $this->preProcessSnippetDelimiters($str);
	
		return 	preg_replace_callback(AM_REGEX_STATEMENT, function($matches) use ($directory) {			
					
				/*
				
				The $matches array can have the following elements:
			
				0:	The full matched string
			
				Includes
				1:	The filename to include
			
				Methods
				2:	Method name
				3:	Optional JSON formatted options
				
				Foreach loop 
				4:	The array/type of content to iterate over
				5:	A file pattern - the foreach loop will iterate over a set of files matching that pattern
				6:	A variable defining a file pattern (basically the same like [5])
				7:	The snippet for each item to be used within the loop
				
				Conditions
				8:	Optional "!" for a boolean condition
				9:	The variable to test in a boolean condition
				10:	The left side of an equation (string)
				11:	The left side of an equation (variable)
				12:	Optional "!" for an equation (a != b)
				13:	The right side of an equation (string)
				14:	The right side of an equation (variable)
				15:	The "if" snippet
				16:	The "else" snippet
				
				*/
				
				// Include
				if (!empty($matches[1])) {
				
					// Include
					Debug::log('Template: Statements: Matched include "' . $matches[1] . '"');
					$file = $directory . '/' . $matches[1];
				
					if (file_exists($file)) {
						Debug::log('Template: Statements: Including "' . $file . '"');	
						return $this->processStatements($this->loadTemplate($file), dirname($file));
					} else {
						Debug::log('Template: Statements: File "' . $file . '" not found!');
					}
					
				} 
				
				// Method (Toolbox or extension)
				if (!empty($matches[2])) {
					
					Debug::log('Template: Statements: Matched method "' . $matches[2] . '"');
					$method = $matches[2];
				
					// Check if options exist.
					if (isset($matches[3])) {
						// Parse the options JSON and also find and replace included variables within the JSON string.
						$options = Parse::jsonOptions($this->processContent($matches[3], true));
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
						Debug::log('Template: Statements: Method "' . $matches[2] . '" is not a core method. Will look for a matching extension ...');
						return Extension::call($method, $options, $this->Automad);
					}
						
				}
			
				// Foreach loop
				if (!empty($matches[4]) && !empty($matches[7])) {
			
					$type = $matches[4];
					$snippet = $matches[7];
					$html = '';
					
					// Pages
					if ($type == 'list') {
						
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
						
					}	
					
					// Filters (tags of the pages in the listing)
					// Each filter can be used as $( :filter ) within a snippet
					if ($type == 'filters') {
				
						foreach ($this->Automad->getListing()->getTags() as $filter) {
							Debug::log('Template: Statements: Processing snippet in loop for filter "' . $filter . '"');
							// Store current filter in the data array to be picked up by templateVariables().
							$this->Automad->getCurrentPage()->data[AM_KEY_FILTER] = $filter;
							$html .= $this->processMarkup($snippet, $directory);
						}
						
						unset($this->Automad->getCurrentPage()->data[AM_KEY_FILTER]);
						
					}
					
					// Tags (of the current page)	
					// Each tag can be used as $( :tag ) within a snippet
					if ($type == 'tags') {
				
						foreach ($this->Automad->getCurrentPage()->tags as $tag) {
							Debug::log('Template: Statements: Processing snippet in loop for tag "' . $tag . '"');
							// Store current tag in the data array to be picked up by templateVariables().
							$this->Automad->getCurrentPage()->data[AM_KEY_TAG] = $tag;
							$html .= $this->processMarkup($snippet, $directory);
						}
						
						unset($this->Automad->getCurrentPage()->data[AM_KEY_TAG]);
							
					}	
					
					// Files
					// Find matching files, in case a string (including variables) is the given argument for the foreach loop.
					if (!empty($matches[5])) {
						// Also parse possible variables within the string.
						$files = Parse::fileDeclaration($this->processContent($matches[5]), $this->Automad->getCurrentPage());
					}
					
					// Find matching files, in case a variable is the given argument for the foreach loop. 
					if (!empty($matches[6])) {
						$files = Parse::fileDeclaration($this->Automad->getValue($matches[6]), $this->Automad->getCurrentPage());	
					}
					
					// The full file path and the basename can be used like $( :file ) and $( :basename ) within a snippet.	
					if (!empty($files)) {
						
						foreach ($files as $file) {
							Debug::log('Template: Statements: Processing snippet in loop for file "' . $file . '"');
							// Store current filename and its basename in the data array to be picked up by templateVariables().
							$this->Automad->getCurrentPage()->data[AM_KEY_FILE] = str_replace(AM_BASE_DIR, '', $file);
							$this->Automad->getCurrentPage()->data[AM_KEY_BASENAME] = basename($file);
							$html .= $this->processMarkup($snippet, $directory);
						}
						
						unset($this->Automad->getCurrentPage()->data[AM_KEY_FILE]);
						unset($this->Automad->getCurrentPage()->data[AM_KEY_BASENAME]);
						
					}
									
					return $html;
						
				}
			
				// Boolean condition
				if (!empty($matches[9]) && !empty($matches[15])) {
					
					// If EMPTY NOT == NOT EMPTY Value.
					if (empty($matches[8]) == !empty($this->Automad->getValue($matches[9]))) {
						
						Debug::log('Template: Statements: Evaluating boolean condition: "' . $matches[8] . '$(' . $matches[9] . ')" > TRUE');
						return $this->processMarkup($matches[15], $directory);
						
					} else {
						
						Debug::log('Template: Statements: Evaluating boolean condition: "' . $matches[8] . '$(' . $matches[9] . ')" > FALSE');
						
						if (!empty($matches[16])) {
							return $this->processMarkup($matches[16], $directory);
						}
						
					}
					
				}
				
				// Equation
				if ((!empty($matches[10]) || !empty($matches[11])) && (!empty($matches[13]) || !empty($matches[14])) && !empty($matches[15])) {
					
					// Get left side.
					if (!empty($matches[10])) {
						$left = $this->processContent($matches[10]);
					} else {
						$left = $this->Automad->getValue($matches[11]);
					}
				
					// Get right side.
					if (!empty($matches[13])) {
						$right = $this->processContent($matches[13]);
					} else {
						$right = $this->Automad->getValue($matches[14]);
					}
					
					// If EMPTY NOT == (LEFT == RIGHT).
					if (empty($matches[12]) == ($left == $right)) {
					
						// True
						Debug::log('Template: Statements: Evaluating equation: "' . $left . ' ' . $matches[12] . '= ' . $right . '" > TRUE');
						return $this->processMarkup($matches[15], $directory);
					
					} else {
					
						// False
						Debug::log('Template: Statements: Evaluating equation: "' . $left . ' ' . $matches[12] . '= ' . $right . '" > FALSE');
					
						if (!empty($matches[16])) {
							return $this->processMarkup($matches[16], $directory);
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
