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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 * 	The Html class holds all methods to generate html.
 */


class Html {
	
	
	/**
	 *	Add an image with an optional link.
	 *
	 *	A glob pattern can be passed as an image. The first match will then be used.
	 *	For example, using a glob pattern has the advantage of being able to display the first image of every page (as a logo or thumbnail),
	 *	by just passing '*.jpg' or '*.png', without naming all the images the same. 
	 *
	 *	The requested image can be optionally resized and cropped. 
	 *	If only a file is specified, the placed image keeps its original size and has no link.
	 *	If the image is a JPG and the description field in its EXIF data is defined, that description is used for the title attribute.
	 *
	 *	@param string $glob
	 *	@param string $w
	 *	@param string $h
	 *	@param boolean $crop
	 *	@param string $link
	 *	@param string $target
	 *	@return the HTML of an img tag (optionally wrapped by the given link)
	 */
	
	public static function addImage($glob, $w = false, $h = false, $crop = false, $link = '', $target = '') {
		
		if ($glob) {
			
			$files = glob($glob);	
			$file = reset($files);
			
			Debug::log('Html: First image matching ' . basename($glob) . ' is ' . $file);
			
		}
		
		if ($file) {
							
			$img = new Image($file, $w, $h, $crop);
			
			if ($img->file) {
			
				if ($target) {
					$target = ' target="' . $target . '"';
				}
			
				$html = '';
		
				if ($link) {
					$html .= '<a href="' . $link . '"' . $target . '>';
				}
			
				$html .= '<img src="' . $img->file . '" title="' . $img->description . '" width="' . $img->width . '" height="' . $img->height . '">';
			
				if ($link) {
					$html .= '</a>';
				}
			
				return $html;
				
			}	
		
		}
		
	}
	
	
	/**
	 *	Add link to $page and check, if $page is the current page or within the current path.
	 *
	 *	@param object $page
	 *	@param string $classes - additional classes to add to the link (separated by space as one string)
	 *	@param string $text - optional link text instead of page title
	 *	@return the HTML tag for the link to the given page
	 */

	public static function addLink($page, $classes = '', $text = '') {
	
		if ($page->isHome()) {	
			$classes .= ' ' . AM_HTML_CLASS_HOME;	
		} 
		
		if ($page->isCurrent()) {	
			$classes .= ' ' . AM_HTML_CLASS_CURRENT;
		} 
		
		if ($page->isInCurrentPath() && !$page->isHome()) {
			$classes .= ' ' . AM_HTML_CLASS_CURRENT_PATH;	
		} 
		
		$classes = trim($classes);
		
		if ($classes) {
			$classes = ' class="' . $classes . '"';
		} 
				
		if (!$text) {
			$text = strip_tags($page->data['title']);
			$title = '';
		} else {
			$title = ' title="' . strip_tags($page->data['title']) . '"';
		}
				
		return '<a' . $classes . $title . ' href="' . $page->url . '">' . $text . '</a>';
		
	}


	/**
	 *	Branch out recursively below a certain relative URL.
	 *
	 *	@param string $parentUrl
	 *	@param boolean $expandAll
	 *	@param array $collection (all pages)
	 *	@return the HTML for the branch/tree (recursive)
	 */

	private static function branch($parentUrl, $expandAll, $collection) {
		
		$selection = new Selection($collection);
		$selection->filterByParentUrl($parentUrl);
		$selection->sortPagesByBasename();
		
		$pages = $selection->getSelection();
		
		if ($pages) {
				
			// Use first element in $pages to determine the current level.
			$pagesKeys = array_keys($pages);
			$level = ' level-' . $pages[array_shift($pagesKeys)]->level;
		
			$html = '<ul class="' . AM_HTML_CLASS_TREE . $level . '">';	
		
			foreach ($pages as $page) {
			
				$html .= '<li>' . self::addLink($page) . '</li>';
			
				// There would be an infinite loop if the parentUrl equals the relUlr.
				// That is the case if the current page is the homepage and the homepage moved to the first level. 
				if ($page->parentUrl != $page->url) {			
					if ($expandAll || $page->isCurrent() || $page->isInCurrentPath()) {			
						$html .= self::branch($page->url, $expandAll, $collection);
					}
				}
			
			}

			$html .= '</ul>';
		
			return $html;
		
		}
		
	}
	
		
	/**
	 * 	Generate the HTML for a breadcrumb navigation out of a selection of pages.
	 *	
	 *	@param array $pages
	 *	@return the HTML of the breadcrumbs
	 */
	
	public static function generateBreadcrumbs($pages) {
		
		$i = 1;
		
		$html = '<div class="' . AM_HTML_CLASS_BREADCRUMBS . '">';
		
		foreach ($pages as $page) {
			
			$html .= '<a href="' . $page->url . '">' . strip_tags($page->data['title']) . '</a>';
			
			// Add separator for all but the last page.	
			if ($i++ < count($pages)) {
				$html .= AM_HTML_STR_BREADCRUMB_SEPARATOR;
			}
		
		}
		
		$html .= '</div>';
		
		return $html;
		
	}
	

	/**
	 *	Generate the HTML for filter menu out of $tags.
	 *
	 *	@param array $tags
	 *	@param string $targetPage (default is empty, stay on same page)
	 *	@return the HTML of the filter menu
	 */
		
	public static function generateFilterMenu($tags, $targetPage = '') {

		if ($tags) {

			$query = Parse::queryArray();
			$current = Parse::queryKey('filter');
		
			$html = '<ul class="' . AM_HTML_CLASS_FILTER . '">';			
		
			// If there is no $tagetPage in the options, the filters will be used to filter a page list 
			// on the current page without leaving the page after selecting a tag.
			// In that case, a visitor stays on the page while using the filters and therefore needs
			// the option to "reset" the filters again to an "unfiltered" mode.
			// The "All" button gets added for that purpose.
			if (!$targetPage) {
			
				// Check if current query is empty. 
				// No query means no filter - in that case the AM_HTML_CLASS_CURRENT gets applied to the "All" button.
				if (!$current) {
					$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
				} else {
					$class = ' ';
				}
			
				// Only change the ['filter'] key
				$query['filter'] = '';
				
				ksort($query);
					
				$html .= '<li><a' . $class . 'href="?' . http_build_query($query) . '">' . AM_HTML_TEXT_FILTER_ALL . '</a></li>';
			
			}
		
			foreach ($tags as $tag) {
			
				// Check if $tag equals current filter in query
				if ($current == $tag) {
					$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
				} else {
					$class = ' ';
				}
			
				// Only change the ['filter'] key
				$query['filter'] = $tag;
				
				ksort($query);
		
				$html .= '<li><a' . $class . 'href="' . $targetPage . '?' . http_build_query($query) . '">' . $tag . '</a></li>';
		
			}
		
			$html .= '</ul>';
		
			return $html;
			
		}
		
	}
	

	/**
	 *	Generate the HTML for a list of resized images linking to their bigger versions.
	 *
	 *	@param string $glob
	 *	@param integer $width
	 *	@param integer $height
	 *	@param integer $crop
	 *	@return The HTML of a list of images as links to their bigger versions.
	 */
	
	public static function generateImageSet($glob, $width = false, $height = false, $crop = false) {
			
		$files = glob($glob);
		
		if ($files) {
			
			$html = '<ul class="' . AM_HTML_CLASS_IMAGESET . '">';
			
			foreach($files as $file) {
				
				$bigImage = new Image($file);
				$html .= '<li>' . self::addImage($file, $width, $height, $crop, $bigImage->file) . '</li>';
				
			}
			
			$html .= '</ul>';
			
			return $html;
			
		}
	
	}
	
	
	/**
	 *	Generate the HTML for a page list out of a selection of pages, an array of variables and optional image settings.
	 *
	 *	@param array $pages (selected pages)
	 *	@param array $vars (variables to output in the list)
	 *	@param string $glob (glob pattern to find a corresponding image within each page directory)
	 *	@param integer $width
	 *	@param integer $height
	 *	@param integer $crop
	 *	@return the HTML of the list
	 */
	
	public static function generateList($pages, $vars, $glob, $width = false, $height = false, $crop = false) {
		
		if ($pages) {			
						
			$html = '<ul class="' . AM_HTML_CLASS_LIST . '">';
		
			foreach ($pages as $page) {
			
				$html .= '<li><a href="' . $page->url . '">';
				
				if ($glob) {
					
					// For each page, the glob pattern is matched against the page's direcory (if the glob is relative),
					// to find a corresponding image as thumbnail.
					// For example $glob = '*.jpg' will always use the first JPG in the page's directoy.
					// To re-use $glob for every page in the loop, $glob can't be modified and 
					// therefore $pageGlob will be used to build the full glob pattern.
					$pageGlob = Modulate::filePath($page->path, $glob);		
					$html .= Html::addImage($pageGlob, $width, $height, $crop);
					
				}
			
				foreach ($vars as $var) {
				
					if (isset($page->data[$var])) {
						
						$text = strip_tags($page->data[$var]);
						
						// Shorten $text to maximal characters (full words).
						if (strlen($text) > AM_HTML_LIST_MAX_CHARS) {
							// Cut $text to max chars
							$text = substr($text, 0, AM_HTML_LIST_MAX_CHARS);
							// Find last space and get position
							$pos = strrpos($text, ' ');
							// Cut $text again at last space's position (< AM_HTML_LIST_MAX_CHARS)
							$text = substr($text, 0, $pos) . ' ...';
						}
					
						// Variable key is used to define the html class.
						// That makes styling with CSS very customizable.
						$html .= '<div class="' . $var . '">' . $text . '</div>';
						
					}
				
				}
			
				$html .= '</a></li>';
			
			}
		
			$html .= '</ul>';
		
			return $html;
			
		}
		
	}


	/**
	 * 	Generate the HTML of a navigation list for the passed pages.
	 *
	 *	Each page gets checked against the current URL. 
	 *	If the page is the current page or the page is a parent of the current page, 
	 *	additional classe will be added to the representing element.
	 *	
	 *	@param array $pages
	 *	@return the HTML of the navigation
	 */
	
	public static function generateNav($pages) {
		
		if ($pages) {
		
			$html = '<ul class="' . AM_HTML_CLASS_NAV . '">';
		
			foreach($pages as $page) {
			
				$html .= '<li>' . self::addLink($page) . '</li>'; 
			
			}
		
			$html .= '</ul>';
		
			return $html;
			
		}
		
	}
	
	
	/**
	 * 	Generate search field.
	 *	
	 *	@param string $url (URL of the results page)
	 *	@param string $placeholderText (placeholder text)
	 *	@return the HTML for the search field
	 */
	
	public static function generateSearchField($url, $placeholderText) {
		
		$html =  '<form class="' . AM_HTML_CLASS_SEARCH . '" method="get" action="' . $url . '">';
		$html .= '<input type="text" name="search" value="' . $placeholderText . '" onfocus="if (this.value==\'' . $placeholderText . '\') { this.value=\'\'; }" onblur="if (this.value==\'\') { this.value=\'' . $placeholderText . '\'; }" />';
		$html .= '</form>';
		
		return $html;
			
	}

	
	/**
	 *	Generate ascending/descending buttons for sorting.
	 *
	 *	@param array $options - An array with the text for each direction: array('SORT_ASC' => 'asc', 'SORT_DESC' => 'desc')
	 *	@return the HTML for the buttons
	 */
	
	public static function generateSortDirectionMenu($options) {
		
		$query = Parse::queryArray();
		$current = Parse::queryKey('sort_dir');
				
		if (!$current) {
			$current = AM_LIST_DEFAULT_SORT_DIR;
		}
		
		$html = '<ul class="' . AM_HTML_CLASS_SORT . '">';
		
		
		// Ascending buttom		
		if ($current == "sort_asc") {
			$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
		} else {
			$class = ' ';
		}
		
		$query['sort_dir'] = "sort_asc";
		ksort($query);
		$html .= '<li class="sort_asc"><a' . $class . 'href="?' . http_build_query($query) . '">' . $options["SORT_ASC"] . '</a></li>';
		
		
		// Descending button
		if ($current == "sort_desc") {
			$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
		} else {
			$class = ' ';
		}
		
		$query['sort_dir'] = "sort_desc";
		$html .= '<li class="sort_desc"><a' . $class . 'href="?' . http_build_query($query) . '">' . $options["SORT_DESC"] . '</a></li>';
		
		$html .= '</ul>';
	
		return $html;
		
	}

	
	/**
	 *	Generate the menu to select the sort type from the given types ($options).
	 *
	 *	@param array $options -	An array with the variables to "sort by", where the key is the variable and the value its description. 
	 *				An array item with a numeric key will be taken for the original order: array('Original', 'title' => 'By Title', 'tags' => 'By Tags').
	 *	@return the HTML of the menu
	 */
	
	public static function generateSortTypeMenu($options) {

		$query = Parse::queryArray();
		$current = Parse::queryKey('sort_type');
		
		// All option array items with numeric keys get merged into one item (last one kept).
		// That way the text for the 'Original Order' button can be defined with just adding a "keyless" value to the array. 
		for($i=0; isset($options[$i]); $i++){
			$options[''] = $options[$i];
			unset($options[$i]);
		}
				
		ksort($options);
		
		$html = '<ul class="' . AM_HTML_CLASS_SORT . '">';
		
		foreach ($options as $key => $value) {
							
			// Check if $value equals current filter in query
			if ($current == $key) {
				$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
			} else {
				$class = ' ';
			}
		
			// Only change the ['sort_type'] key
			$query['sort_type'] = $key;
			
			ksort($query);
	
			$html .= '<li><a' . $class . 'href="?' . http_build_query($query) . '">' . $value . '</a></li>';
			
		}
	
		$html .= '</ul>';
	
		return $html;
	
	}
	
	
	/**
	 * 	Generate the HTML for a full site tree.
	 *
	 *	@param array $collection (all pages)
	 *	@param boolean $expandAll
	 *	@return the HTML of the tree
	 */
	
	public static function generateTree($collection, $expandAll = true) {
		
		// The tree starts on level 1. By default the homepage will not be included.
		// To include the homepage, it has to be moved to the first level by using Selection::makeHomePageFirstLevel()
		// or $[includeHome] from the templates.
		return self::branch('/', $expandAll, $collection);
		
	}
	
	
}


?>
