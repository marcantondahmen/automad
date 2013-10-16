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
 *	(c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
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
		foreach ($this->P->data as $key => $value) {
			$content = preg_replace('/' . preg_quote(TEMPLATE_VAR_DELIMITER_LEFT) . '(' . $key . ')' . preg_quote(TEMPLATE_VAR_DELIMITER_RIGHT) . '/', $value, $content);	
		}
		
		// Delete all undefined variables in template
		$content = preg_replace('/' . preg_quote(TEMPLATE_VAR_DELIMITER_LEFT) . '[A-Za-z0-9_\-]+' . preg_quote(TEMPLATE_VAR_DELIMITER_RIGHT) . '/', '', $content);
				
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
		
		$html = $this->processTemplate($content);
		
		echo $html;		
		
	}	
	
	
}


?>
