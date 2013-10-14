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
 *	The Parse class holds all parsing methods.
 */
 
 
class Parse {
	
	
	/**
	 *	Loads and parses a text file.
	 *
	 *	First it separates the different blocks into simple key/value pairs.
	 *	Then it creates an array of vars by splitting the pairs. 
	 * 
	 *	@param string $file
	 *	@return array $vars
	 */
	 
	public function textFile($file) {
		
		// split $file into data blocks
		$pairs = explode(DATA_BLOCK_SEPARATOR, file_get_contents($file));
		
		// split $pairs into an array of vars
		$vars = array();
		foreach ($pairs as $pair) {
		
			list($key, $value) = explode(DATA_PAIR_SEPARATOR, $pair, 2);
			$vars[trim($key)] = trim($value);	
			
		}
		
		return $vars;
		
	}
 
 
 	/**
 	 *	Extracts the tags string out of a given array and returns an array with these tags.
 	 *
 	 *	@param array $data
 	 *	@return array $tags
 	 */
	
	public function extractTags($data) {
		
		$tags = array();
		
		foreach ($data as $key => $value) {
		
			if ($key == DATA_TAGS_KEY) {
	
				// All tags are splitted into an array
				$tags = explode(DATA_TAG_SEPARATOR, $value);
				// Trim Tags
				$tags = array_map(function($tag) {
						return trim($tag); 
					}, $tags);
				
			}		
			
		}
		
		return $tags;
		
	}
 	
 
}
 
 
?>
