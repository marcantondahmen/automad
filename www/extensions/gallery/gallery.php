<?php
/*
 *	GALLERY
 *	Extension for Automad
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Extensions;


/**
 *	Create a simple gallery out of a set of images defined by a glob pattern.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Gallery {
	
	
	/**
	 *	Every extension has one main method which will be called when parsing a template file.
	 *	The name of that method is the same name as the name of the class and subnamespace (case insensitive).
	 *	To group all files for an extension (.css and .js) the .php file of the class gets simply the same name as the containing folder:
	 *	/extensions/gallery/gallery.php
	 *
	 *	All .css and .js files placed within /extensions/gallery get then automatically appended to the <head> section of the page when 
	 *	using the extension within a template file.
	 * 	In case there is also a minified version (for example gallery.css and gallery.min.css), only the minified version gets loaded.
	 *	
	 *	In this case the naming pattern looks like:
	 *	- namespace: 	Extensions
	 *	- directory:	/extensions/gallery
	 *	- class file:	/extensions/gallery/gallery.php
	 *	- class: 	Gallery
	 *	- method:	Gallery 
	 *	
	 *	This main method must always have two parameters, which will be passed automatically when calling the extension: $obj->Gallery($options, $Automad)
	 *	- $options:	An array with all the options
	 *	- $Automad:	The Automad object
	 *	
	 *	The example method below basically generates and returns the HTML for a simple image slideshow.
	 *	The main method of an extension must always return the output for the template.
	 *
	 *	Note: The Gallery method is not a kind of constructor (like it would be in PHP 4). Since this is a namespaced class,
	 *	a method with the same name as the last part of the namespace isn't called when creating an instance of the class (PHP 5.3+).
	 *
	 *	@param array $options
	 *	@param object $Automad
	 *	@return The generated HTML of the gallery. 
	 */

	public function Gallery($options, $Automad) {
			
		$defaults = 	array(
					'files' => '*.jpg',
					'width' => 200,
					'height' => 200,
					'crop' => true,
					'order' => false, 
					'class' => false,
					'firstWidth' => false,
					'firstHeight' => false,
					'firstClass' => false,
					'enlargedWidth' => false,
					'enlargedHeight' => false
				);
		
		// Merge defaults with options
		$options = array_merge($defaults, $options);
		
		// Get file list.
		$files = \Automad\Core\Parse::fileDeclaration($options['files'], $Automad->getCurrentPage());
		
		// Sort images.
		if ($options['order'] == 'asc') {
			sort($files, SORT_NATURAL);
		}
		
		if ($options['order'] == 'desc') {
			rsort($files, SORT_NATURAL);
		}
		
		// Generate HTML		
		$html = '<div class="gallery">';	
		$html .= 	\Automad\Core\Html::generateImageSet(
					$files, 
					$options['width'], 
					$options['height'], 
					$options['crop'], 
					$options['class'], 
					$options['firstWidth'], 
					$options['firstHeight'], 
					$options['firstClass'],
					$options['enlargedWidth'],
					$options['enlargedHeight']
				);
		$html .= '</div>';
				
		return $html;
		
	}


}


?>