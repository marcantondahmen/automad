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
	 * 	The current Page object.
	 */

	private $Page;
	
	
	/**
	 *	The template file for the current page.
	 */
	
	private $template;
	
	
	/**
	 *	Define $Automad and $Page, check if the page gets redirected and get the template name. 
	 */
	
	public function __construct($Automad) {
		
		$this->Automad = $Automad;
		$this->Page = $Automad->getCurrentPage();
		
		// Redirect page, if an URL is defined.
		if (isset($this->Page->data[AM_KEY_URL])) {
			header('Location: ' . Resolve::url($this->Page, $this->Page->url));
			die;
		}
	
		$this->template = $this->Page->getTemplate();
		
		Debug::log('Template: New instance created!');
		Debug::log('Template: Current Page:');
		Debug::log($this->Page);
		
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
	 *	Find all links/URLs in $output and resolve the matches according to their type.
	 *	
	 *	@param string $output
	 *	@return $output
	 */
	
	private function resolveUrls($output) {
		
		$Page = $this->Page;
		
		// action, href and src
		$output = 	preg_replace_callback('/(action|href|src)="(.+?)"/',
				function($match) use ($Page) {
					return $match[1] . '="' . Resolve::url($Page, $match[2]) . '"';
				},
				$output);
				
		// Inline styles (like background-image)
		$output = 	preg_replace_callback('/url\(\'(.+?)\'\)/',
				function($match) use ($Page) {
					return 'url(\'' . Resolve::url($Page, $match[1]) . '\')';
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
		
		$output = 	preg_replace_callback('/(?<!mailto:)\b([\w\d\._\+\-]+@([a-zA-Z_\-\.]+)\.[a-zA-Z]{2,6})/', 
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
	 * 	Render the current page.
	 *
	 *	@return The fully rendered HTML for the current page.
	 */
	
	public function render() {
		
		Debug::log('Template: Render template: ' . $this->template);
		
		$output = Parse::templateBuffer($this->template, $this->Automad);
		$output = Parse::templateNestedIncludes($output, dirname($this->template), $this->Automad);
		$output = Parse::templateMethods($output, $this->Automad);
		$output = Parse::templateVariables($output, $this->Automad);
		
		$output = $this->addMetaTags($output);
		$output = $this->resolveUrls($output);	
		$output = $this->obfuscateEmails($output);
	
		return $output;	
		
	}	
		
	
}


?>
