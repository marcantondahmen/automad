<?php
/*
 *	GALLERY
 *	Extension for the Automad CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Extensions;


/**
 *	Create a simple gallery out of a set of images defined by a glob pattern.
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
	 *	This main method must always have two parameters, which will be passed automatically when calling the extension: $obj->Gallery($options, $Site)
	 *	- $options:	An array with all the options
	 *	- $Site:	The Site object, to make all Site methods and variables available for the extension
	 *	
	 *	The example method below basically generates and returns the HTML for a simple image slideshow.
	 *	The main method of an extension must always return the output for the template.
	 *
	 *	Note: The Gallery method is not a kind of constructor (like it would be in PHP 4). Since this is a namespaced class,
	 *	a method with the same name as the last part of the namespace isn't called when creating an instance of the class (PHP 5.3+).
	 *
	 *	@param array $options
	 *	@param object $Site
	 *	@return The generated HTML of the gallery. 
	 */

	public function Gallery($options, $Site) {
			
		$defaults = 	array(
					'glob' => '*.jpg',
					'width' => 200,
					'height' => 200,
					'class' => ''
				);
		
		// Merge defaults with options
		$options = array_merge($defaults, $options);
		
		// Build full glob pattern
		$Page = $Site->getCurrentPage();
		$glob = \Automad\Core\Modulate::filePath($Page->path, $options['glob']);
		
		// Generate HTML		
		$html = '<div class="gallery">';	
		$html .= \Automad\Core\Html::generateImageSet($glob, $options['width'], $options['height'], true, $options['class']);
		$html .= '</div>';
				
		return $html;
		
	}


}


?>