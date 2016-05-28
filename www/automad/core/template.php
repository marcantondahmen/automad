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
 *	When render() is called, first the template file gets loaded.
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
	 * 	Multidimensional array of collected extension assets grouped by type (CSS/JS).
	 */
	
	private $extensionAssets = array();
	
	
	/**
	 * 	An array of snippets defined within a template.
	 */
	
	private $snippets = array();
	
	
	/**
	 * 	The Toolbox object.
	 */
	
	private $Toolbox;
	
	
	/**
	 *	The template file for the current page.
	 */
	
	private $template;
	
	
	/**
	 * 	Array holding all temporary independent system variables (those starting with a ":", like {[ :file ]}) being created in loops.
	 */
	
	private $independentSystemVars = array();
	
	
	/**
	 *	Define $Automad and $Page, check if the page gets redirected and get the template name. 
	 */
	
	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		$this->Toolbox = new Toolbox($Automad);
		$Page = $Automad->Context->get();
		
		// Redirect page, if the defined URL variable differs from AM_REQUEST.
		if (!empty($Page->url)) {
			if ($Page->url != AM_REQUEST) {
				header('Location: ' . Resolve::url($Page, $Page->url));
				die;
			}
		}
		
		$this->template = $Page->getTemplate();
		
		Debug::log($Page, 'New instance created for the current page');
		
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
	 * 	Create the HTML tags for each file in $this->extensionAssets and prepend them to the closing </head> tag.
	 *	
	 *	@param string $str
	 *	@return $str
	 */
	
	private function createExtensionAssetTags($str) {
		
		Debug::log($this->extensionAssets, 'Assets');
		
		$html = '';
		
		if (isset($this->extensionAssets['.css'])) {
			foreach ($this->extensionAssets['.css'] as $file) {
				$html .= "\t" . '<link type="text/css" rel="stylesheet" href="' . $file . '" />' . "\n";
				Debug::log($file, 'Created tag for');	
			}
		}
		
		if (isset($this->extensionAssets['.js'])) {
			foreach ($this->extensionAssets['.js'] as $file) {
				$html .= "\t" . '<script type="text/javascript" src="' . $file . '"></script>' . "\n";
				Debug::log($file, 'Created tag for');
			}
		}
		
		// Prepend all items ($html) to the closing </head> tag.
		return str_replace('</head>', $html . '</head>', $str);
		
	}


	/**
	 *	Merge given assets with $this->extensionAssets. 
	 *
	 *	The $this->extensionAssets array consists of two sub-arrays - $this->extensionAssets['.css'] and $this->extensionAssets['.js']. 
	 *	Therefore the $assets parameter must have the same structure to be merged successfully.
	 *
	 *	@param array $assets (Array containing two sub-arrays: $assets['.css'] and $assets['.js'])
	 */

	private function mergeExtensionAssets($assets) {
		
		// Make sure, $this->extensionAssets has a basic structure to enable merging new assets.
		$this->extensionAssets = array_merge(array('.css' => array(), '.js' => array()), $this->extensionAssets);
		
		foreach (array('.css', '.js') as $type) {
			
			if (!empty($assets[$type])) {
				$this->extensionAssets[$type] = array_merge($this->extensionAssets[$type], $assets[$type]);
			}
			
		}
		
	}

	
	/**
	 *	Return the requested independent system variable.
	 *	Independent system variables are all variables created by Automad at runtime and independent from the Context and Shared objects. 
	 *
	 *	@param string $var
	 *	@return the value of $var
	 */
	
	private function getIndependentSystemVar($var) {
		
		// Check whether $var is generated within a loop and therefore stored in $independentSystemVars or
		// if $var is related to the context, filelist or pagelist object.
		if (array_key_exists($var, $this->independentSystemVars)) {
			
			return $this->independentSystemVars[$var];
			
		} else {
			
			switch ($var) {
					
				case AM_KEY_FILELIST_COUNT:
					// The filelist count represents the number of files within the last defined filelist. 
					return count($this->Automad->getFilelist()->getFiles());
					
				case AM_KEY_PAGELIST_COUNT:
					// The pagelist count represents the number of pages within the last defined pagelist. 
					return count($this->Automad->getPagelist()->getPages());
					
				case AM_KEY_CAPTION:
					// Get the caption for the currently used ":file".
					// In case ":file" is "image.jpg", the parsed caption file is "image.jpg.caption" and the returned value is stored in ":caption".
					if (isset($this->independentSystemVars[AM_KEY_FILE])) {
						return Parse::caption(AM_BASE_DIR . $this->independentSystemVars[AM_KEY_FILE]);
					} else {
						return false;
					}
					
			}
				
		}
	
	}


	/**
	 *	Set a system variable.
	 *	
	 *	@param string $var
	 *	@param mixed $value
	 */

	private function setIndependentSystemVar($var, $value) {
		
		$this->independentSystemVars[$var] = $value;
		
	}


	/**
	 *	Check whether a requested $key represents an independent system variable.
	 *
	 *	@param string $key
	 *	@return boolean true/false
	 */

	private function isIndependentSystemVar($key) {
		
		$systemVarKeys = array_merge(array_keys($this->independentSystemVars), array(AM_KEY_FILELIST_COUNT, AM_KEY_PAGELIST_COUNT, AM_KEY_CAPTION));
		
		return (in_array($key, $systemVarKeys));
		
	}
	
	
	/**
	 *	Get the value of a given variable key depending on the current context - either from the page data, the system variables or from the $_GET array.
	 *
	 *	@param string $key
	 *	@return The value
	 */
	
	private function getValue($key) {
		
		if (strpos($key, '?') === 0) {
			
			// Query string parameter.
			$key = substr($key, 1);
			
			if (isset($_GET[$key])) {
				return htmlspecialchars($_GET[$key]);
			} 
	
		} else {
			
			if ($this->isIndependentSystemVar($key)) {
				// Independent system variable.
				return $this->getIndependentSystemVar($key);
			} else {
				// Page data and system variables depending on the current context.
				return $this->Automad->Context->get()->get($key);
			}
			
		}
			
	}


	/**
	 * 	Preprocess recursive statements to identify the top-level (outer) statements within a parsed string. 
	 *
	 *	@param $str
	 *	@return The preprocessed $str where all outer opening statement delimiters get an additional marker appended.
	 */

	private function preProcessWrappingStatements($str) {
		
		$depth = 0;
		$regex = 	'/(' . 
				'(?P<begin>' . preg_quote(AM_DEL_STATEMENT_OPEN) . '\s*(?:if|for|foreach|with|snippet).*?' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')|' .
				'(?P<else>' . preg_quote(AM_DEL_STATEMENT_OPEN) . '\s*else\s*' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')|' .
				'(?P<end>' . preg_quote(AM_DEL_STATEMENT_OPEN) . '\s*end\s*' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')' .
				')/is';
		
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
					$return = str_replace(AM_DEL_STATEMENT_OPEN, AM_DEL_STATEMENT_OPEN . Regex::$outerStatementMarker, $return);
				} 
				
				// Increase depth after (!) return was possible modified (in case depth === 0) in case the match is begin or else.
				if (!empty($match['begin']) || !empty($match['else'])) {
					$depth++;
				}
							
				return $return;
			
			}, $str);
		
	}


	/**
	 *	Process content variables and optional string functions. Like {[ var | function1 ( parameters ) | function2 | ... ]}	
	 *
	 *	Find and replace all variables within $str with values from either the context page data array or, if not defined there, from the shared data array, 
	 *	or from the $_GET array (only those starting with a "?").      
	 *   
	 *	By first checking the page data (handled by the Page class), basically all shared data variables can be easily overridden by a page. 
	 *	Optionally all values can be parsed as "JSON safe", by escaping all quotes and wrapping variable is quotes when needed.
	 *	In case a variable is used as an option value for a method and is not part of a string, that variable doesn't need to be 
	 *	wrapped in double quotes to work within the JSON string - the double quotes get added automatically.
	 *
	 *	@param string $str
	 *	@param boolean $isJsonString 
	 *	@return The processed $str
	 */

	private function processContent($str, $isJsonString = false) {
		
		// Build regex. Also match possible JSON elements like ":", "," and "}". They will be added to the output when returning the value if existing.
		$regexContent = '/(?P<parameterStart>:\s*)?' . Regex::variable('var') . '(?P<parameterEnd>\s*(,|\}))?/s';
				
		return 	preg_replace_callback($regexContent, function($matches) use ($isJsonString) {
				
				// Merge $matches with empty defaults to skip later checks whether an item exists.
				$matches = array_merge(array('parameterStart' => '', 'parameterEnd' => '', 'varFunctions' => ''), $matches);
						
				// Get the value.
				$value = $this->getValue($matches['varName']);
				
				// Modify $value by processing all matched string functions.
				$value = Pipe::process($value, $matches['varFunctions']);
				
				// In case $value will be used as an JSON option, some chars have to be escaped to work within a JSON formatted string.
				if ($isJsonString) {
					
					$value = String::jsonEscape($value);
					
					// In case the variable is an "stand-alone" value in a JSON formatted string (regex ": {[ var ]} (,|})" ), 
					// it has to be wrapped in double quotes.
					// In that case $matches['parameterStart'] and $matches['parameterEnd'] are not empty.
					if ($matches['parameterStart'] && $matches['parameterEnd']) {
						$value = '"' . $value . '"';
						Debug::log($value, 'Wrapping content in double quotes to be valid JSON');
					}
					
				}
				
				// Always wrap $value in parameterStart and parameterEnd! In case $value is not a parameter of a JSON string, they will be just empty strings.
				// If $value is a stand-alone parameter, the output will look like:
				// : "value", or : "value" } 
				$value = $matches['parameterStart'] . $value . $matches['parameterEnd'];
				Debug::log($value, $matches['varName'] . ' ' . $matches['varFunctions']);	
					
				return $value;
															
			}, $str);
		
	}


	/**
	 *	Process the full markup - variables, includes, methods and other constructs.
	 *
	 * 	Replace variable keys with its values, call Toolbox methods, call Extensions, execute statements (with, loops and conditions) and include template elements recursively.     
	 *	For example {@ file.php @}, {@ method{ options } @}, {@ foreach in ... @} ... {@ end @} or {@ if {[ var ]} @} ... {@ else @} ... {@ end @}.    
	 *
	 *	The "with" statement makes data associated with a specified page or a file accessible.    
	 *	With a page, the context changes to the given page, with files, the file's system variables (:file, :basename and :caption) can be used.      
	 *
	 *	Inside a "foreach in pagelist" loop, the context changes with each iteration and the active page in the loop becomes the current page.    
	 *	Therefore all variables of the active page in the loop can be accessed using the standard template syntax like $( var ).    
	 *	Inside other loops, the following system variables can be used within a snippet: {[ :filter ]}, {[ :tag ]}, {[ :file ]} and {[ :basename ]}.  
	 *	All loops also generate an index {[ :i ]} for each elements in the array. 
	 *
	 *	@param string $str - The string to be parsed
	 *	@param string $directory - The directory of the currently included file/template
	 *	@return the processed string	
	 */

	private function processMarkup($str, $directory) {
	
		// Identify the outer statements.
		$str = $this->preProcessWrappingStatements($str);
		
		return 	preg_replace_callback('/' . Regex::markup() . '/is', function($matches) use ($directory) {
												
				// Variable - if the variable syntax gets matched, simply process that string as content to get the value.
				if (!empty($matches['var'])) {
					return $this->processContent($matches['var']);
				}
							
				// Include
				if (!empty($matches['file'])) {
					
					Debug::log($matches['file'], 'Matched include');
					$file = $directory . '/' . $matches['file'];
				
					if (file_exists($file)) {
						Debug::log($file, 'Including');	
						return $this->processMarkup($this->Automad->loadTemplate($file), dirname($file));
					} else {
						Debug::log($file, 'File not found');
					}
						
				}
				
				// Call a snippet, Toolbox method or extension.
				if (!empty($matches['call'])) {
					
					$call = $matches['call'];
					Debug::log($call, 'Matched call');
					
					// Check if options exist.
					if (isset($matches['options'])) {
						// Parse the options JSON and also find and replace included variables within the JSON string.
						$options = Parse::jsonOptions($this->processContent($matches['options'], true));
					} else {
						$options = array();
					}
					
					// Call snippet or method in order of priority: Snippets, Toolbox methods and extensions.
					if (array_key_exists($call, $this->snippets)) {
						
						// Process a registered snippet.
						Debug::log($call, 'Process registered snippet');
						return $this->processMarkup($this->snippets[$call], $directory);
						
					} else if (method_exists($this->Toolbox, $call)) {
						
						// Call a toolbox method, in case there is no matching snippet. 
						Debug::log($options, 'Calling method ' . $call . ' and passing the following options');	
						return $this->Toolbox->$call($options);
						
					} else {
						
						// Try an extension, if no snippet or toolbox method was found.
						Debug::log($call . ' is not a snippet or core method. Will look for a matching extension ...');
						$Extension = new Extension($call, $options, $this->Automad);
						$this->mergeExtensionAssets($Extension->getAssets());
						return $Extension->getOutput();
						
					}
					
				}
				
				// Define a snippet
				if (!empty($matches['snippet'])) {
					
					$this->snippets[$matches['snippet']] = $matches['snippetSnippet'];
					Debug::log($this->snippets, 'Registered snippet "' . $matches['snippet'] . '"');
					
				}
				
				// With
				if (!empty($matches['with'])) {
					
					$Context = $this->Automad->Context;
					$url = $this->processContent(trim($matches['with'], '\'"'));
					
					// Previous or next page. Use lowercase matches to be case insensitive.
					if (strtolower($matches['with']) == 'prev' || strtolower($matches['with']) == 'next') {
						
						$Selection = new Selection($this->Automad->getPagelist()->getPages());
						$Selection->filterPrevAndNextToUrl($Context->get()->url);
						$pages = $Selection->getSelection();
						
						if (array_key_exists(strtolower($matches['with']), $pages)) {
							$Page = $pages[strtolower($matches['with'])];
						}
						
					}
				
					// Any existing page.
					if (array_key_exists($url, $this->Automad->getCollection())) {
						$Page = $this->Automad->getPageByUrl($url);
					}
						
					// Process snippet for $Page.
					if (!empty($Page)) {	
						Debug::log($Page->url, 'With page');
						// Save original context.
						$contextBeforeWith = $Context->get();
						// Set context to $url.
						$Context->set($Page);
						// Parse snippet.
						$html = $this->processMarkup($matches['withSnippet'], $directory);
						// Restore original context.
						$Context->set($contextBeforeWith);
						return $html;
					} 
										
					// If no matching page exists, check for a file.
					$files = Parse::fileDeclaration($url, $Context->get(), true);
					
					if (!empty($files)) {
						
						$file = $files[0];
						Debug::log($file, 'With file');
						// Store current filename and its basename in the system variable buffer.
						$this->setIndependentSystemVar(AM_KEY_FILE, $file);
						$this->setIndependentSystemVar(AM_KEY_BASENAME, basename($file));
						// Process snippet.
						$html = $this->processMarkup($matches['withSnippet'], $directory);
						// Reset system variables.
						$this->setIndependentSystemVar(AM_KEY_FILE, NULL);
						$this->setIndependentSystemVar(AM_KEY_BASENAME, NULL);
						return $html;
						
					} 
						
					// In case $url is not a page and also not a file (no 'return' was called before), process the 'withElseSnippet'.
					Debug::log($url, 'With: No matching page or file found for');
					
					if (!empty($matches['withElseSnippet'])) {
						return $this->processMarkup($matches['withElseSnippet'], $directory);
					}
	
				}
				
				// For loop
				// To test whether the matched statement is a for loop, $matches['forSnippet'] has to be checked, 
				// because both other matches (forStart and forEnd) could be set to 0 (!empty() = false)!
				if (!empty($matches['forSnippet'])) {
					
					$start = intval($this->processContent($matches['forStart']));
					$end = intval($this->processContent($matches['forEnd']));
					$html = '';
					
					// Save the index before any loop - the index will be overwritten when iterating over filter, tags and files and must be restored after the loop.
					$iBeforeLoop = $this->getIndependentSystemVar(AM_KEY_INDEX);
					
					// The loop.
					for ($i = $start; $i <= $end; $i++) {
						// Set index variable. The index can be used as {[ :i ]}.
						$this->setIndependentSystemVar(AM_KEY_INDEX, $i);
						// Parse snippet.
						Debug::log($i, 'Processing snippet in loop for index');
						$html .= $this->processMarkup($matches['forSnippet'], $directory);
					}
					
					// Restore index.
					$this->setIndependentSystemVar(AM_KEY_INDEX, $iBeforeLoop);
					
					return $html;
					
				}
				
				// Foreach loop
				if (!empty($matches['foreach'])) {
						
					$Context = $this->Automad->Context;
					$foreachSnippet = $matches['foreachSnippet'];
					$foreachElseSnippet = '';
					
					if (!empty($matches['foreachElseSnippet'])) {
						$foreachElseSnippet = $matches['foreachElseSnippet'];
					}
					
					$html = '';
					$i = 0;
					
					// Save the index before any loop - the index will be overwritten when iterating over filter, tags and files and must be restored after the loop.
					$iBeforeLoop = $this->getIndependentSystemVar(AM_KEY_INDEX);
					
					if (strtolower($matches['foreach']) == 'pagelist') {
						
						// Pagelist
						
						// Get pages.
						$pages = $this->Automad->getPagelist()->getPages();
						// Save context page.
						$contextBeforeLoop = $Context->get();
						
						Debug::log($pages, 'Foreach in pagelist loop');
						
						foreach ($pages as $Page) {
							// Cache the current pagelist configuration to be restored after processing the snippet.
							$pagelistConfigCache = $this->Automad->getPagelist()->config();
							// Set context to the current page in the loop.
							$Context->set($Page);
							// Set index for current page. The index can be used as {[ :i ]}.
							$this->setIndependentSystemVar(AM_KEY_INDEX, ++$i);
							// Parse snippet.
							Debug::log($Page, 'Processing snippet in loop for page: "' . $Page->url . '"');
							$html .= $this->processMarkup($foreachSnippet, $directory);
							// Restore pagelist configuration.
							$this->Automad->getPagelist()->config($pagelistConfigCache);
						}
						
						// Restore context.
						$Context->set($contextBeforeLoop);
							
					} else if (strtolower($matches['foreach']) == 'filters') {
						
						// Filters (tags of the pages in the pagelist)
						// Each filter can be used as {[ :filter ]} within a snippet.
						
						foreach ($this->Automad->getPagelist()->getTags() as $filter) {
							Debug::log($filter, 'Processing snippet in loop for filter');
							// Store current filter in the system variable buffer.
							$this->setIndependentSystemVar(AM_KEY_FILTER, $filter);
							// Set index. The index can be used as {[ :i ]}.
							$this->setIndependentSystemVar(AM_KEY_INDEX, ++$i);
							$html .= $this->processMarkup($foreachSnippet, $directory);
						}
	
						$this->setIndependentSystemVar(AM_KEY_FILTER, NULL);
							
					} else if (strtolower($matches['foreach']) == 'tags') {

						// Tags (of the current page)	
						// Each tag can be used as {[ :tag ]} within a snippet.

						foreach ($Context->get()->tags as $tag) {
							Debug::log($tag, 'Processing snippet in loop for tag');							
							// Store current tag in the system variable buffer.
							$this->setIndependentSystemVar(AM_KEY_TAG, $tag);							
							// Set index. The index can be used as {[ :i ]}.
							$this->setIndependentSystemVar(AM_KEY_INDEX, ++$i);
							$html .= $this->processMarkup($foreachSnippet, $directory);
						}
						
						$this->setIndependentSystemVar(AM_KEY_TAG, NULL);
	
					} else {
						
						// Files
						// The file path and the basename can be used like {[ :file ]} and {[ :basename ]} within a snippet.
						
						if (strtolower($matches['foreach']) == 'filelist') {
							// Use files from filelist.
							$files = $this->Automad->getFilelist()->getFiles();
						} else {
							// Parse given glob pattern within any kind of quotes or from a variable value.  
							$files = Parse::fileDeclaration($this->processContent(trim($matches['foreach'], '\'"')), $Context->get(), true);
						}
						
						foreach ($files as $file) {
							Debug::log($file, 'Processing snippet in loop for file');
							// Store current filename and its basename in the system variable buffer.
							$this->setIndependentSystemVar(AM_KEY_FILE, $file);
							$this->setIndependentSystemVar(AM_KEY_BASENAME, basename($file));
							// Set index. The index can be used as {[ :i ]}.
							$this->setIndependentSystemVar(AM_KEY_INDEX, ++$i);
							$html .= $this->processMarkup($foreachSnippet, $directory);
						}
						
						$this->setIndependentSystemVar(AM_KEY_FILE, NULL);
						$this->setIndependentSystemVar(AM_KEY_BASENAME, NULL);
							
					}
					
					// Restore index.
					$this->setIndependentSystemVar(AM_KEY_INDEX, $iBeforeLoop);
					
					// If the counter ($i) is 0 (false), process the "else" snippet.
					if (!$i) {
						Debug::log('foreach in ' . strtolower($matches['foreach']), 'No elements array. Processing else statement for');
						$html .= $this->processMarkup($foreachElseSnippet, $directory);
					}
					
					return $html;
					
				}
				
				// Condition
				if (!empty($matches['if'])) {
					
					$ifSnippet = $matches['ifSnippet'];
					$ifElseSnippet = '';
				
					if (!empty($matches['ifElseSnippet'])) {
						$ifElseSnippet = $matches['ifElseSnippet'];
					} 
					
					// Match each part of a logically combined expression separately.
					preg_match_all('/(?P<operator>^|' . Regex::$logicalOperator . '\s+)' . Regex::expression('expression') . '/is', trim($matches['if']), $parts, PREG_SET_ORDER);
					
					// Process each part and merge the partial result with the final result.
					foreach ($parts as $part) {
							
						// Separate comparisons from boolean expressions and get a partial result.
						if (!empty($part['expressionOperator'])) {
							
							// Comparison.
							
							// Merge default keys with $part to make sure each key exists in $part without testing.
							$part = 	array_merge(
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
							
							// Parse both sides of the expression. All possible matches for each side can get merged in to one string, since there will be only one item for left/right not empty.
							$left = 	$this->processContent(
										stripslashes($part['expressionLeftDoubleQuoted']) .
										stripslashes($part['expressionLeftSingleQuoted']) .
										$part['expressionLeftNumber'] .
										$part['expressionLeftVar']
									);
							$right = 	$this->processContent(
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
							$expressionVar = $this->processContent($part['expressionVar']);
							
							// If EMPTY NOT == NOT EMPTY Value.
							$partialResult = (empty($part['expressionNot']) == !empty($expressionVar));
							
						}
						
						// Combine results based on logical operator - note that for the first part, the operator will be empty of course.
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
					if ($result) {
						
						Debug::log('TRUE', 'Evaluating condition: if ' . $matches['if']);
						return $this->processMarkup($ifSnippet, $directory);
						
					} else {
						
						Debug::log('FALSE', 'Evaluating condition: if ' . $matches['if']);
						return $this->processMarkup($ifElseSnippet, $directory);
						
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
		
		$Page = $this->Automad->Context->get();
		
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
				
				Debug::log($matches[1], 'Obfuscating email');
					
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
		
		Debug::log($this->template, 'Render template');
		
		$output = $this->Automad->loadTemplate($this->template);
		$output = $this->processMarkup($output, dirname($this->template));
		$output = $this->createExtensionAssetTags($output);
		$output = $this->addMetaTags($output);
		$output = $this->resolveUrls($output);	
		$output = $this->obfuscateEmails($output);
	
		return $output;	
		
	}	
		
	
}


?>