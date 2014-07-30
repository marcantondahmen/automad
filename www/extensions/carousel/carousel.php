<?php
/*
 *	CAROUSEL
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
 *	The Carousel extension creates the markup for a Twitter Bootstrap carousel and automatically generates page variables for each matched image.
 *	To be used, Twitter's Bootstrap CSS and JS files are required.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Carousel {
	
	
	/**
	 *	Every extension has one main method which will be called when parsing a template file.
	 *	The name of that method is the same name as the name of the class and subnamespace (case insensitive).
	 *	The .php file of the class gets simply the same name as the containing folder:
	 *	/extensions/carousel/carousel.php
	 *	
	 *	In this case the naming pattern looks like:
	 *	- namespace: 	Extensions
	 *	- directory:	/extensions/carousel
	 *	- class file:	/extensions/carousel/carousel.php
	 *	- class: 	Carousel
	 *	- method:	Carousel 
	 *	
	 *	This main method must always have two parameters, which will be passed automatically when calling the extension: $obj->Carousel($options, $Automad)
	 *	- $options:	An array with all the options
	 *	- $Automad:	The Automad object.
	 *	
	 *	Note: The Carousel method is not a kind of constructor (like it would be in PHP 4). Since this is a namespaced class,
	 *	a method with the same name as the last part of the namespace isn't called when creating an instance of the class (PHP 5.3+).
	 *
	 *	@param array $options
	 *	@param object $Automad
	 *	@return The generated HTML of the Carousel. 
	 */
	
	public function Carousel($options, $Automad) {
			
		$defaults = 	array(
					'files' => '*.jpg',
					'width' => 400,
					'height' => 300,
					'fullscreen' => false,
					'order' => false,
					'duration' => 3000,
					'controls' => true
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

		// HTML
		if ($files) {
			
			// Generate unique ID, in case more than one carousel get used on one page.
			$id = 'carousel-' . crc32(uniqid('', true));
			
			// Add "fullscreen" class, if $options['fullscreen'] is true.
			if ($options['fullscreen']) {
				$classAttribute = ' class="carousel fullscreen slide"';
			} else {
				$classAttribute = ' class="carousel slide"';
			}
		
			// The duration option gets passed as a data attribute.
			$html = '<div id="' . $id . '"' . $classAttribute . ' data-ride="carousel" data-interval="' . $options['duration'] . '">';
		
			// Indicators
			if (count($files) > 1 && $options['controls']) {
			
				$html .= '<ol class="carousel-indicators">';
			
				foreach ($files as $i => $file) {
		
					$html .= '<li data-target="#' . $id . '" data-slide-to="' . $i . '"';
			
					if ($i == 0) {
						$html .= ' class="active"';
					}
		
					$html .= '></li>';
			
				}
			
				$html .= '</ol>';
			
			}
					
			// Slides	
			$html .= '<div class="carousel-inner">';		
		
			foreach ($files as $i => $file) {
		
				$html .= '<div class="item';
		
				if ($i == 0) {
					$html .= ' active';
				}
		
				$html .= '">';
				
				if ($options['fullscreen']) {
					$image = new \Automad\Core\Image($file, $options['width'], $options['height'], false);
					$html .= '<div class="image" style="background-image: url(\'' . $image->file . '\');"></div>';
				} else {
					$html .= \Automad\Core\Html::addImage($file, $options['width'], $options['height'], true);
				}
				
				$html .= '<div class="carousel-caption">' . \Automad\Core\Html::addVariable('carousel_caption_' . \Automad\Core\Parse::sanitize(basename($file))) . '</div>' .
					 '</div>';			
		
			}
			
			$html .= '</div>';
		
			// Controls
			if (count($files) > 1 && $options['controls']) {
				$html .= '<a class="left carousel-control" href="#' . $id . '" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>' . 
					 '<a class="right carousel-control" href="#' . $id . '" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>';
			}
		
			$html .= '</div>';
				
			return $html;
		
		}
		
	}
	
	
}


?>