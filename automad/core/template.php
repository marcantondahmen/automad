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
 * 	The Template class hold all methods to render the current page using a template file.
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
	 * 	Replace all vars in template with values from $this->currentPage and all function shortcuts with functions from the Html class.
	 *
	 *	@param string $content
	 *	@return string $content
	 */
	
	private function processTemplate($content) {

		// Call functions dynamically with optional parameter in () or without () for no options.
		// For example $[function(parameter)] or just $[function]
		$html = new Html($this->S); 
		$content = 	preg_replace_callback('/' . preg_quote(TEMPLATE_FN_DELIMITER_LEFT) . '([A-Za-z0-9_\-]+)(\([A-Za-z0-9\/_\-]*\))?' . preg_quote(TEMPLATE_FN_DELIMITER_RIGHT) . '/', 
				function($matches) use($html) {
					if (method_exists($html, $matches[1])) {
						if (!isset($matches[2])) {
							// If there is no parameter passed (no brackets)
							$matches[2] = '';
						}
						return $html->$matches[1](trim($matches[2],'()'));
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
