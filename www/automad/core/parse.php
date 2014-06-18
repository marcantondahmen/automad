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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');
 
 
/**
 *	The Parse class holds all parsing methods.
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
	 *	In a second step, all URLs are checked and fixed, in case they point to the website's root.
	 *	
	 *	@param string $file
	 *	@return Array of variables
	 */
	
	public static function markdownFile($file) {
		
		$vars = self::textFile($file);
			
		$vars = array_map(function($var) {
			 
				if (strpos($var, "\n") !== false) {
					// If $var has line breaks (is multiline)
					return \Lib\Michelf\MarkdownExtra::defaultTransform($var); 
				} else {
					// If $var is a single line, skip parsing
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
	 *	Cleans up a string to be used as URL, directory or file name. 
	 *	The returned string constists of the following characters: a-z, A-Z, -, _ and a dot (.)
	 *	That means, this method is safe to be used with filenames as well, since it keeps the dots as suffix separators.
	 *
	 *	Note: To produce fully safe prefixes and directory names, 
	 *	possible dots should be removed separatly from this method by just calling the standard str_replace('.', '_', $str) before. 
	 *
	 *	@param string $str
	 *	@return $str
	 */
	
	public static function sanitize($str) {
		
		$search  = array('&'  ,'/','*','+'  ,'@'   ,'ä','ö','ü','å','ø','á','à','é','è','Ä','Ö','Ü','Å','Ø','Á','À','É','È');
		$replace = array('and','-','x','and','_at_','a','o','u','a','o','a','a','e','e','A','O','U','A','O','A','A','E','E');
		
		return preg_replace('/[^\w\.\-]/', '_', strtolower(str_replace($search, $replace, trim($str))));
		
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
		$defaults = 	array(	
					AM_KEY_SITENAME => $_SERVER['SERVER_NAME'],
					AM_KEY_THEME => basename(reset(glob(AM_BASE_DIR . AM_DIR_THEMES . '/*', GLOB_ONLYDIR)))  
				);
		
		// Merge defaults with settings from file.
		return array_merge($defaults, self::markdownFile(AM_FILE_SITE_SETTINGS));
		
	}


	/**
	 *	Call Toolbox methods and Extension dynamically with optional parameters. Additionally add all CSS/JS files of the matched extensions to the header.
	 *	For example t(Tool{JSON-options}) or x(Extension{JSON-options}).
	 *	The optional parameters have to be passed in (dirty) JSON format, like {key1: "String", key2: 10, ...}.
	 *	The parser understands dirty JSON, so wrapping the keys in double quotes is not needed.
	 *	
	 *	@param string $str (the string to be parsed)
	 *	@param object $Site
	 *	@return The parsed $str
	 */

	public static function templateMethods($str, $Site) {
				
		$Toolbox = new Toolbox($Site);
		$Extender = new Extender($Site);

		$use = 	array(
				'site' => $Site,
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
						$options = self::jsonOptions(self::templateVariables($matches[3], $use['site'], true));
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
	 *	Scan a string for "i(filename.php)" to include template elements recursively.
	 *
	 *	@param string $str (the string which has to be scanned)
	 *	@param string $directory (the base directory for including the files)
	 *	@return The recursively scanned output including the content of all matched includes.
	 */
	
	public static function templateNestedIncludes($str, $directory) {
		
		return 	preg_replace_callback(AM_REGEX_INC, function($matches) use ($directory) {
					
				$file = $directory . '/' . $matches[1];
				if (file_exists($file)) {
						
					Debug::log('Parse: Include: ' . $file);
					ob_start();
					include $file;
					$content = ob_get_contents();
					ob_end_clean();
					return self::templateNestedIncludes($content, dirname($file));
						
				}
					
			}, $str);
		
	}
	

	/**
	 *	Replace all site vars "s(variable)" with content from site.txt and all page vars "p(variable)" with content from the current page.
	 *	Optionally all values can be parsed as "JSON safe", by stripping all quotes and wrapping each value in double quotes.
	 *
	 *	@param string $str
	 *	@param object $Site
	 *	@param boolean $jsonSafe (if true, all quotes get removed from the variable values and the values get wrapped in double quotes, to avoid parsing errors, when a value is empty "")
	 *	@return The parsed $str
	 */
	
	public static function templateVariables($str, $Site, $jsonSafe = false) {
		
		// Site variables
		$use = array('site' => $Site, 'jsonSafe' => $jsonSafe);
		$str = preg_replace_callback(AM_REGEX_SITE_VAR, function($matches) use ($use) {
					
					if ($use['jsonSafe']) {
						return '"' . self::jsonEscape($use['site']->getSiteData($matches[1])) . '"';
					} else {
						return $use['site']->getSiteData($matches[1]);
					}
								
				}, $str);
		
		// Page variables
		$Page = $Site->getCurrentPage();
		$use = array('data' => $Page->data, 'jsonSafe' => $jsonSafe);
		$str = preg_replace_callback(AM_REGEX_PAGE_VAR, function($matches) use ($use) {
						
					if (array_key_exists($matches[1], $use['data'])) {
						
						if ($use['jsonSafe']) {
							return '"' . self::jsonEscape($use['data'][$matches[1]]) . '"';
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
		
		$text = file_get_contents($file);
		
		// Define full delimiter for splitting the data blocks.
		// So basically every line, only containing a single AM_PARSE_BLOCK_SEPARATOR, will be used to explode $text.
		$del = "\n" . AM_PARSE_BLOCK_SEPARATOR . "\n";
	
		// Normalize line endings and remove trailing whitespace from AM_PARSE_BLOCK_SEPARATOR to match $del.
		// So, one AM_PARSE_BLOCK_SEPARATOR, followed by optional spaces and wrapped in new line charachters
		// will be replaced with the simplified $del.
		// "\R" is used to match all line endings (CRLF, LF, CR).
		$text = preg_replace('/\R' . preg_quote(AM_PARSE_BLOCK_SEPARATOR) . '\s*\R/s', $del, $text);
			
		// split $file into data blocks
		$pairs = explode($del, $text);
		
		// split $pairs into an array of vars
		$vars = array();
		foreach ($pairs as $pair) {
		
			list($key, $value) = explode(AM_PARSE_PAIR_SEPARATOR, $pair, 2);
			$vars[trim($key)] = trim($value);	
			
		}
		
		return $vars;
		
	}
 

}
 
 
?>
