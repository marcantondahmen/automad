<?php
/**
 *	NAVBAR
 *	Extension for the Automad CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Extensions;


/**
 *	The Navbar extension creates the markup of a Twitter Bootstrap Navbar as a menu for all first level pages.
 *	To be used, Twitter's Bootstrap CSS and JS files are required.
 */


class Navbar {
	
	
	/**
	 *	Every extension has one main method which will be called when parsing a template file.
	 *	The name of that method is the same name as the name of the class and subnamespace (case insensitive).
	 *	The .php file of the class gets simply the same name as the containing folder:
	 *	/extensions/navbar/navbar.php
	 *	
	 *	In this case the naming pattern looks like:
	 *	- namespace: 	Extensions
	 *	- directory:	/extensions/navbar
	 *	- class file:	/extensions/navbar/navbar.php
	 *	- class: 	Navbar
	 *	- method:	Navbar 
	 *	
	 *	This main method must always have two parameters, which will be passed automatically when calling the extension: $obj->Navbar($options, $site)
	 *	- $options:	An array with all the options
	 *	- $site:	The Site object, to make all data available for the extension
	 *	
	 *	Note: The Navbar method is not a kind of constructor (like it would be in PHP 4). Since this is a namespaced class,
	 *	a method with the same name as the last part of the namespace isn't called when creating an instance of the class (PHP 5.3+).
	 *
	 *	@param array $options
	 *	@param object $site
	 *	@return The generated HTML. 
	 */
	
	public function Navbar($options, $site) {
		
		$defaults = 	array(
					'fluid' => true,
					'fixedToTop' => false,
					'brand' => $site->getSiteName(),
					'logo' => false,
					'logoWidth' => 100,
					'logoHeight' => 100,
					'search' => 'Search'
				);
		
		// Merge defaults with options
		$options = array_merge($defaults, $options);
		
		// Get first level pages
		$selection = new \Core\Selection($site->getCollection());
		$selection->filterByParentUrl('/');
		$selection->sortPagesByBasename();
		$pages = $selection->getSelection();
		
		// Generate HTML
		$html = '<nav class="navbar navbar-default';
		
		if ($options['fixedToTop']) {
			$html .= ' navbar-fixed-top';
		}
		
		$html .= '" role="navigation"><div class="container';
			
		if ($options['fluid']) {
			$html .= '-fluid';
		}
			
		$html .= '">' .
			 '<div class="navbar-header">' .
			 '<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>' .
			 '<a class="navbar-brand" href="/">';
			
		if ($options['logo']) {
			$html .= \Core\Html::addImage(AM_BASE_DIR . $options['logo'], $options['logoWidth'], $options['logoHeight']);
		} else {
			$html .= $options['brand'];
		}
	  
		$html .= '</a>' .
			 '</div>' .
			 '<div class="collapse navbar-collapse" id="navbar">' . 
			 '<ul class="nav navbar-nav">';
			
		foreach ($pages as $P) {
		
			$html .= '<li';
			
			if ($P->isCurrent()) {
				$html .= ' class="active"';
			}
			
			$html .= '>' . \Core\Html::addLink($P) . '</li>';
			
		}	
			
		$html .= '</ul>';
		
		if ($options['search']) {
			$html .= '<form class="navbar-form navbar-right" role="search" method="get" action="' . AM_PAGE_RESULTS_URL . '">' . 
				 '<input class="form-control" type="text" name="search" value="' . $options['search'] . '" ' .
				 'onfocus="if (this.value==\'' . $options['search'] . '\') { this.value=\'\'; }" onblur="if (this.value==\'\') { this.value=\'' . $options['search'] . '\'; }" />' .
				 '</form>';
		}
		
		$html .= '</div>' .
			 '</div>' .
			 '</nav>';
		
		return $html;
		
	}
	
	
}