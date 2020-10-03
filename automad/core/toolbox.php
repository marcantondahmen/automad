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
 *	AUTOMAD
 *
 *	Copyright (c) 2013-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 * 	The Toolbox class holds all methods to be used within the template files.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2013-2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Toolbox {
	

	/**
	 * 	Automad object.
	 */
	
	private $Automad;
	
	
	/**
	 *	The full collection of pages.
	 */
	
	private $collection;
	
	
	/**
	 * 	The Automad object is passed as an argument. It shouldn't be created again (performance).
	 */
		
	public function __construct($Automad) {
				
		$this->Automad = $Automad;
		$this->collection = $this->Automad->getCollection();
				
	}
	
	
	/**
	 * 	Generate breadcrumbs to the current page, if the page's level is > 0 (not homepage / search results / page not found).
	 *
	 * 	Options:
	 * 	- class: false (class of <ul> element)
	 * 	- excludeHidden: false (exclude hidden pages)
	 *      
	 *	@param array $options
	 *	@return string The HTML of a breadcrumb navigation
	 */
	
	public function breadcrumbs($options = array()) {
		
		if ($this->Automad->Context->get()->level > 0) {	
				
			$options = 	array_merge(
							array(
								'class' => false, 
								'excludeHidden' => false
							), 
							$options
						);
				
			$Selection = new Selection($this->collection);
			$Selection->filterBreadcrumbs($this->Automad->Context->get()->url);
			
			if ($options['class']) {
				$class = ' class="' . $options['class'] . '"';
			} else {
				$class= '';
			}
			
			$html = '<ul' . $class . '>';
			
			foreach ($Selection->getSelection($options['excludeHidden']) as $Page) {
				$html .= '<li><a href="' . $Page->url . '">' . Str::stripTags($Page->get(AM_KEY_TITLE)) . '</a></li> ';
			}
			
			$html .= '</ul>';
			
			return $html;
			
		}
		
	}
	
	
	/**
	 * 	Configure the filelist to be used in foreach loops.
	 *	
	 *	@param array $options
	 */
	
	public function filelist($options = array()) {
		
		$this->Automad->getFilelist()->config($options);
		
	}
	

	/**
	 *	Place an image.
	 *
	 * 	Options:
	 *  - file: false (filepath or glob pattern - when using a glob pattern, the first match is used)
	 *  - width: false (width in pixels)
	 *  - height: false (height in pixels)
	 *  - crop: false (crop image)
	 *  - class: false (class for the <img /> element)
	 *
	 *	@param array $options - (file: path/to/file (or glob pattern), width: px, height: px, crop: 1)
	 *	@return string The HTML for the image output
	 */
	
	public function img($options = array()) {
		
		// Default options
		$defaults = 	array(
							'file' => false,
							'width' => false,
							'height' => false,
							'crop' => false,
							'class' => false
						);
		
		// Merge options with defaults				
		$options = array_merge($defaults, $options);
			
		if ($options['file']) {
			
			$glob = Resolve::filePath($this->Automad->Context->get()->path, $options['file']);
			$files = FileSystem::glob($glob);
			$file = reset($files);
			$img = new Image($file, $options['width'], $options['height'], $options['crop']);
			
			if ($img->file) {
				
				if ($options['class']) {
					$class = ' class="' . $options['class'] . '"';
				} else {
					$class = '';
				}	
						
				return '<img' . $class . ' src="' . $img->file . '" width="' . $img->width . '" height="' . $img->height . '" />';
				
			}
			
		}

	}
	
		
	/**
	 *	Generate a list for the navigation below a given URL.
	 *
	 * 	Options:
	 * 	- context: '/' (The parent URL)
	 *  - hompage: false (include homepage to first-level nav)
	 *  - excludeHidden: true (exclude hidden pages)
	 *  - class: false (class of the <ul> element)
	 *  - active: false (class of the active <li> element)
	 *
	 *	@param array $options
	 *	@return string The HTML of a navigation list	
	 */
	
	public function nav($options = array()) {
		
		$defaults = 	array(
							'context' => '/', 
							'homepage' => false,
							'excludeHidden' => true,
							'class' => false,
							'active' => false
						);
		
		$options = array_merge($defaults, $options);
		
		// Prepare class attributes.
		if ($options['class']) {
			$class = ' class="' . $options['class'] . '"';
		} else {
			$class = '';
		}
		
		if ($options['active']) {
			$active = ' class="' . $options['active'] . '"';
		} else {
			$active = '';
		}
		
		// Get pages.		
		$Selection = new Selection($this->collection);
		$Selection->filterByParentUrl($options['context']);
		$Selection->sortPages();
		$pages = $Selection->getSelection($options['excludeHidden']);
		
		// Add Homepage to first-level navigation if context is the homepage and the option 'homepage' is true.
		if ($options['context'] == '/' && $options['homepage']) {
			$pages = array('/' => $this->collection['/']) + $pages;
		}
		
		// Create list.
		$html = '<ul' . $class . '>';
		
		foreach ($pages as $Page) {
			
			$html .= '<li';
			
			if (($Page->isCurrent() || $Page->isInCurrentPath()) && $Page->url != '/') {
				$html .= $active;
			}
			
			$html .= '><a href="' . $Page->url . '">' . Str::stripTags($Page->get(AM_KEY_TITLE)) . '</a></li>'; 
			
		}
		
		$html .= '</ul>';
		
		return $html;
		
	}
	
	
	/**
	 *	Generate a list for the navigation below the current page.   
	 *  Options are the same like those for nav() except the 'context' option.
	 *
	 *	@param array $options
	 *	@return string The HTML of the navigation list	
	 */
	
	public function navChildren($options = array()) {
	
		// Always set 'context' to the current page's URL by merging that parameter with the other specified options.
		return $this->nav(array_merge($options, array('context' => $this->Automad->Context->get()->url)));
		
	}
	
	
	/**
	 *	Generate a list for the navigation below the current page's parent.   
	 *  Options are the same like those for nav() except the 'context' option.
	 *
	 *	@param array $options
	 *	@return string The HTML of the navigation list	
	 */
	
	public function navSiblings($options = array()) {
		
		// Set context to current parentUrl and overwrite passed options
		return $this->nav(array_merge($options, array('context' => $this->Automad->Context->get()->parentUrl)));
		
	}
	
	
	/**
	 *	Generate a list for the navigation at the top level including the home page (level 0 & 1).   
	 *	Options are the same like those for nav() except the 'context' option.
	 *
	 *	@param array $options
	 *	@return string The HTML of the navigation list	
	 */
	
	public function navTop($options = array()) {
		
		// Set context to '/' and overwrite passed options.
		return $this->nav(array_merge($options, array('context' => '/')));
		
	}
	
	
	/**
	 * 	Generate full navigation tree.
	 *
	 *  Options:
	 *  - all: true (expand all pages or only in current path)
	 *  - context: '' (parent URL)
	 *  - rootLevel: false (a kind of flexible context page at a given level)
	 *  - excludeHidden: true (exclude hidden pages)
	 *  - class: false (class of the <ul> element)
	 *  - active: false (class of the active <li> element)
	 *
	 *	@param array $options - (all: expand all pages (boolean), context: "/parenturl", rootLevel: integer)
	 *	@return string The HTML of the tree
	 */
	
	public function navTree($options = array()) {
				
		$defaults = 	array( 
							'all' => true,
							'context' => '',
							'rootLevel' => false,
							'excludeHidden' => true,
							'class' => false,
							'active' => false
						);
				
		$options = array_merge($defaults, $options);
		
		// If 'rootLevel' is not false (!==, can be 0), 
		// the tree always starts below the given level within the breadcrumb trail to the current page.
		// So, $context gets dynamically determined in contrast to defining 'context' within the options.
		// When 'rootLevel' is defined, the 'context' option will be ignored.
		if ($options['rootLevel'] !== false) {
			
			$Selection = new Selection($this->collection);
			$Selection->filterBreadcrumbs($this->Automad->Context->get()->url);
			
			foreach ($Selection->getSelection($options['excludeHidden']) as $breadcrumb) {
				if ($breadcrumb->level == $options['rootLevel']) {
					$context = $breadcrumb->url;
				}
			}
				
		} else {
			// If the 'rootLevel' option is set to false, the 'context' option will be used.
			$context = $options['context'];
		}
		
		// The tree only gets generated, if $context is defined, because in case the 'rootLevel' option is 
		// defined and greater than the actual level of the current page, $context won't be defined.
		if (isset($context)) {	

			$Selection = new Selection($this->collection);
			$Selection->filterByParentUrl($context);
			$Selection->sortPages();
			
			if ($pages = $Selection->getSelection($options['excludeHidden'])) {
					
				if ($options['class']) {
					$class = ' class="' . $options['class'] . '"';
				} else {
					$class = '';
				}
			
				$html = '<ul' . $class . '>';	
			
				foreach ($pages as $Page) {
				
					$html .= '<li';
					
					if ($Page->isCurrent() && $options['active']) {
						$html .= ' class="' . $options['active'] . '"';
					}
					
					$html .= '><a href="' . $Page->url . '">' . Str::stripTags($Page->get(AM_KEY_TITLE)) . '</a>';
									
					if ($options['all'] || $Page->isCurrent() || $Page->isInCurrentPath()) {
						
						$html .= 	$this->navTree(array_merge(
										$options,
										array(
											'context' => $Page->url, 
											'rootLevel' => false
										)
									));
							
					}
				
					$html .= '</li>';
				
				}

				$html .= '</ul>';
			
				return $html;
			
			}
			
		}
	
	}
	
	
	/**
	 *	Alias for calling the pagelist() method with the default options merged with the specified ones
	 *	to override all previous configurations.
	 *	
	 *	@param array $options 
	 */

	public function newPagelist($options = array()) {
		
		$this->pagelist(array_merge($this->Automad->getPagelist()->getDefaults(), $options));
	
	}
	
	
	/**
	 *	Change of configuration for Automad's Pagelist object.
	 *
	 *	Options:   
	 *
	 *	- context: an optionally fixed URL for the context of a pagelist of type breadcrumbs or children. In case this parameter is false, within a loop the context always changes dynamically to the current page.
	 *	- excludeCurrent: default false
	 *	- excludeHidden: default true
	 *	- filter: filter pages by tags
	 *	- limit: limit the object's array of relevant pages
	 *	- offset: offset the within the array of all relevant pages
	 *	- page: false (the current page in the pagination - to be used with the limit parameter)
	 *	- search: filter pages by search string
	 *	- sort: sorting options string, like "date desc, title asc"
	 *	- template: include only pages matching that template	
	 *	- type: sets the type of pagelist (default is false) - valid types are false (all), "children", "related", "siblings" and "breadcrumbs"
	 *  	
	 *	@param array $options 
	 */

	public function pagelist($options = array()) {
			
		$this->Automad->getPagelist()->config($options);
	
	}
	
	
	/**
	 *	Merge passed key-value pairs ($options) with current query string.
	 *
	 *	@param array $options
	 *	@return string The merged query string
	 */
	
	public function queryStringMerge($options) {
		
		return http_build_query(array_merge($_GET, $options));
		
	}
	
	
	/**
	 *	Redirect page.
	 *      
	 *	@param array $options
	 */
	
	public function redirect($options) {
		
		$options = 	array_merge(array(
						'url' => false,
						'code' => 302
					), $options);
		
		$url = Resolve::absoluteUrlToRoot(Resolve::relativeUrlToBase($options['url'], $this->Automad->Context->get()));
		
		header('Location: ' . $url, true, $options['code']);
		exit();
		
	}
	
	
	/**
	 *	Set shared or session data variables by passing an array of key/value pairs.
	 *	
	 *	@param array $options
	 */
	
	public function set($options) {
		
		foreach ($options as $key => $value) {
					
			if (preg_match('/' . Regex::$charClassAllVariables . '/', $key)) {
				
				if (strpos($key, '%') === 0) {
					SessionData::set($key, $value); 
				} else {
					$this->Automad->Shared->set($key, $value);
				}
				
			} 
			
		}
		
	}
	
		
}
