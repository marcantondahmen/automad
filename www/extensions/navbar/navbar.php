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
 *	The Navbar extension creates the markup of a Twitter Bootstrap Navbar for multiple levels.
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
					'search' => 'Search',
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
			$brand = \Core\Html::addImage(AM_BASE_DIR . $options['logo'], $options['logoWidth'], $options['logoHeight']);
		} else {
			$brand = $options['brand'];
		}
		
		// Main nav wrapper
		$html = '<nav class="navbar navbar-default' . $fixed . '" role="navigation">';
			
		// To determine all pages for each row, first the "breadcrumbs" get filtered.		 
		$P = $site->getCurrentPage();
		$selection = new \Core\Selection($site->getCollection());
		$selection->filterBreadcrumbs($P->url);
		$breadcrumbs = $selection->getSelection();
		
		// Generate rows.		
		foreach ($breadcrumbs as $breadcrumb) {
		
			// Limit number of levels to be < $options['levels'].
			// $options['levels'] == 2 > 2 rows (levels 0 & 1).
			if ($breadcrumb->level < $options['levels']) {
				
				$selection = new \Core\Selection($site->getCollection());
				$selection->filterByParentUrl($breadcrumb->url);
				$selection->sortPagesByBasename();
				$pages = $selection->getSelection();
			
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
							
							$html .= '>' . \Core\Html::addLink($page) . '</li>';
							
						}
						
						$html .= '</ul>';
						
						// Search box
						if ($options['search']) {
						
							$html .= '<form class="navbar-form navbar-left" role="search" method="get" action="' . AM_PAGE_RESULTS_URL . '">' . 
								 '<input class="form-control" type="text" name="search" value="' . $options['search'] . '" ' .
								 'onfocus="if (this.value==\'' . $options['search'] . '\') { this.value=\'\'; }" onblur="if (this.value==\'\') { this.value=\'' . $options['search'] . '\'; }" />' .
								 '</form>';
							
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
							
							$html .= '>' . \Core\Html::addLink($page) . '</li>';
							
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