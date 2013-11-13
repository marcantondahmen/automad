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
 *	When render() is called, first the template file gets loaded by loadTemplate().
 *	The output, basically the raw template HTML (including the generated HTML by PHP in the template file) 
 *	gets stored in $output.
 *
 *	In a second step $output gets processed. All variables get replaced with values from the page's text file and 
 *	all $[function]s get replaced with the return values of the matching methods of the Toolbox class.
 *	
 *	That way, it is possible that the template.php file can include HTML as well as PHP, while the "user-generated" content in the text files 
 *	can not have any executable code (PHP). There are no "eval" functions needed, since all the PHP gets only included from the template files,
 *	which should not be edited by users anyway.
 *
 *	All the replaced functions in the template file provide an easy way for designing a template file without any PHP knowledge. 
 *	The processTemplate() method checks, if a found $[function] in the template file matches a method of the Toolbox class to then repalce 
 *	that match with the method's return value.  
 *
 *	In a last step, all URLs within the generated HTML get modulated to the be relative to the server's root (or absolute), before $output gets returned.
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
	 *	The template file for the current page.
	 */
	
	private $template;

	
	/**
	 *	Define $S, $P and $theme.
	 */
	
	public function __construct($site) {
		
		$this->S = $site;
		$this->P = $site->getCurrentPage();
		$this->template = $this->P->getTemplatePath($this->S->getThemePath());
		
		Debug::pr('Template: New instance created!');
		
	}
		
	
	/**
	 *	Load the unmodified template file and return its output.
	 *
	 *	@param string $template
	 *	@return $ouput 
	 */
	
	private function loadTemplate($template) {
		
		ob_start();
		require_once $template;
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	
	}
	
		
	/**
	 * 	Replace all vars in template with values from $this->currentPage and all function shortcuts with functions from the Toolbox class.
	 *
	 *	@param string $output
	 *	@return string $output
	 */
	
	private function processTemplate($output) {

		// Call functions dynamically with optional parameter in () or without () for no options.
		// For example $[function(parameter)] or just $[function]
		$toolbox = new Toolbox($this->S); 
		$output = 	preg_replace_callback('/' . preg_quote(AM_TMPLT_DEL_TOOL_L) . '([A-Za-z0-9_\-]+)(\(.*\))?' . preg_quote(AM_TMPLT_DEL_TOOL_R) . '/', 
				function($matches) use($toolbox) {
					if (method_exists($toolbox, $matches[1])) {
						if (!isset($matches[2])) {
							// If there is no parameter passed (no brackets),
							// an empty string will be passed as an argument
							$matches[2] = '';
						}
						return $toolbox->$matches[1](trim($matches[2],'()'));
					}
				}, 
				$output);
							
		// Replace vars in data array			
		$data = $this->P->data;
		$output =	preg_replace_callback('/' . preg_quote(AM_TMPLT_DEL_VAR_L) . '([A-Za-z0-9_\-]+)' . preg_quote(AM_TMPLT_DEL_VAR_R) . '/',
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
	 *	Root-relative URLs: 	AM_BASE_URL is prepended (and AM_INDEX in case of pages)
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
							return $match[1] . '="' . AM_BASE_URL . $url . '"';
						} else {
							return $match[1] . '="' . AM_BASE_URL . AM_INDEX . $url . '"';	
						}
												
					} else {
						
						// Just a relative URL
						if (Parse::isFileName($url)) {
							// Remove double slash when relPath is empty.
							$path = ltrim($P->relPath . '/', '/');
							return $match[1] . '="' . AM_BASE_URL . AM_DIR_PAGES . '/' . $path . $url . '"';
						} else {
							return $match[0];
						}
						
					}
					
				},
				$output);
	
		return $output;
		
	}
	
	
	/**
	 * 	Render the current page.
	 *
	 *	@return The fully rendered HTML for the current page.
	 */
	
	public function render() {
		
		Debug::pr('Template: Render template: ' . $this->template);
		
		$output = $this->loadTemplate($this->template);
		$output = $this->processTemplate($output);
		$output = $this->modulateUrls($output);	
	
		return $output;	
		
	}	
	
	
}


?>
