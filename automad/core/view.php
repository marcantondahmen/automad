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
 *	Copyright (c) 2013-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;
use Automad\GUI as GUI;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 * 	The View class holds all methods to render the current page using a template file.
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
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2013-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class View {
	
	
	/**
	 * 	The Automad object.
	 */
	
	private $Automad;
	
	
	/**
	 * 	Multidimensional array of collected extension assets grouped by type (CSS/JS).
	 */
	
	public $extensionAssets = array();
	
	
	/**
	 *	The InPage objetc.
	 */
	
	private $InPage;
	
	
	/**
	 *	The Runtime object.
	 */
	
	private $Runtime;
	

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
	 * 	Set the headless mode for the current view.
	 */

	private $headless;
	
	
	/**
	 *	Define $Automad and $Page, check if the page gets redirected and get the template name. 
	 *	
	 *	@param object $Automad
	 *	@param boolean $headless
	 */
	
	public function __construct($Automad, $headless = false) {
		
		$this->Automad = $Automad;
		$this->headless = $headless;
		$this->Runtime = new Runtime($Automad);
		$this->Toolbox = new Toolbox($Automad);
		$this->InPage = new GUI\InPage();
		$Page = $Automad->Context->get();
		
		// Redirect page, if the defined URL variable differs from the original URL.
		if ($Page->url != $Page->origUrl) {
			$url = Resolve::absoluteUrlToRoot(Resolve::relativeUrlToBase($Page->url, $Page));
			header('Location: ' . $url, true, 301);
			die;
		}
		
		// Set template.
		if ($this->headless) {
			$this->template = Headless::getTemplate();
		} else {
			$this->template = $Page->getTemplate();
		}
		
		Debug::log($Page, 'New instance created for the current page');
		
	}
	

	/**
	 *	Add meta tags to the head of $str.
	 *
	 *	@param string $str
	 *	@return string The meta tag
	 */
	
	private function addMetaTags($str) {
		
		$meta =  "\n\t" . '<meta name="Generator" content="Automad ' . AM_VERSION . '">';
		
		return str_replace('<head>', '<head>' . $meta, $str);
		
	}
	

	/**
	 * 	Create the HTML tags for each file in $this->extensionAssets and prepend them to the closing </head> tag.
	 *	
	 *	@param string $str
	 *	@return string The processed string
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

	public function mergeExtensionAssets($assets) {
		
		// Make sure, $this->extensionAssets has a basic structure to enable merging new assets.
		$this->extensionAssets = array_merge(array('.css' => array(), '.js' => array()), $this->extensionAssets);
		
		foreach (array('.css', '.js') as $type) {
			
			if (!empty($assets[$type])) {
				$this->extensionAssets[$type] = array_merge($this->extensionAssets[$type], $assets[$type]);
			}
			
		}
		
	}

	
	/**
	 *	Get the value of a given variable key depending on the current context - either from the page data, the system variables or from the $_GET array.
	 *
	 *	@param string $key
	 *	@return string The value
	 */
	
	private function getValue($key) {
		
		if (strpos($key, '?') === 0) {
			
			// Query string parameter.
			$key = substr($key, 1);
			return Request::query($key);
			
		} else if (strpos($key, '%') === 0) {	
			
			// Session variable.
			return SessionData::get($key);
		
		} else if (strpos($key, '+') === 0) {

			// Blocks variable.
			$value = Blocks::render($this->Automad->Context->get()->get($key), $this->Automad);
			$this->mergeExtensionAssets(Blocks::$extensionAssets);
			return $value;

		} else {
			
			// First try to get the value from the current Runtime object.
			$value = $this->Runtime->get($key);
			
			// If $value is NULL (!), try the current context.
			if (is_null($value)) {
				$value = $this->Automad->Context->get()->get($key);
			} 
			
			return $value;
			
		}
			
	}


	/**
	 * 	Preprocess recursive statements to identify the top-level (outer) statements within a parsed string. 
	 *
	 *	@param string $str
	 *	@return string The preprocessed $str where all outer opening statement delimiters get an additional marker appended.
	 */

	private function preProcessWrappingStatements($str) {
		
		$depth = 0;
		$regex = 	'/(' . 
					'(?P<begin>' . preg_quote(AM_DEL_STATEMENT_OPEN) . '\s*(?:if|for|foreach|with|snippet)\s.*?' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')|' .
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
	 *	Optionally all values can be parsed as "JSON safe" ($isOptionString), by escaping all quotes and wrapping variable is quotes when needed.
	 *	In case a variable is used as an option value for a method and is not part of a string, that variable doesn't need to be 
	 *	wrapped in double quotes to work within the JSON string - the double quotes get added automatically.
	 *
	 *  By setting $inPageEdit to true, for every processed variable, a temporary markup for an edit button is appended to the actual value.
	 *  That temporary button still has to be processed later by calling processInPageEditButtons(). 
	 *
	 *	@param string $str
	 *	@param boolean $isOptionString 
	 *	@param boolean $inPageEdit
	 *	@return string The processed $str
	 */

	private function processContent($str, $isOptionString = false, $inPageEdit = false) {
		
		// Prepare JSON strings by wrapping all stand-alone variables in quotes.		
		if ($isOptionString) {
			
			$str = preg_replace_callback('/' . Regex::keyValue() . '/s', function($pair) {
				
				if (strpos($pair['value'], AM_DEL_VAR_OPEN) === 0) {
					$pair['value'] = '"' . trim($pair['value']) . '"';
				}
				
				return $pair['key'] . ':' . $pair['value'];
				
			}, $str);
			
		}		
			
		return 	preg_replace_callback('/' . Regex::variable('var') . '/s', function($matches) use ($isOptionString, $inPageEdit) {
					
				// Get the value.
				$value = $this->getValue($matches['varName']);
				
				// Resolve URLs in content before passing it to pipe functions
				// to make sure images and files can be used correctly in custom
				// pipe functions.
				$value = $this->resolveUrls($value, 'relativeUrlToBase', array($this->Automad->Context->get()));

				// Get pipe functions.
				$functions = array();
				
				preg_match_all('/' . Regex::pipe('pipe') . '/s', $matches['varFunctions'], $pipes, PREG_SET_ORDER);
				
				foreach ($pipes as $pipe) {
					
					if (!empty($pipe['pipeFunction'])) {
						
						$parametersArray = array();
						
						if (isset($pipe['pipeParameters'])) {
							
							preg_match_all('/' . Regex::csv() . '/s', $pipe['pipeParameters'], $pipeParameters, PREG_SET_ORDER);

							foreach ($pipeParameters as $match) {
																
								$parameter = trim($match[1]);
								
								if (in_array($parameter, array('true', 'false'))) {
									$parameter = filter_var($parameter, FILTER_VALIDATE_BOOLEAN);
								} else {
									// Remove outer quotes and strip slashes.
									$parameter = preg_replace('/^([\'"])(.*)\1$/s', '$2', $parameter);
									$parameter = stripcslashes($this->processContent($parameter));
								}
								
								$parametersArray[] = $parameter;
								
							}
							
						}
						
						$functions[] = array(
							'name' => $pipe['pipeFunction'],
							'parameters' => $parametersArray
						);
						
					}
					
					// Math.
					if (!empty($pipe['pipeOperator'])) {
						
						$functions[] = array(
							'name' => $pipe['pipeOperator'],
							'parameters' => $this->processContent($pipe['pipeNumber'])
						);
						
					}
					
				}
				
				// Modify $value by processing all matched string functions.
				$value = Pipe::process($value, $functions);
				
				// Escape values to be used in headless mode and option strings.
				if ($this->headless || $isOptionString) {
					$value = Str::escape($value);
				}
				
				// Inject "in-page edit" button in case varName starts with a word-char and an user is logged in.
				// The button needs to be wrapped in delimiters to enable a secondary cleanup step to remove buttons within HTML tags.
				if ($inPageEdit && !$this->headless) {
					
					$value = 	$this->InPage->injectTemporaryEditButton(
									$value, 
									$matches['varName'], 
									$this->Automad->Context
								);
						
				}	
				
				return $value;
															
			}, $str);
		
	}


	/**
	 *	Process a file related snippet like <@ foreach "*.jpg" { options } @> ... <@ end @>.
	 *      
	 *	@param string $file
	 *	@param array $options  
	 *	@param string $snippet  
	 *	@param string $directory
	 *	@return string $html           
	 */
	
	private function processFileSnippet($file, $options, $snippet, $directory) {
		
		// Shelve runtime data.
		$runtimeShelf = $this->Runtime->shelve();
		
		// Store current filename and its basename in the system variable buffer.
		$this->Runtime->set(AM_KEY_FILE, $file);
		$this->Runtime->set(AM_KEY_BASENAME, basename($file));
		
		// If $file is an image, also provide width and height (and possibly a new filename after a resize).
		if (Parse::fileIsImage($file)) {
			
			// The Original file size.
			$imgSize = getimagesize(AM_BASE_DIR . $file);
			$this->Runtime->set(AM_KEY_WIDTH, $imgSize[0]);
			$this->Runtime->set(AM_KEY_HEIGHT, $imgSize[1]);
			
			// If any options are given, create a resized version of the image.
			if (!empty($options)) {
		
				$options = 	array_merge(
								array(
									'width' => false, 
									'height' => false, 
									'crop' => false
								), 
								$options
							);
				
				$img = new Image(AM_BASE_DIR . $file, $options['width'], $options['height'], $options['crop']);
				$this->Runtime->set(AM_KEY_FILE_RESIZED, $img->file);
				$this->Runtime->set(AM_KEY_WIDTH_RESIZED, $img->width);
				$this->Runtime->set(AM_KEY_HEIGHT_RESIZED, $img->height);
				
			}
			
		} 
		
		// Process snippet.
		$html = $this->interpret($snippet, $directory);
		
		// Unshelve runtime data.
		$this->Runtime->unshelve($runtimeShelf);

		return $html;
			
	}


	/**
	 *	Process the full markup - variables, includes, methods and other constructs.   
	 *       
	 * 	Replace variable keys with its values, call Toolbox methods, call Extensions, execute statements (with, loops and conditions) and include template elements recursively.     
	 *	For example <@ file.php @>, <@ method { options } @>, <@ foreach in ... @> ... <@ end @> or <@ if @{var} @> ... <@ else @> ... <@ end @>.    
	 *        
	 *	With and foreach:   
	 *         
	 *	Pages:   
	 *	Within a <@ with "/url" @> statement or a <@ foreach in pagelist @> loop the context changes (with each iteration in loops)
	 *	and the active page becomes the current page. Therefore all variables of that active page inside the with statements or loop
	 *	can simply be accessed using the standard template syntax like @{var}.    
	 *         
	 *  Files:   
	 *	The <@ with "image.jpg" {options} @> statement and the <@ foreach in filelist {options} @> or <@ foreach in "*.jpg" {options} @> loop
	 *	make data associated with files accessible. The following runtime vars can be used inside:    
	 *	- @{:basename}   
	 *	- @{:file}   
	 *	- @{:width}   
	 *	- @{:height}   
	 *	- @{:fileResized}   
	 *	- @{:widthResized}   
	 *	- @{:heightResized}   
	 *	- @{:caption}   
	 *       
	 *  Tags/Filters:    
	 *	Inside other foreach loops, the following runtime variables can be used within a snippet:   
	 *	- @{:filter}  
	 *	- @{:tag}  
	 *		 
	 *	All loops also generate an index @{:i} for each elements in the array.   
	 *
	 *	@param string $str - The string to be parsed
	 *	@param string $directory - The directory of the currently included file/template
	 *	@return string The interpreted string	
	 */

	public function interpret($str, $directory) {
	
		// Strip whitespace.
		$str = $this->stripWhitespace($str);

		// Identify the outer statements.
		$str = $this->preProcessWrappingStatements($str);
		
		$str = preg_replace_callback('/' . Regex::markup() . '/is', function($matches) use ($directory) {
						
				// Variable - if the variable syntax gets matched, simply process that string as content to get the value.
				// In-page editing gets enabled here.
				if (!empty($matches['var'])) {
					return $this->processContent($matches['var'], false, true);
				}
							
				// Include
				if (!empty($matches['file'])) {
					
					Debug::log($matches['file'], 'Matched include');
					$file = $directory . '/' . $matches['file'];
				
					if (file_exists($file)) {
						Debug::log($file, 'Including');	
						return $this->interpret($this->Automad->loadTemplate($file), dirname($file));
					} else {
						Debug::log($file, 'File not found');
					}
						
				}
				
				// Call a snippet, Toolbox method or extension.
				if (!empty($matches['call'])) {
					
					$call = $matches['call'];
					Debug::log($call, 'Matched call');
					
					// Check if options exist.
					if (isset($matches['callOptions'])) {
						// Parse the options JSON and also find and replace included variables within the JSON string.
						$options = Parse::jsonOptions($this->processContent($matches['callOptions'], true));
					} else {
						$options = array();
					}
					
					// Call snippet or method in order of priority: Snippets, Toolbox methods and extensions.
					if (array_key_exists($call, $this->snippets)) {
						
						// Process a registered snippet.
						Debug::log($call, 'Process registered snippet');
						return $this->interpret($this->snippets[$call], $directory);
						
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
						
						// Cache the current pagelist config and temporary disable the excludeHidden parameter to also
						// get the neighbors of a hidden page.
						$pagelistConfigShelf = $this->Automad->getPagelist()->config();
						$this->Automad->getPagelist()->config(array('excludeHidden' => false));
						
						$Selection = new Selection($this->Automad->getPagelist()->getPages(true));
						$Selection->filterPrevAndNextToUrl($Context->get()->url);
						$pages = $Selection->getSelection();
						
						// Restore the original pagelist config.
						$this->Automad->getPagelist()->config($pagelistConfigShelf);
						
						if (array_key_exists(strtolower($matches['with']), $pages)) {
							$Page = $pages[strtolower($matches['with'])];
						}
						
					}
				
					// Any existing page.
					// To avoid overriding $Page (next/prev), it has to be tested explicitly whether
					// the URL actually exists.
					if (array_key_exists($url, $this->Automad->getCollection())) {
						$Page = $this->Automad->getPage($url);
					}
						
					// Process snippet for $Page.
					if (!empty($Page)) {	
						Debug::log($Page->url, 'With page');
						// Save original context and pagelist.
						$contextShelf = $Context->get();
						$pagelistConfigShelf = $this->Automad->getPagelist()->config();
						// Set context to $url.
						$Context->set($Page);
						// Parse snippet.
						$html = $this->interpret($matches['withSnippet'], $directory);
						// Restore original context and pagelist.
						$Context->set($contextShelf);
						$this->Automad->getPagelist()->config($pagelistConfigShelf);
						return $html;
					} 
										
					// If no matching page exists, check for a file.
					$files = Parse::fileDeclaration($url, $Context->get(), true);
					
					if (!empty($files)) {
						
						$file = $files[0];
						Debug::log($file, 'With file');
						
						return $this->processFileSnippet(
							$file, 
							Parse::jsonOptions($this->processContent($matches['withOptions'], true)), 
							$matches['withSnippet'], 
							$directory
						);
						
					} 
						
					// In case $url is not a page and also not a file (no 'return' was called before), process the 'withElseSnippet'.
					Debug::log($url, 'With: No matching page or file found for');
					
					if (!empty($matches['withElseSnippet'])) {
						return $this->interpret($matches['withElseSnippet'], $directory);
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
					$runtimeShelf = $this->Runtime->shelve();
					
					// The loop.
					for ($i = $start; $i <= $end; $i++) {
						// Set index variable. The index can be used as @{:i}.
						$this->Runtime->set(AM_KEY_INDEX, $i);
						// Parse snippet.
						Debug::log($i, 'Processing snippet in loop for index');
						$html .= $this->interpret($matches['forSnippet'], $directory);
					}
					
					// Restore index.
					$this->Runtime->unshelve($runtimeShelf);
					
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
					
					// Shelve the runtime objetc before any loop. 
					// The index will be overwritten when iterating over filter, tags and files and must be restored after the loop.
					$runtimeShelf = $this->Runtime->shelve();
					
					if (strtolower($matches['foreach']) == 'pagelist') {
						
						// Pagelist
						
						// Get pages.
						$pages = $this->Automad->getPagelist()->getPages();
						Debug::log($pages, 'Foreach in pagelist loop');
						
						// Shelve context page and pagelist config.
						$contextShelf = $Context->get();
						$pagelistConfigShelf = $this->Automad->getPagelist()->config();
						
						// Calculate offset for index.
						if ($pagelistPage = intval($pagelistConfigShelf['page'])) {
							$offset = ($pagelistPage - 1) * intval($pagelistConfigShelf['limit']);
						} else {
							$offset = intval($pagelistConfigShelf['offset']);
						}
						
						foreach ($pages as $Page) {
							// Set context to the current page in the loop.
							$Context->set($Page);
							// Set index for current page. The index can be used as @{:i}.
							$this->Runtime->set(AM_KEY_INDEX, ++$i + $offset);
							// Parse snippet.
							Debug::log($Page, 'Processing snippet in loop for page: "' . $Page->url . '"');
							$html .= $this->interpret($foreachSnippet, $directory);
							// Note that the config only has to be shelved once before starting the loop, 
							// but has to be restored after each snippet to provide the correct data (like :pagelistCount)
							// for the next iteration, since a changed config would generate incorrect values in 
							// recursive loops.
							$this->Automad->getPagelist()->config($pagelistConfigShelf);
						}
						
						// Restore context.
						$Context->set($contextShelf);
							
					} else if (strtolower($matches['foreach']) == 'filters') {
						
						// Filters (tags of the pages in the pagelist)
						// Each filter can be used as @{:filter} within a snippet.
						
						foreach ($this->Automad->getPagelist()->getTags() as $filter) {
							Debug::log($filter, 'Processing snippet in loop for filter');
							// Store current filter in the system variable buffer.
							$this->Runtime->set(AM_KEY_FILTER, $filter);
							// Set index. The index can be used as @{:i}.
							$this->Runtime->set(AM_KEY_INDEX, ++$i);
							$html .= $this->interpret($foreachSnippet, $directory);
						}
							
					} else if (strtolower($matches['foreach']) == 'tags') {

						// Tags (of the current page)	
						// Each tag can be used as @{:tag} within a snippet.

						foreach ($Context->get()->tags as $tag) {
							Debug::log($tag, 'Processing snippet in loop for tag');							
							// Store current tag in the system variable buffer.
							$this->Runtime->set(AM_KEY_TAG, $tag);							
							// Set index. The index can be used as @{:i}.
							$this->Runtime->set(AM_KEY_INDEX, ++$i);
							$html .= $this->interpret($foreachSnippet, $directory);
						}
	
					} else {
						
						// Files
						// The file path and the basename can be used like @{:file} and @{:basename} within a snippet.
						
						if (strtolower($matches['foreach']) == 'filelist') {
							// Use files from filelist.
							$files = $this->Automad->getFilelist()->getFiles();
						} else {
							// Parse given glob pattern within any kind of quotes or from a variable value.  
							$files = Parse::fileDeclaration($this->processContent(trim($matches['foreach'], '\'"')), $Context->get(), true);
						}
						
						foreach ($files as $file) {
							Debug::log($file, 'Processing snippet in loop for file');
							// Set index. The index can be used as @{:i}.
							$this->Runtime->set(AM_KEY_INDEX, ++$i);
							$html .= $this->processFileSnippet(
									$file, 
									Parse::jsonOptions($this->processContent($matches['foreachOptions'], true)), 
									$foreachSnippet, 
									$directory
							);
						}
							
					}
					
					// Restore runtime.
					$this->Runtime->unshelve($runtimeShelf);
				
					// If the counter ($i) is 0 (false), process the "else" snippet.
					if (!$i) {
						Debug::log('foreach in ' . strtolower($matches['foreach']), 'No elements array. Processing else statement for');
						$html .= $this->interpret($foreachElseSnippet, $directory);
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
							
							// Parse both sides of the expression. All possible matches for each side can get merged in to one string, since there will be only one item for left/right not empty.
							$left = $this->processContent(
										stripslashes($part['expressionLeftDoubleQuoted']) .
										stripslashes($part['expressionLeftSingleQuoted']) .
										$part['expressionLeftNumber'] .
										$part['expressionLeftVar']
									);
							$right = $this->processContent(
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
						return $this->interpret($ifSnippet, $directory);
						
					} else {
						
						Debug::log('FALSE', 'Evaluating condition: if ' . $matches['if']);
						return $this->interpret($ifElseSnippet, $directory);
						
					}
						
				}
				
			}, $str);
			
		return $this->resolveUrls($str, 'relativeUrlToBase', array($this->Automad->Context->get()));
		
	}
	
	
	/**
	 *	Resize any image in the output in case it has a specified size as query string like
	 *	for example "/shared/image.jpg?200x200".
	 *
	 *	@param string $str
	 *	@return string The processed string
	 */

	public function resizeImages($str) {

		return preg_replace_callback('/(\/[\w\.\-\/]+(?:jpg|jpeg|gif|png))\?(\d+)x(\d+)/is', function($match) {

			$file = AM_BASE_DIR . $match[1];

			if (is_readable($file)) {
				$image = new Image($file, $match[2], $match[3], true);
				return $image->file;
			}

			return $match[0];

		}, $str);

	}


	/**
	 *	Find and resolve URLs using the specified resolving method and parameters.
	 *
	 *	@param string $str
	 *	@param string $method
	 *	@param array $parameters
	 *	@return string The processed string
	 */
	
	public function resolveUrls($str, $method, $parameters = array()) {
		
		$method = '\Automad\Core\Resolve::' . $method;
		
		// Find URLs in markdown like ![...](image.jpg?100x100).
		$str =	preg_replace_callback('/(\!\[[^\]]*\]\()([^\)]+\.(?:jpg|jpeg|gif|png))([^\)]*\))/is', function($match) use ($method, $parameters) {
					
					$parameters = array_merge(array(0 => $match[2]), $parameters);
					$url = call_user_func_array($method, $parameters);
					
					if (file_exists(AM_BASE_DIR . $url)) {
						return $match[1] . $url . $match[3];
					} else {
						return $match[0];
					}
					
				}, $str);

		// Find URLs in action, href and src attributes. 
		// Note that all URLs in markdown code blocks will be ignored (<[^>]+).
		$str = 	preg_replace_callback('/(<[^>]+(?:action|href|src))=((?:\\\\)?")(.+?)((?:\\\\)?")/is', function($match) use ($method, $parameters) {
					$parameters = array_merge(array(0 => $match[3]), $parameters);
					$url = call_user_func_array($method, $parameters);
					// Matches 2 and 4 are quotes.
					return $match[1] . '=' . $match[2] . $url . $match[4];
				}, $str);
				
		// Inline styles (like background-image).
		// Note that all URLs in markdown code blocks will be ignored (<[^>]+).
		$str = 	preg_replace_callback('/(<[^>]+)url\(\'?(.+?)\'?\)/is', function($match) use ($method, $parameters) {
					$parameters = array_merge(array(0 => $match[2]), $parameters);
					$url = call_user_func_array($method, $parameters);
					return $match[1] . 'url(\'' . $url . '\')';
				}, $str);
				
		// Image srcset attributes.
		// Note that all URLs in markdown code blocks will be ignored (<[^>]+).
		$str = 	preg_replace_callback('/(<[^>]+srcset)=((?:\\\\)?")([^"]+)((?:\\\\)?")/is', function($match) use ($method, $parameters) {
					$urls = preg_replace_callback('/([^,\s]+)\s+(\w+)/is', function($match) use ($method, $parameters) {
						$parameters = array_merge(array(0 => $match[1]), $parameters);
						return call_user_func_array($method, $parameters) . ' ' . $match[2];
					}, $match[3]);
					// Matches 2 and 4 are quotes.
					return $match[1] . '=' . $match[2] . $urls . $match[4]; 
				}, $str);
	
		return $str;
				
	}

	
	/**
	 *	Strip whitespace before or after delimiters when using "<@~" or "~@>".
	 *
	 *	@param string $str
	 *	@return string The processed string
	 */

	private function stripWhitespace($str) {

		$str = preg_replace('/\s*(' . preg_quote(AM_DEL_STATEMENT_OPEN) . ')~/is', '$1', $str);
		$str = preg_replace('/~(' . preg_quote(AM_DEL_STATEMENT_CLOSE) . ')\s*/is', '$1', $str);
		return $str;

	}

	
	/**
	 *	Obfuscate all stand-alone eMail addresses matched in $str. 
	 *	Addresses in links are ignored. In headless mode, obfuscation is disabled.
	 *	
	 *	@param string $str
	 *	@return string The processed string
	 */
	
	public function obfuscateEmails($str) {
		
		if ($this->headless) {
			return $str;
		}

		$regexEmail = '[\w\.\+\-]+@[\w\-\.]+\.[a-zA-Z]{2,}';
		
		// The following regex matches all email links or just an email address. 
		// That way it is possible to separate email addresses 
		// within <a></a> tags from stand-alone ones.
		$regex = '/(<a\s[^>]*href="mailto.+?<\/a>|(?P<email>' . $regexEmail . '))/is';
		
		return 	preg_replace_callback($regex, function($matches) {
				
				// Only stand-alone addresses are obfuscated.
				if (!empty($matches['email'])) {
					
					Debug::log($matches['email'], 'Obfuscating');
						
					$html = '<a href="#" onclick="this.href=\'mailto:\'+ this.innerHTML.split(\'\').reverse().join(\'\')" style="unicode-bidi:bidi-override;direction:rtl">';
					$html .= strrev($matches['email']);
					$html .= "</a>&#x200E;";
			
					return $html;
					
				} else {
					
					Debug::log($matches[0], 'Ignoring');	
						
					return $matches[0];
					
				}
				
			}, $str);
						
	}
		
	
	/**
	 * 	Render the current page.
	 *
	 *	@return string The fully rendered HTML for the current page.
	 */
	
	public function render() {
		
		Debug::log($this->template, 'Render template');
		
		$output = $this->Automad->loadTemplate($this->template);
		$output = $this->interpret($output, dirname($this->template));
		$output = $this->createExtensionAssetTags($output);
		$output = $this->addMetaTags($output);
		$output = $this->obfuscateEmails($output);
		$output = $this->resizeImages($output);
		$output = $this->resolveUrls($output, 'absoluteUrlToRoot');
		$output = Blocks::injectAssets($output);
		$output = $this->InPage->createUI($output);
		
		return trim($output);	
		
	}	
		
	
}
