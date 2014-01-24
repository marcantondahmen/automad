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


// The namespace for all extensions.
namespace Extensions;


/**
 *	The Slider extension is a good example to understand, how extensions work.
 *	It creates a simple slideshow of images based on a glob pattern.
 */


class Slider {
	
		
	/**
	 *	Every extension has one main method which will be called when parsing a template file.
	 *	The name of that method is the same name as the name of the class and subnamespace (case insensitive).
	 *	To group all files for an extension (.css and .js) the .php file of the class gets simply the same name as the containing folder:
	 *	/extensions/slider/slider.php
	 *
	 *	All .css and .js files placed within /extensions/slider get then automatically appended to the <head> section of the page when 
	 *	using the extension within a template file.
	 *	
	 *	In this case the naming pattern looks like:
	 *	- namespace: 	Extensions
	 *	- directory:	/extensions/slider
	 *	- class file:	/extensions/slider/slider.php
	 *	- class: 	Slider
	 *	- method:	Slider 
	 *	
	 *	This main method must always have two parameters, which will be passed automatically when calling the extension: $obj->Slider($options, $site)
	 *	- $options:	An array with all the options
	 *	- $site:	The Site object, to make all data available for the extension
	 *	
	 *	The example method below basically generates and returns the HTML for a simple image slideshow.
	 *	The main method of an extension must always return the output for the template.
	 *
	 *	Note: The Slider method is not a kind of constructor (like it would be in PHP 4). Since this is a namespaced class,
	 *	a method with the same name as the last part of the namespace isn't called when creating an instance of the class (PHP 5.3+).
	 *
	 *	@param array $options
	 *	@param object $site
	 *	@return The generated HTML of the slider. 
	 */
	
	public function Slider($options, $site) {
		
		$defaults = 	array(
					'glob' => '*.jpg',
					'width' => 400,
					'height' => 300,
					'duration' => 3000
				);
		
		// Merge defaults with options
		$options = array_merge($defaults, $options);
		
		// Build full glob pattern
		$page = $site->getCurrentPage();
		$glob = \Core\Modulate::filePath($page->path, $options['glob']);
		
		// Get files.
		$files = glob($glob);
		
		// The duration option gets passed as a data attribute.
		$html = '<div class="slider" data-duration="' . $options['duration'] . '" style="width: ' . $options['width'] . 'px; height: ' . $options['height'] . 'px;">';
				
		foreach ($files as $file) {
			
			$html .= \Core\Html::addImage($file, $options['width'], $options['height'], true);
						
		}
		
		$html .= '</div>';
				
		return $html;
		
	}
	
	
}


?>