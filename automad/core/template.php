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
		
		require_once $this->P->getTemplatePath($this->S->getTheme());
		
	}
	
	
	/**
	 * 	Replace all vars in template with values from $this->currentPage.
	 *
	 *	@param string $content
	 *	@return string $content
	 */
	
	private function processTemplate($content) {
				
		// Replace vars in data array		
		foreach ($this->P->data as $key => $value) {
			$content = preg_replace('/' . preg_quote(TEMPLATE_VAR_DELIMITER_LEFT) . '(' . $key . ')' . preg_quote(TEMPLATE_VAR_DELIMITER_RIGHT) . '/', $value, $content);	
		}
		
		// Delete all undefined variable in template
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
