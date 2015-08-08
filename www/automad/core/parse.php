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
 *	The Parse class holds all parsing methods.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */
 
class Parse {
	

	/**
	 *	Return an array with the allowed file types.
	 *
	 *	@return Array of file types
	 */

	public static function allowedFileTypes() {
		
		// Split string
		$types = explode(AM_PARSE_STR_SEPARATOR, AM_ALLOWED_FILE_TYPES);
		// Trim items
		$types = array_map(function($type) {
				return trim($type);
		         }, $types);
		
		return $types;
		
	}


 	/**
 	 *	Extracts the tags string out of a given array and returns an array with these tags.
 	 *
 	 *	@param array $data
 	 *	@return array $tags
 	 */
	
	public static function extractTags($data) {
		
		$tags = array();
		
		foreach ($data as $key => $value) {
		
			if ($key == AM_KEY_TAGS) {
	
				// All tags are splitted into an array
				$tags = explode(AM_PARSE_STR_SEPARATOR, $value);
				// Trim & strip tags
				$tags = array_map(function($tag) {
						return trim(strip_tags($tag)); 
					}, $tags);
				
			}		
			
		}
		
		return $tags;
		
	}
	

	/**
	 *	Parse a file declaration string where multiple glob patterns can be separated by a comma and return an array with the resolved file paths.
	 * 
	 *	@param string $str
	 *	@param object $Page (current page)
	 *	@return Array with resolved file paths
	 */

	public static function fileDeclaration($str, $Page) {
		
		$files = array();
		
		foreach (explode(AM_PARSE_STR_SEPARATOR, $str) as $glob) {
					
			if ($f = glob(Resolve::filePath($Page->path, trim($glob)))) {
				$files = array_merge($files, $f);
			}
			
		}
		
		array_walk($files, function(&$file) { 
			$file = realpath($file); 
		});
		
		return $files;
		
	}


	/**
	 *	Tests if a string is a file name (with an allowed file extension).
	 *
	 *	Basically a possibly existing file extension is checked against the array of allowed file extensions.
	 *
	 *	"/url/file.jpg" will return true, "/url/file" or "/url/file.something" will return false.
	 *	
	 *	@param string $str
	 *	@return boolean
	 */
	
	public static function isFileName($str) {
		
		// Remove possible query string
		$str = preg_replace('/\?.*/', '', $str);
		
		// Get just the basename
		$str = basename($str);
		
		// Possible extension		
		$extension = strtolower(pathinfo($str, PATHINFO_EXTENSION));
		
		if (in_array($extension, self::allowedFileTypes())) {
			return true;
		} else {
			return false;
		}
		
	}
	

	/**
	 * 	Escape a string to be used within a JSON string.
	 *	
	 *	@param string $str
	 *	@return Escaped, JSON-safe string
	 */

	public static function jsonEscape($str) {
		
		$search = array('"',   "'",  "\n", "\r");
		$replace = array('\"', "\'", ' ',  ' ');
		
		return str_replace($search, $replace, $str);
		
	}

	
	/**
	 *	Parse a (dirty) JSON string and return an associative, filtered array
	 *
	 *	@param string $str
	 *	@return $options - associative array
	 */

	public static function jsonOptions($str) {
		
		$options = array();
		
		if ($str) {
			
			// Clean up "dirty" JSON by replacing single with double quotes and
			// wrapping all keys in double quotes.
			$str = str_replace("'", '"', $str);
			$str = preg_replace('/([{,]+)\s*([^":\s]+)\s*:/i', '\1"\2":', $str);
				
			// Decode JSON.
			$options = json_decode($str, true);
			
			// Remove empty ('') strings, but leave false values (false, 0 or "0").
			$options = 	array_filter($options, function($value) {
						return ($value !== '');
					}); 
						
		}
		
		return $options;
		
	}
	
	
	/**
	 *	Parses a text file including markdown syntax. 
	 *	
	 *	If a variable in that file has a multiline string as its value, that string will be then parsed as markdown.
	 *	If the variable string is just a single line, markdown parsing is skipped.
	 *	If the variable is a multiline string, but starts and ends with a <head> element, markdown parsing will be skipped as well.
	 *	
	 *	@param string $file
	 *	@return Array of variables
	 */
	
	public static function markdownFile($file) {
		
		$vars = self::textFile($file);
			
		$vars = array_map(function($var) {
			 
				$regexTag = '/^<(!--|base|link|meta|script|style|title).*>$/is';
			 
				if (strpos($var, "\n") !== false && !preg_match($regexTag, $var)) {
					// If $var is a multiline string and not only one or more tags (meta, script, link tags ...).
					return \Michelf\MarkdownExtra::defaultTransform($var); 
				} else {
					// If $var is just a single line or just one or more <head> element(s), skip parsing.
					return $var;
				}
				
			}, $vars);
					
		return $vars;
		
	}
	
		
	/**
	 *	Get the query string, if existing.
	 *
	 *	@return $query
	 */
	
	public static function queryArray() {
		
		// First get existing query string to prevent overwriting existing settings passed already
		// and store its data in $query.
		if (isset($_GET)) {
			$query = $_GET;
		} else {
			$query = array();
		}
		
		return $query;
		
	}
	
	
	/**
	 *	Return value of a query string parameter or any empty string, if that parameter doesn't exist.
	 *	Note: Since this method always returns a string, it should not be used to test whether a parameter exists in the query string, 
	 * 	because a non-existing parameter and an empty string as a parameter's value will return the same.
	 * 
	 *	@param string $key
	 *	@return $queryKey
	 */
	
	public static function queryKey($key) {
	
		if (isset($_GET[$key])) {
			$queryKey = $_GET[$key];
		} else {
			$queryKey = '';
		}
		
		return $queryKey;
	
	}
		

	/**
	 *	Return the URL of the currently requested page.
	 *	
	 *	@return The requested URL
	 */

	public static function request() {
		
		$request = '';

		if (!isset($_SERVER['QUERY_STRING'])) {
			$_SERVER['QUERY_STRING'] = '';
		}
		
		// Check if the query string starts with a '/'. 
		// That is the case if the requested page gets passed as part of the query string when rewriting is enabled (.htaccess or nginx.conf).
		if (strncmp($_SERVER['QUERY_STRING'], '/', 1) === 0) {
			
			// The requested page gets passed as part of the query string when pretty URLs are enabled and requests get rewritten like:
			// domain.com/page -> domain.com/index.php?/page 
			// Or with a query string: 
			// domain.com/page?key=value -> domain.com/index.php?/page&key=value
			// Depending on the rewrite rules and environment, the query string can also look like:
			// domain.com/page?key=vaule -> domain.com/index.php?/page?key=value (note the 2nd "?"!)
			$query = preg_split('/[&\?]/', $_SERVER['QUERY_STRING'], 2);
			$request = $query[0];
			Debug::log('Parse: Request: Getting request from QUERY_STRING: ' . $_SERVER['QUERY_STRING']);
			Debug::log('Parse: Request: Split Query String: ' . var_export($query, true));
			
			// In case there is no real query string except the requested page.
			if (!isset($query[1])) {
				$query[1] = '';
			}
			
			// Rebuild correct $_GET array without requested page.
			parse_str($query[1], $_GET);
			
			// Remove request from QUERY_STRING.
			$_SERVER['QUERY_STRING'] = $query[1];
			
			Debug::log('Parse: Request: $_GET: ' . var_export($_GET, true));
			
		} else {
				
			// The requested page gets passed 'index.php/page/path'.
			// That can be the case if rewriting is disabled and AM_INDEX equals '/index.php'.
			if (isset($_SERVER['PATH_INFO'])) {
		
				$request = $_SERVER['PATH_INFO'];
				Debug::log('Parse: Request: Getting request from PATH_INFO');
	
			} else if (isset($_SERVER['ORIG_PATH_INFO'])) {	
	
				$request = $_SERVER['ORIG_PATH_INFO'];
				Debug::log('Parse: Request: Getting request from ORIG_PATH_INFO');
	
			} else if (isset($_SERVER['REQUEST_URI'])) {
		
				$request = trim(str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), '?');
				Debug::log('Parse: Request: Getting request from REQUEST_URI');
	
			} else if (isset($_SERVER['REDIRECT_URL'])) {
	
				$request = $_SERVER['REDIRECT_URL'];
				Debug::log('Parse: Request: Getting request from REDIRECT_URL');
			
			} else if (isset($_SERVER['PHP_SELF'])) {
	
				$request = $_SERVER['PHP_SELF'];
				Debug::log('Parse: Request: Getting request from PHP_SELF');
	
			}
			
		}
	
		// Remove unwanted components from the request.
		$request = str_replace(AM_BASE_URL, '', $request);
		$request = str_replace('/index.php', '', $request);
		
		// Remove trailing slash from URL to keep relative links consistent.
		if (substr($request, -1) == '/' && $request != '/') {
			header('Location: ' . AM_BASE_URL . AM_INDEX . rtrim($request, '/'), false, 301);
			die;
		}
		
		$request = '/' . trim($request, '/');
		
		Debug::log('Parse: Request: ' . $request);
		
		return $request; 
		
	}


	/**
	 *	Cleans up a string to be used as URL, directory or file name. 
	 *	The returned string constists of the following characters: a-z, A-Z, - and optional dots (.)
	 *	That means, this method is safe to be used with filenames as well, since it keeps by default the dots as suffix separators.
	 *
	 *	Note: To produce fully safe prefixes and directory names, 
	 *	possible dots should be removed by setting $removeDots = true. 
	 *
	 *	@param string $str
	 *	@param boolean $removeDots	
	 *	@return the sanitized string
	 */
	
	public static function sanitize($str, $removeDots = false) {
			
		// If dots should be removed from $str, replace them with '-', since URLify::filter() only removes them fully without replacing.
		if ($removeDots) {
			$str = str_replace('.', '-', $str);
		}
		
		// Convert slashes separately to avoid issues with regex in URLify.
		$str = str_replace('/', '-', $str);
		
		// Configure URLify. 
		// Add non-word chars and reset the remove list.
		// Note: $maps gets directly manipulated without using URLify::add_chars(). 
		// Using the add_chars() method would extend $maps every time, Parse::sanitize() gets called. 
		// Adding a new array to $maps using a key avoids that and just overwrites that same array after the first call without adding new elements.
		\JBroadway\URLify::$maps['nonWordChars'] = array('=' => '-', '&' => '-and-', '+' => '-plus-', '@' => '-at-', '|' => '-', '*' => '-x-');
		\JBroadway\URLify::$remove_list = array();
		
		// Since all possible dots got removed above (if $removeDots is true), 
		// $str should be filtered as filename to keep dots if there are still in $str. 
		return \JBroadway\URLify::filter($str, 100, '', true);
		
	}
	
	
	/**
	 * 	Parse Site Data to replace defaults.
	 *
	 *	Get all sitewide settings (like site name, the theme etc.) from the main settings file 
	 *	in the root of the /shared directory.
	 *
	 *	@return Array with the site's settings
	 */
	
	public static function siteData() {
		
		// Define default settings.
		// Use the server name as default site name and the first found theme folder as default theme.	
		$themes = glob(AM_BASE_DIR . AM_DIR_THEMES . '/*', GLOB_ONLYDIR);	
		$defaults = 	array(	
					AM_KEY_SITENAME => $_SERVER['SERVER_NAME'],
					AM_KEY_THEME => basename(reset($themes))  
				);
		
		// Merge defaults with settings from file.
		return array_merge($defaults, self::markdownFile(AM_FILE_SITE_SETTINGS));
		
	}


	/**
	 *	Load and buffer a template file and return its content as string. The Automad object gets passed as parameter to be available for all plain PHP within the included file.
	 *	This is basically the base method to load a template without parsing the Automad markup. It just gets the parsed PHP content.
	 *
	 *	Note that even when the it is possible to use plain PHP in a atemplate file, all that code will be parsed first when buffering, before any of the Automad markup is getting parsed.
	 *	That means also, that is not possible to make plain PHP code really interact with any of the Automad placeholder markup.
	 *
	 *	@param string $file
	 *	@param object $Automad
	 *	@return the parsed content 
	 */

	public static function templateBuffer($file, $Automad) {
		
		ob_start();
		@include $file;
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
		
	}


	/**
	 *	Call Toolbox methods and Extension dynamically with optional parameters. Additionally add all CSS/JS files of the matched extensions to the header.
	 *	For example t(Tool{JSON-options}) or x(Extension{JSON-options}).
	 *	The optional parameters have to be passed in (dirty) JSON format, like {key1: "String", key2: 10, ...}.
	 *	The parser understands dirty JSON, so wrapping the keys in double quotes is not needed.
	 *	
	 *	@param string $str (the string to be parsed)
	 *	@param object $Automad
	 *	@return The parsed $str
	 */

	public static function templateMethods($str, $Automad) {
				
		$Toolbox = new Toolbox($Automad);
		$Extender = new Extender($Automad);

		$use = 	array(
				'automad' => $Automad,
				'toolbox' => $Toolbox,  
				'extender' => $Extender
			);	
		
		// Scan $str for extensions and add all CSS & JS files for the matched classes to the HTML <head>.
		$str = $Extender->addHeaderElements($str);
		
		// Toolbox methods and Extensions MUST be parsed at the same time, since the Extensions use also Toolbox properties. 
		// Tools and Extensions must be able to influence each other - therefore the order of parsing is very important.
		return preg_replace_callback(AM_REGEX_METHODS, function($matches) use ($use) {
					
					$delimiter = $matches[1];
					$method = $matches[2];
					
					if (isset($matches[3])) {
						// Parse the options JSON and also find and replace included variables within the JSON string.
						$options = Parse::jsonOptions(Parse::templateVariables($matches[3], $use['automad'], true));
					} else {
						$options = array();
					}
					
					// Call methods depending on placeholder type (Toolbox or Extension)
					if ($delimiter == AM_PLACEHOLDER_TOOL) {
						
						// Toolbox method
						if (method_exists($use['toolbox'], $method)) {
					
							Debug::log('Parse: Matched tool: "' . $method . '" and passing the following options:');
							Debug::log($options);	
						
							return $use['toolbox']->$method($options);
						
						}
						
					} else {
						
						// Extension
						Debug::log('Parse: Matched extension: "' . $method . '"');
									
						return $use['extender']->callExtension($method, $options);
						
						
					}
					
				}, $str);		
	
	}


	/**
	 *	Scan a string for "i(filename.php)" to include template elements recursively. The Automad object gets passed as well to be able to access the site's data also form template elements included by i().
	 *
	 *	@param string $str (the string which has to be scanned)
	 *	@param string $directory (the base directory for including the files)
	 *	@param object $Automad
	 *	@return The recursively scanned output including the content of all matched includes.
	 */
	
	public static function templateNestedIncludes($str, $directory, $Automad) {
		
		$use = array('directory' => $directory, 'automad' => $Automad);
		
		return 	preg_replace_callback(AM_REGEX_INC, function($matches) use ($use) {
					
				$file = $use['directory'] . '/' . $matches[1];
				if (file_exists($file)) {
						
					Debug::log('Parse: Include: ' . $file);				
					$content = Parse::templateBuffer($file, $use['automad']);
					return Parse::templateNestedIncludes($content, dirname($file), $use['automad']);
						
				}
					
			}, $str);
		
	}
	

	/**
	 *	Replace all site vars "s(variable)" with content from site.txt and all page vars "p(variable)" with content from the current page.
	 *	Optionally all values can be parsed as "JSON safe", by stripping all quotes and wrapping each value in double quotes.
	 *
	 *	@param string $str
	 *	@param object $Automad
	 *	@param boolean $jsonSafe (if true, all quotes get removed from the variable values and the values get wrapped in double quotes, to avoid parsing errors, when a value is empty "")
	 *	@return The parsed $str
	 */
	
	public static function templateVariables($str, $Automad, $jsonSafe = false) {
		
		// Site variables
		$use = array('automad' => $Automad, 'jsonSafe' => $jsonSafe);
		$str = preg_replace_callback(AM_REGEX_SITE_VAR, function($matches) use ($use) {
					
					if ($use['jsonSafe']) {
						return '"' . Parse::jsonEscape($use['automad']->getSiteData($matches[1])) . '"';
					} else {
						return $use['automad']->getSiteData($matches[1]);
					}
								
				}, $str);
		
		// Page variables
		$Page = $Automad->getCurrentPage();
		$use = array('data' => $Page->data, 'jsonSafe' => $jsonSafe);
		$str = preg_replace_callback(AM_REGEX_PAGE_VAR, function($matches) use ($use) {
						
					if (array_key_exists($matches[1], $use['data'])) {
						
						if ($use['jsonSafe']) {
							return '"' . Parse::jsonEscape($use['data'][$matches[1]]) . '"';
						} else {
							return $use['data'][$matches[1]];
						}
						
					} else {
				
						if ($use['jsonSafe']) { 
							return '""';
						}	
				
					}
							
				}, $str);
						
		return $str;

	}
	
	
	/**
	 *	Loads and parses a text file.
	 *
	 *	First it separates the different blocks into simple key/value pairs.
	 *	Then it creates an array of vars by splitting the pairs. 
	 * 
	 *	@param string $file
	 *	@return array $vars
	 */
	 
	public static function textFile($file) {
		
		$vars = array();
			
		// Get file content and normalize line breaks.
		$content = preg_replace('/\r\n?/', "\n", file_get_contents($file));	
			
		// Split $content into data blocks on every line only containing one or more AM_PARSE_BLOCK_SEPARATOR and whitespace, followed by a key in a new line. 
		$pairs = preg_split('/\n' . preg_quote(AM_PARSE_BLOCK_SEPARATOR) . '+\s*\n(?=[\w\.\-]+' . preg_quote(AM_PARSE_PAIR_SEPARATOR) . ')/s', $content);
		
		// Split $pairs into an array of vars.
		foreach ($pairs as $pair) {
		
			list($key, $value) = explode(AM_PARSE_PAIR_SEPARATOR, $pair, 2);
			$vars[trim($key)] = trim($value);	
			
		}
		
		return $vars;
		
	}
 

}
 
 
?>
