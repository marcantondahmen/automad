<?php
/**
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
	

	public function Gallery($options, $site) {
		
		$defaults = 	array(
					'glob' => '*.jpg',
					'width' => 200,
					'height' => 200,
					'class' => ''
				);
		
		// Merge defaults with options
		$options = array_merge($defaults, $options);
		
		// Build full glob pattern
		$page = $site->getCurrentPage();
		$glob = \Core\Modulate::filePath($page->path, $options['glob']);
		
		// Generate HTML		
		$html = '<div class="gallery">';	
		$html .= \Core\Html::generateImageSet($glob, $options['width'], $options['height'], true, $options['class']);
		$html .= '</div>';
				
		return $html;
		
	}


}


?>