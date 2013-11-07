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
 *	gets stored in $content.
 *
 *	In a second step $content gets processed. All variables get replaced with values from the page's text file and 
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
 *	In a last step the processed $content get displayed.
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
		
	
	public function __construct() {
		
		$this->S = new Site();
		$this->P = $this->S->getCurrentPage();
		
	}


	/**
	 * 	Load page template.
	 */
	
	private function loadTemplate() {
		
		require_once $this->P->getTemplatePath($this->S->getThemePath());
		
	}
		
	
	/**
	 * 	Replace all vars in template with values from $this->currentPage and all function shortcuts with functions from the Tool class.
	 *
	 *	@param string $content
	 *	@return string $content
	 */
	
	private function processTemplate($content) {

		// Call functions dynamically with optional parameter in () or without () for no options.
		// For example $[function(parameter)] or just $[function]
		$tool = new Tool($this->S); 
		$content = 	preg_replace_callback('/' . preg_quote(TEMPLATE_FN_DELIMITER_LEFT) . '([A-Za-z0-9_\-]+)(\(.*\))?' . preg_quote(TEMPLATE_FN_DELIMITER_RIGHT) . '/', 
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
				$content);
							
		// Replace vars in data array			
		$data = $this->P->data;
		$content =	preg_replace_callback('/' . preg_quote(TEMPLATE_VAR_DELIMITER_LEFT) . '([A-Za-z0-9_\-]+)' . preg_quote(TEMPLATE_VAR_DELIMITER_RIGHT) . '/',
				function($matches) use($data) {
					if (array_key_exists($matches[1], $data)) {
						return $data[$matches[1]];
					}
				},
				$content);
				
		return $content;
		
	}
	
	
	/**
	 *	Find all links/URLs in $content and modulate the matches according to their type.
	 * 
	 *	Absolute URLs: 		not modified
	 *	Root-relative URLs: 	BASE_URL is prepended (and INDEX in case of pages)
	 *	Relative URLs:		Only URLs of files are modified - the full file system path gets prepended
	 *	
	 *	@param string $content
	 *	@return $content
	 */
	
	private function modulateUrls($content) {
		
		$P = $this->P;

		$content = 	preg_replace_callback('/(action|href|src)="(.+?)"/',
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
							return $match[1] . '="' . BASE_URL . SITE_PAGES_DIR . '/' . $P->relPath . '/' . $url . '"';
						} else {
							return $match[0];
						}
						
					}
					
				},
				$content);
	
		return $content;
		
	}
	
	
	/**
	 * 	Renders the current page.
	 */
	
	public function render() {
		
		ob_start();
		$this->loadTemplate();
		$content = ob_get_contents();
		ob_end_clean();
		
		$content = $this->processTemplate($content);	
		$content = $this->modulateUrls($content);
			
		echo $content;
		
		Debug::pr('BASE_URL: ' . BASE_URL);
		Debug::pr('Pretty URLs: ' . var_export(!(boolean)INDEX, true));
		Debug::pr('Theme: ' . $this->S->getThemePath());
		Debug::pr('Template: ' . $this->P->getTemplatePath($this->S->getThemePath()));
		Debug::pr(get_defined_constants(true)['user']);
		Debug::pr($this->S);	
		
	}	
	
	
}


?>
