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
 *	(c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 */
 
 
/**
 *	The Data class includes all methods to handle data saved in text files.
 */
 
 
class Data {
	
	
	/**
	 *	Loads and parses a text file.
	 *
	 *	First it separates the different blocks into simple key/value pairs.
	 *	Then it creates an array of vars by splitting the pairs. 
	 *	Keys which describe the tags of a page are detected and their value will be returned
	 *	as an array. All other Values are strings.
	 * 
	 *	@param string $file
	 *	@return array $vars
	 */
	 
	public function parseTxt($file) {
		
		// split $file into data blocks
		$pairs = explode(DATA_BLOCK_SEPARATOR, file_get_contents($file));
		
		// split $pairs into an array of vars
		$vars = array();
		foreach ($pairs as $pair) {
		
			list($key, $value) = explode(DATA_PAIR_SEPARATOR, $pair, 2);
			
			$key = trim($key);
			$value = trim($value);
			
			if ($key == DATA_TAGS_KEY) {
				
				// All tags are splitted into an array
				$tags = explode(DATA_TAG_SEPARATOR, $value);
				// trim Tags
				$vars[$key] = 	array_map(function($tag) {
							return trim($tag); 
						}, $tags);
				
			} else {
				
				// All other possible values are strings
				$vars[$key] = $value;
				
			}
			
		}
		
		return $vars;
		
	}
 
 
 
 
}
 
 
?>
