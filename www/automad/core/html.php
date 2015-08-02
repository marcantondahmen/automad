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
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 * 	The Html class holds all methods to generate html.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2014 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
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
	 * 	@param string $class
	 *	@param boolean $addCaption
	 *	@return the HTML of an img tag (optionally wrapped by the given link)
	 */
	
	public static function addImage($glob, $w = false, $h = false, $crop = false, $link = false, $target = false, $class = false, $addCaption = false) {
		
		if ($files = glob($glob)) {
			
			$file = reset($files);
			
			Debug::log('Html: First image matching ' . basename($glob) . ' is ' . $file);
							
			$img = new Image($file, $w, $h, $crop);
			
			if ($img->file) {
			
				if ($target) {
					$target = ' target="' . $target . '"';
				}
			
				$html = '';
		
				if ($link) {
					$html .= '<a href="' . $link . '"' . $target . '>';
				}
				
				if ($class) {
					$class = ' class="' . $class . '"';
				}
				
				if ($addCaption) {
					$caption = ' data-caption="' . self::addVariable('image_caption_' . Parse::sanitize(basename($file))) . '"';
				} else {
					$caption = '';
				}
			
				$html .= '<div class="' . AM_HTML_CLASS_IMAGE_WRAPPER . '"><img' . $class . ' src="' . $img->file . '" alt="' . $img->description . '" title="' . $img->description . '" width="' . $img->width . '" height="' . $img->height . '"' . $caption . ' /></div>';
			
				if ($link) {
					$html .= '</a>';
				}
			
				return $html;
				
			}	
		
		}
		
	}
	
	
	/**
	 *	Add link to $Page and check, if $Page is the current page or within the current path.
	 *
	 *	@param object $Page
	 *	@param string $classes - additional classes to add to the link (separated by space as one string)
	 *	@param string $text - optional link text instead of page title
	 *	@return the HTML tag for the link to the given page
	 */

	public static function addLink($Page, $classes = '', $text = '') {
	
		if ($Page->isHome()) {	
			$classes .= ' ' . AM_HTML_CLASS_HOME;	
		} 
		
		if ($Page->isCurrent()) {	
			$classes .= ' ' . AM_HTML_CLASS_CURRENT;
		} 
		
		if ($Page->isInCurrentPath() && !$Page->isHome()) {
			$classes .= ' ' . AM_HTML_CLASS_CURRENT_PATH;	
		} 
		
		$classes = trim($classes);
		
		if ($classes) {
			$classes = ' class="' . $classes . '"';
		} 
				
		if (!$text) {
			$text = strip_tags($Page->data[AM_KEY_TITLE]);
			$title = '';
		} else {
			$title = ' title="' . strip_tags($Page->data[AM_KEY_TITLE]) . '"';
		}
				
		return '<a' . $classes . $title . ' href="' . $Page->url . '">' . $text . '</a>';
		
	}


	/**
	 *	Add a page variable to the HTML of a template. 
	 * 	In case an extension needs to generate a variable by itself automatically, this method can be used to generate the correct syntax for the variable markup,
	 *	since all extensions will be parsed before the variables.
	 * 
	 *	@param string $name
	 *	@return The markup for the variable
	 */

	public static function addVariable($name) {
		
		return AM_PLACEHOLDER_PREFIX . AM_PLACEHOLDER_PAGE_VAR . '(' . $name . ')';
	
	}

	
	/**
	 * 	Generate the HTML for a breadcrumb navigation out of a selection of pages.
	 *	
	 *	@param array $pages
	 * 	@param string $separator
	 *	@return the HTML of the breadcrumbs
	 */
	
	public static function generateBreadcrumbs($pages, $separator = '') {
		
		$i = 1;
		
		$html = '<div class="' . AM_HTML_CLASS_BREADCRUMBS . '">';
		
		foreach ($pages as $Page) {
			
			$html .= '<a href="' . $Page->url . '">' . strip_tags($Page->data[AM_KEY_TITLE]) . '</a>';
			
			// Add separator for all but the last page.	
			if ($i++ < count($pages)) {
				$html .= ' ' . $separator . ' ';
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
					
				$html .= '<li><a' . $class . 'href="?' . http_build_query($query, '', '&amp;') . '">' . AM_HTML_TEXT_FILTER_ALL . '</a></li>';
			
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
		
				$html .= '<li><a' . $class . 'href="' . $targetPage . '?' . http_build_query($query, '', '&amp;') . '">' . $tag . '</a></li>';
		
			}
		
			$html .= '</ul>';
		
			return $html;
			
		}
		
	}
	

	/**
	 *	Generate the HTML for a set of resized images linking to their bigger versions.
	 *
	 *	@param array $files (array with file names)
	 *	@param integer $width
	 *	@param integer $height
	 *	@param integer $crop
	 * 	@param string $class
	 *	@param integer $firstWidth (width for the first image)
	 *	@param integer $firstHeight (height for the first image)
	 *	@param string $firstClass (wrapping class for the first image)
	 *	@param integer $enlargedWidth (maximum width of enlarged images)
	 *	@param integer $enlargedHeight (maximum height of enlarged images)
	 *	@return The HTML of a list of images as links to their bigger versions.
	 */
	
	public static function generateImageSet(
					$files, 
					$width = false, 
					$height = false, 
					$crop = false, 
					$class = false,
					$firstWidth = false, 
					$firstHeight = false, 
					$firstClass = false,
					$enlargedWidth = false,
					$enlargedHeight = false
				) {
			
		if (is_array($files)) {
			
			$html = '';
			$first = true;
			
			foreach($files as $file) {
				
				// Save $class, $width and $hight in temporary variables for standard images, to keep original parameters untouched and avoid overwriting these in case the first image has special values.
				if ($class) {
					$classAttribute = ' class="' . $class . '"';
				} else {
					$classAttribute = '';
				}
				
				$w = $width;
				$h = $height;
				
				// Temporary variables can (if defined) be overwritten here for the first image.
				if ($first) {
					
					if ($firstClass) {
						$classAttribute = ' class="' . $firstClass . '"';
					}
					
					if ($firstWidth) {
						$w = $firstWidth;
					}
					
					if ($firstHeight) {
						$h = $firstHeight;
					}
					
					$first = false;
					
				}
				
				// Add image.
				$enlargedImage = new Image($file, $enlargedWidth, $enlargedHeight);
				$html .= '<div' . $classAttribute . '>' . self::addImage($file, $w, $h, $crop, $enlargedImage->file, false, AM_HTML_CLASS_LIST_ITEM_IMG, true) . '</div>';
				
			}
			
			return $html;
			
		}
	
	}
	
	
	/**
	 *	Generate the HTML for a page list out of a selection of pages, an array of variables and optional image settings.
	 *
	 *	@param array $pages (selected pages)
	 *	@param array $vars (variables to output in the list)
	 *	@param string $glob (one or more glob patterns to find a corresponding image within each page directory)
	 *	@param integer $width
	 *	@param integer $height
	 *	@param integer $crop
	 * 	@param string $class (optional class for list items)
	 *	@param integer $maxChars (maximum number of characters to be displayed for each variable)
	 *	@param string $header (the list's header)
	 *	@param array $style (an array of css properties like { property: "page_variable_name", ... } to override the site's CSS on per item basis via inline styles)
	 *	@param integer $firstWidth (width for the image of the first list item)
	 *	@param integer $firstHeight (height for the image of the first list item)
	 *	@param string $firstClass (wrapping class for the first list item)
	 *	@return the HTML of the list
	 */
	
	public static function generateList(
					$pages, 
					$vars, 
					$glob = false, 
					$width = false, 
					$height = false, 
					$crop = false, 
					$class = false, 
					$maxChars = false, 
					$header = false, 
					$style = false, 
					$firstWidth = false, 
					$firstHeight = false, 
					$firstClass = false
				) {
		
		if ($pages) {
						
			if (!$class) {
				$class = AM_HTML_CLASS_LIST_ITEM;
			}
			
			if (!$maxChars) {
				$maxChars = AM_HTML_LIST_MAX_CHARS;
			}
			
			$html = '';
			
			if ($header) {
				$html .= '<div class="' . AM_HTML_CLASS_LIST_HEADER . '">' . $header . '</div>';
			}
						
			$html .= '<ul class="' . AM_HTML_CLASS_LIST . '">';
		
			// Set $first to true to determine first element in forach loop.
			$first = true;
		
			foreach ($pages as $Page) {
				
				// Define $cls, $w and $h per item to avoid overwriting originals and allow special values for the first item.
				$cls = $class;
				$w = $width;
				$h = $height;
				
				// First item only.
				if ($first) {
			
					if ($firstClass) {
						$cls = $firstClass;
					}
					
					if ($firstWidth) {
						$w = $firstWidth;
					}
					
					if ($firstHeight) {
						$h = $firstHeight;
					}
					
					$first = false;
					
				}
				
				// Build string for inline styles to override the site's CSS.
				$styleAttribute = '';
				
				if (is_array($style)) {
						
					foreach ($style as $property => $propertyVariable) {
						if (isset($Page->data[$propertyVariable])) {
							$styleAttribute .= $property . ': ' . $Page->data[$propertyVariable] . '; ';
						}
					}
					
					if ($styleAttribute) {	
						$styleAttribute = ' style="' . trim($styleAttribute) . '"';
					}
					
				}
				
				$html .= '<li class="' . $cls . '"><a href="' . $Page->url . '"' . $styleAttribute . '>';
				
				// Image
				if ($glob) {
					
					// For each page, the file pattern(s) is (are) matched against the page's direcory (if the glob is relative),
					// to find a corresponding image as thumbnail.
					// For example $glob = '*.jpg *.png' will always use the first JPG in the page's directoy and if nothing is found, it will try the first PNG.
					$images = Parse::fileDeclaration($glob, $Page);
					$image = reset($images);		
					$html .= Html::addImage($image, $w, $h, $crop, false, false, AM_HTML_CLASS_LIST_ITEM_IMG);
					
				}
			
				$html .= '<div class="' . AM_HTML_CLASS_LIST_ITEM_DATA . '">';
			
				// Variables
				foreach ($vars as $var) {
				
					if (isset($Page->data[$var])) {
						
						$text = strip_tags($Page->data[$var]);
						
						// Shorten $text to maximal characters (full words).
						if (strlen($text) > $maxChars) {
							// Cut $text to max chars
							$text = substr($text, 0, $maxChars);
							// Find last space and get position
							$pos = strrpos($text, ' ');
							// Cut $text again at last space's position (< $maxChars)
							$text = substr($text, 0, $pos) . ' ...';
						}
					
						// Variable key is used to define the html class.
						// That makes styling with CSS very customizable.
						$html .= '<div class="' . $var . '">' . $text . '</div>';
						
					}
				
				}
			
				$html .= '</div></a></li>';
			
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
	 * 	@param string $class - optional wrapping class for the <ul>
	 *	@return the HTML of the navigation
	 */
	
	public static function generateNav($pages, $class = false) {
		
		if ($pages) {
		
			if (!$class) {
				$class = AM_HTML_CLASS_NAV;
			}
		
			$html = '<ul class="' . $class . '">';
		
			foreach($pages as $Page) {
				$html .= '<li>' . self::addLink($Page) . '</li>'; 
			}
		
			$html .= '</ul>';
		
			return $html;
			
		}
		
	}
	
	
	/**
	 * 	Generate search field.
	 *	
	 *	@param string $url (URL of the results page)
	 *	@param string $placeholder (placeholder text)
	 *	@param string $formClass	
	 *	@param string $inputClass 
	 *	@param string $button (button text)
	 *	@param string $buttonClass
	 *	@return the HTML for the search field
	 */
	
	public static function generateSearchField($url, $placeholder = false, $formClass = false, $inputClass = false, $button = false, $buttonClass = false) {
		
		if ($placeholder) {
			$placeholder = ' placeholder="' . $placeholder . '"';
		}
		
		if ($formClass) {
			$formClass = ' class="' . $formClass . '"';
		}
		
		if ($inputClass) {
			$inputClass = ' class="' . $inputClass . '"';
		}
		
		if ($buttonClass) {
			$buttonClass = ' class="' . $buttonClass . '"';
		}
		
		if ($button) {
			$button = '<button' . $buttonClass . ' type="submit">' . $button . '</button>';
		}
		
		return 	'<form' . $formClass . ' method="get" action="' . $url . '" onsubmit="return (this.search.value.length > 0)">' .
			'<input' . $inputClass . ' type="text" name="search"' . $placeholder . ' value="" />' .
			$button .
			'</form>';

	}


	/**
	 *	Generate the HTML for a list of sort buttons (item & order).
	 * 
	 * 	@param array $options
	 * 	@return The sort menu's HTML
	 */

	public static function generateSortMenu($options) {
		
		// Get all items from the query string to keep the current filters/search items when building the links. 
		$query = Parse::queryArray();
		
		// Determine the current sort settings, by merging the default options with possible items from the query string.
		$current = array_merge(reset($options), $query);
			
		$html = '<ul class="' . AM_HTML_CLASS_SORT . '">';
	
		foreach ($options as $text => $opt) {
			
			// Test. whether the current "button" matches the current sort settings.
			if ($current['sortItem'] == $opt['sortItem'] && $current['sortOrder'] == $opt['sortOrder']) {
				$class = ' class="' . AM_HTML_CLASS_CURRENT . '" ';
			} else {
				$class = ' ';
			}
			
			// Merge query with sorting options. The second array just makes sure, 
			// that both items (item & order) are overwritten in the current "button", even if $opt doesn't have both keys. 
			$query = array_merge($query, array('sortItem' => '', 'sortOrder' => false), $opt);
			ksort($query);
	
			$html .= '<li><a' . $class . 'href="?' . http_build_query($query, '', '&amp;') . '">' . $text . '</a></li>';
	
		}

		$html .= '</ul>';
		
		return $html;
		
	}

	
	/**
	 *	Branch out recursively below a certain relative URL.
	 *
	 *	@param string $parentUrl
	 *	@param boolean $expandAll
	 *	@param array $collection (all pages)
	 *	@return the HTML for the branch/tree (recursive)
	 */

	public static function generateTree($parentUrl, $expandAll, $collection) {
		
		$Selection = new Selection($collection);
		$Selection->filterByParentUrl($parentUrl);
		$Selection->sortPagesByBasename();
		
		$pages = $Selection->getSelection();
		
		if ($pages) {
				
			// Use first element in $pages to determine the current level.
			$pagesKeys = array_keys($pages);
			$level = ' level-' . $pages[array_shift($pagesKeys)]->level;
		
			$html = '<ul class="' . AM_HTML_CLASS_TREE . $level . '">';	
		
			foreach ($pages as $Page) {
			
				$html .= '<li>';
				$html .= self::addLink($Page);
								
				if ($expandAll || $Page->isCurrent() || $Page->isInCurrentPath()) {			
					$html .= self::generateTree($Page->url, $expandAll, $collection);
				}
			
				$html .= '</li>';
			
			}

			$html .= '</ul>';
		
			return $html;
		
		}
		
	}

	
}


?>