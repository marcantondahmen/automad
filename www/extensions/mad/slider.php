<?php
/**
 *	SLIDER
 *	Extension for the Automad CMS
 *
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Mad;


/**
 *	The Slider extension is a good starting point to understand, how extensions work.
 *	It creates a simple slideshow of images based on a glob pattern.
 */


class Slider {
	
	
	/**
	 *	The Site object.
	 */
	
	private $S;
	
	
	/**
	 *	The constructor must only (!) have the $site parameter.
	 *	When calling any extension method, a new instance of the extension is created and the Site object gets passed from the Extender.
	 *
	 *	@param object $site
	 */
	
	public function __construct($site) {
	
		// $this->S stores all (!) site information.
		$this->S = $site;
		
	}
	
	
	/**
	 *	Any method within an extension must only have the $options parameter.
	 *	The $options parameter is an array containing all passed parameters in the template file. 
	 *	
	 *	This method basically generates and returns the HTML for a simple image slideshow.
	 *
	 *	@param array $options
	 *	@return The generated HTML of the slider. 
	 */
	
	public function generate($options) {
		
		$defaults = 	array(
					'glob' => '*.jpg',
					'width' => 400,
					'height' => 300,
					'duration' => 3000
				);
		
		// Merge defaults with options
		$options = array_merge($defaults, $options);
		
		// Make pixels integer values
		foreach (array('width', 'height') as $key) {
			$options[$key] = intval($options[$key]);
		}
		
		// Build full glob pattern
		$P = $this->S->getCurrentPage();
		$glob = \Core\Modulate::filePath($P->path, $options['glob']);
		
		// Get files.
		$files = glob($glob);
		
		// The duration option gets passed as a data attribute.
		$html = '<div class="slider" data-duration="' . $options['duration'] . '">';
				
		foreach ($files as $file) {
			
			$html .= \Core\Html::addImage($file, $options['width'], $options['height'], true);
						
		}
		
		$html .= '</div>';
				
		return $html;
		
	}
	
	
}


?>