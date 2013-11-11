<?php defined('AUTOMAD') or die('Direct access not permitted!');
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


/**
 * 	The Template class holds all methods to render the current page using a template file.
 *	
 *	When render() is called the output buffer gets started and the template file gets loaded.
 *	The output of the included file, basically the raw template HTML (including the generated HTML by PHP in the template file) 
 *	gets stored in $output.
 *
 *	In a second step $output gets processed. All variables get replaced with values from the page's text file and 
 *	all $[function]s get replaced with the return values of the matching methods of the Tool class.
 *	
 *	That way, it is possible that the template.php file can include HTML as well as PHP, while the "user-generated" content in the text files 
 *	can not have any executable code (PHP). There are no "eval" functions needed, since all the PHP gets only included from the template files,
 *	which should not be edited by users anyway.
 *
 *	All the replaced functions in the template file provide an easy way for designing a template file without any PHP knowledge. 
 *	The processTemplate() method checks, if a found $[function] in the template file matches a method of the Tool class to then repalce 
 *	that match with the method's return value.  
 *
 *	In a last step the processed $output get displayed.
 */


class Template {
	
	
	/**
	 * 	The Site object.
	 */
	
	private $S;
	
	
	/**
	 * 	The current Page object.
	 */

	private $P;
	
	
	/**
	 *	Load the unmodified template file and return its output.
	 *
	 *	@return $ouput 
	 */
	
	private function getTemplate() {
		
		ob_start();
		require_once $this->P->getTemplatePath($this->S->getThemePath());
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	
	}
	
		
	/**
	 * 	Replace all vars in template with values from $this->currentPage and all function shortcuts with functions from the Tool class.
	 *
	 *	@param string $output
	 *	@return string $output
	 */
	
	private function processTemplate($output) {

		// Call functions dynamically with optional parameter in () or without () for no options.
		// For example $[function(parameter)] or just $[function]
		$tool = new Tool($this->S); 
		$output = 	preg_replace_callback('/' . preg_quote(TEMPLATE_FN_DELIMITER_LEFT) . '([A-Za-z0-9_\-]+)(\(.*\))?' . preg_quote(TEMPLATE_FN_DELIMITER_RIGHT) . '/', 
				function($matches) use($tool) {
					if (method_exists($tool, $matches[1])) {
						if (!isset($matches[2])) {
							// If there is no parameter passed (no brackets),
							// an empty string will be passed as an argument
							$matches[2] = '';
						}
						return $tool->$matches[1](trim($matches[2],'()'));
					}
				}, 
				$output);
							
		// Replace vars in data array			
		$data = $this->P->data;
		$output =	preg_replace_callback('/' . preg_quote(TEMPLATE_VAR_DELIMITER_LEFT) . '([A-Za-z0-9_\-]+)' . preg_quote(TEMPLATE_VAR_DELIMITER_RIGHT) . '/',
				function($matches) use($data) {
					if (array_key_exists($matches[1], $data)) {
						return $data[$matches[1]];
					}
				},
				$output);
				
		return $output;
		
	}
	
	
	/**
	 *	Find all links/URLs in $output and modulate the matches according to their type.
	 * 
	 *	Absolute URLs: 		not modified
	 *	Root-relative URLs: 	BASE_URL is prepended (and INDEX in case of pages)
	 *	Relative URLs:		Only URLs of files are modified - the full file system path gets prepended
	 *	
	 *	@param string $output
	 *	@return $output
	 */
	
	private function modulateUrls($output) {
		
		$P = $this->P;
		$output = 	preg_replace_callback('/(action|href|src)="(.+?)"/',
				function($match) use ($P) {
					
					$url = $match[2];
					
					if (strpos($url, '://') !== false) {
												
						// Absolute URL
						return $match[0];
						
					} else if (strpos($url, '/') === 0) {
						
						// Relative to root	
						if (Parse::isFileName($url)) {
							return $match[1] . '="' . BASE_URL . $url . '"';
						} else {
							return $match[1] . '="' . BASE_URL . INDEX . $url . '"';	
						}
												
					} else {
						
						// Just a relative URL
						if (Parse::isFileName($url)) {
							// Remove double slash when relPath is empty.
							$path = ltrim($P->relPath . '/', '/');
							return $match[1] . '="' . BASE_URL . SITE_PAGES_DIR . '/' . $path . $url . '"';
						} else {
							return $match[0];
						}
						
					}
					
				},
				$output);
	
		return $output;
		
	}
	
	
	/**
	 * 	Renders the current page.
	 */
	
	public function render() {
		
		$C = new Cache();
		
		if ($C->cacheIsApproved()) {
		
			// If cache is up to date and the cached file exists,
			// just get the page from the cache.
			echo $C->readCache();
			
		} else {
			
			// If the cache is not approved,
			// everything has to be re-rendered.
			$this->S = new Site();
			$this->P = $this->S->getCurrentPage();
		
			$output = $this->getTemplate();
			$output = $this->processTemplate($output);
			$output = $this->modulateUrls($output);	
			
			// Write the rendered HTML to the cache.
			$C->writeCache($output);
		
			echo $output;
			
			Debug::pr('Template: Theme path: ' . $this->S->getThemePath());
			Debug::pr('Template: Template file: ' . $this->P->getTemplatePath($this->S->getThemePath()));
			Debug::pr(array_keys($this->S->getCollection()));
		
		} 	
		
		Debug::pr('Template: BASE_URL: ' . BASE_URL);
		Debug::pr('Template: Pretty URLs: ' . var_export(!(boolean)INDEX, true));
		Debug::pr(get_defined_constants(true)['user']);
				
	}	
	
	
}


?>
