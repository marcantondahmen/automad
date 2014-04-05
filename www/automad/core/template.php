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
		$this->template = $this->P->getTemplate();
		
		Debug::log('Template: New instance created!');
		Debug::log('Template: Current Page:');
		Debug::log($this->P);
		
	}
	

	/**
	 *	Add Meta tags to the head of $output.
	 *
	 *	@param string $output
	 *	@return $output
	 */
	
	private function addMetaTags($output) {
		
		$meta =  "\n\t" . '<meta name="Generator" content="Automad ' . AM_VERSION . '">';
		
		return str_replace('<head>', '<head>' . $meta, $output);
		
	}
	
	
	/**
	 *	Call Extension dynamically with optional parameters and add all found CSS/JS files to the header.
	 *	For example x(Extension{JSON-options}).
	 *	The optional parameters have to be passed in (dirty) JSON format, like {key1: "String", key2: 10, ...}.
	 *	The parser understands dirty JSON, so wrapping the keys in double quotes is not needed.
	 *	
	 *	@param string $output (the buffered output of the template file)
	 *	@return The parsed $output
	 */
	
	private function extensions($output) {
		
		$extender = new Extender($this->S);
		
		// Scan $output for extensions and add all CSS & JS files for the matched classes to the HTML <head>.
		$output = $extender->addHeaderElements($output);
		
		// Call extension methods. Match: x(Extension{Options})
		// The options have to be passed in (dirty) JSON format, like {key1: "String", key2: 10, ...}
		$output = 	preg_replace_callback('/' . preg_quote(AM_TMPLT_DEL_XTNSN_L) . '\s*([A-Za-z0-9_\-]+)\s*({.*?})?\s*' . preg_quote(AM_TMPLT_DEL_XTNSN_R) . '/s', 
				function($matches) use($extender) {
				
					if (!isset($matches[2])) {
						// If there are no options passed.
						$matches[2] = false;
					}
				
					$options = Parse::toolOptions($matches[2]);	
					
					Debug::log('Template: Matched extension: "' . $matches[1] . '"');				
					return $extender->callExtension($matches[1], $options);
				
				}, 
				$output);
				
		return $output;
		
	}
	
		
	/**
	 *	Load the unmodified template file and return its output.
	 *
	 *	@param string $template
	 *	@return $output 
	 */
	
	private function loadTemplate($template) {
		
		ob_start();
		require_once $template;
		$output = ob_get_contents();
		ob_end_clean();
		
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
		
		$pagePath = $this->P->path;
		$output = 	preg_replace_callback('/(action|href|src)="(.+?)"/',
				function($match) use ($pagePath) {
					return $match[1] . '="' . Modulate::url($pagePath, $match[2]) . '"';
				},
				$output);
	
		return $output;
		
	}
	
	
	/**
	 *	Obfuscate all eMail addresses matched in $output.
	 *	
	 *	@param string $output
	 *	@return $output
	 */
	
	private function obfuscateEmails($output) {
		
		$output = 	preg_replace_callback('/([\w\d\._\+\-]+@([a-zA-Z_\-\.]+)\.[a-zA-Z]{2,6})/', 
				function($matches) {
				
					Debug::log('Template: Obfuscating: ' . $matches[1]);
					
					$html = '<a href="#" onclick="this.href=\'mailto:\'+ this.innerHTML.split(\'\').reverse().join(\'\')" style="unicode-bidi:bidi-override;direction:rtl">';
					$html .= strrev($matches[1]);
					$html .= "</a>&#x200E;";
		
					return $html;
					
				}, 
				$output);
				
		return $output;
				
	}
	
	
	/**
	 *	Replace all page vars "p(variable)" with content from the current page.
	 *
	 *	@param string $output
	 *	@return The parsed $output
	 */
	
	private function pageVars($output) {
		
		$data = $this->P->data;
		
		return	preg_replace_callback('/' . preg_quote(AM_TMPLT_DEL_PAGE_VAR_L) . '\s*([A-Za-z0-9_\-]+)\s*' . preg_quote(AM_TMPLT_DEL_PAGE_VAR_R) . '/',
				function($matches) use($data) {	
					if (array_key_exists($matches[1], $data)) {
						return $data[$matches[1]];
					}
				},
				$output);
					
	}
	
	
	/**
	 * 	Render the current page.
	 *
	 *	@return The fully rendered HTML for the current page.
	 */
	
	public function render() {
		
		Debug::log('Template: Render template: ' . $this->template);
		
		$output = $this->loadTemplate($this->template);
		
		$output = Parse::templateNestedIncludes($output, dirname($this->template));
		
		$output = $this->tools($output);
		$output = $this->extensions($output);
		$output = $this->pageVars($output);
		$output = $this->siteVars($output);
		
		$output = $this->addMetaTags($output);
		$output = $this->modulateUrls($output);	
		$output = $this->obfuscateEmails($output);
	
		return $output;	
		
	}	
	
	
	/**
	 *	Replace all site vars "s(variable)" with content from site.txt.
	 *
	 *	@param string $output
	 *	@return The parsed $output
	 */
	
	private function siteVars($output) {
			
		$site = $this->S;
			
		return	preg_replace_callback('/' . preg_quote(AM_TMPLT_DEL_SITE_VAR_L) . '\s*([A-Za-z0-9_\-]+)\s*' . preg_quote(AM_TMPLT_DEL_SITE_VAR_R) . '/',
				function($matches) use($site) {
					return $site->getSiteData($matches[1]);				
				},
				$output);
	
	}

	
	/**
	 *	Call Toolbox methods dynamically with optional parameters.
	 *	For example t(function{JSON-options}) or just t(function).
	 *	The optional parameters have to be passed in (dirty) JSON format, like {key1: "String", key2: 10, ...}.
	 *	The parser understands dirty JSON, so wrapping the keys in double quotes is not needed.
	 *	
	 *	@param string $output (the buffered output of the template file)
	 *	@return The parsed $output
	 */
	
	private function tools($output) {
		
		$toolbox = new Toolbox($this->S);
		
		return 	preg_replace_callback('/' . preg_quote(AM_TMPLT_DEL_TOOL_L) . '\s*([A-Za-z0-9_\-]+)\s*({.*?})?\s*' . preg_quote(AM_TMPLT_DEL_TOOL_R) . '/s', 
				function($matches) use($toolbox) {	
					if (method_exists($toolbox, $matches[1])) {
					
						if (!isset($matches[2])) {
							// If there is no parameter passed (no brackets),
							// an empty string will be passed as an argument
							$matches[2] = false;
						}
						
						$options = Parse::toolOptions($matches[2]);	
						Debug::log('Template: Matched tool: "' . $matches[1] . '"');
						return $toolbox->$matches[1]($options);
						
					}
				}, 
				$output);		

	}
	
	
}


?>
