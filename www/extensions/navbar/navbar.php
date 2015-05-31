<?php
/*
 *	NAVBAR
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
 *	The Navbar extension creates the markup of a Twitter Bootstrap Navbar for multiple levels.
 *	To be used, Twitter's Bootstrap CSS and JS files are required.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
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
	 *	This main method must always have two parameters, which will be passed automatically when calling the extension: $obj->Navbar($options, $Automad)
	 *	- $options:	An array with all the options
	 *	- $Automad:	The Automad object, to make all the Site's data available for the extension
	 *	
	 *	Note: The Navbar method is not a kind of constructor (like it would be in PHP 4). Since this is a namespaced class,
	 *	a method with the same name as the last part of the namespace isn't called when creating an instance of the class (PHP 5.3+).
	 *
	 *	@param array $options
	 *	@param object $Automad
	 *	@return The generated HTML. 
	 */
	
	public function Navbar($options, $Automad) {
		
		$defaults = 	array(
					'fluid' => true,
					'fixedToTop' => false,
					'brand' => $Automad->getSiteName(),
					'logo' => false,
					'logoWidth' => 100,
					'logoHeight' => 100,
					'search' => 'Search',
					'searchPosition' => 'left',
					'levels' => 2
				);
		
		// Merge defaults with options
		$options = array_merge($defaults, $options);
			
		if ($options['fixedToTop']) {
			$fixed = ' navbar-fixed-top';
		} else {
			$fixed = '';	
		}
		
		if ($options['fluid']) {
			$container = 'container-fluid';
		} else {
			$container = 'container';
		}
		
		if ($options['logo']) {
			$brand = \Automad\Core\Html::addImage(AM_BASE_DIR . $options['logo'], $options['logoWidth'], $options['logoHeight']);
		} else {
			$brand = $options['brand'];
		}
		
		// Main nav wrapper
		$html = '<nav class="navbar navbar-default' . $fixed . '">';
			
		// To determine all pages for each row, first the "breadcrumbs" get filtered.	
		$Page = $Automad->getCurrentPage();	 
		$Selection = new \Automad\Core\Selection($Automad->getCollection());
		$Selection->filterBreadcrumbs($Page->url);
		$breadcrumbs = $Selection->getSelection();
		
		// Generate rows.		
		foreach ($breadcrumbs as $breadcrumb) {
		
			// Limit number of levels to be < $options['levels'].
			// $options['levels'] == 2 > 2 rows (levels 0 & 1).
			if ($breadcrumb->level < $options['levels']) {
				
				$Selection = new \Automad\Core\Selection($Automad->getCollection());
				$Selection->filterByParentUrl($breadcrumb->url);
				$Selection->sortPagesByBasename();
				$pages = $Selection->getSelection();
			
				if ($pages) {
				
					if ($breadcrumb->level === 0) {
							
						// First level navigation
						$html .= '<div class="level-' . ($breadcrumb->level + 1) . '">';
						
						// Wrapping container
						$html .= '<div class="' . $container . '">';
						
						// Header (brand and collapse button)
						$html .= '<div class="navbar-header">' .
							 '<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>' .
							 '<a class="navbar-brand" href="/">' . $brand . '</a>' .
							 '</div>';
						
						// Collapsable
						$html .= '<div class="collapse navbar-collapse">';
						
						// Page's List
						$html .= '<ul class="nav navbar-nav">';
						
						foreach ($pages as $page) {
							
							$html .= '<li';
					
							if ($page->isCurrent()) {
								$html .= ' class="active"';
							}
							
							$html .= '>' . \Automad\Core\Html::addLink($page) . '</li>';
							
						}
						
						$html .= '</ul>';
						
						// Search box
						if ($options['search']) {
							
							$html .= \Automad\Core\Html::generateSearchField(
									AM_PAGE_RESULTS_URL, 
									$options['search'], 
									'navbar-form navbar-' . $options['searchPosition'], 
									'form-control'
								);
								
						} 
						
						// Close collapse
						$html .= '</div>';
						
						// Close container
						$html .= '</div>';
						
						// Close level
						$html .= '</div>';
						
					} else {
						
						// All other levels (>1)
						$html .= '<div class="level-' . ($breadcrumb->level + 1) . '">'. 
							 '<div class="' . $container . '">' .
							 '<div class="collapse navbar-collapse">' .
							 '<ul class="nav navbar-nav">';
						
						foreach ($pages as $page) {
							
							$html .= '<li';
					
							if ($page->isCurrent()) {
								$html .= ' class="active"';
							}
							
							$html .= '>' . \Automad\Core\Html::addLink($page) . '</li>';
							
						}
						
						$html .= '</ul>' .
							 '</div>' .
							 '</div>' .
							 '</div>';
						
					}
					
				}
					
			}
		
		}
		
		// Close nav wrapper
		$html .= '</nav>';
		
		return $html;
		
	}
	
	
}