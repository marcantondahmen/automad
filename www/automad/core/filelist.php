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
 *	Copyright (c) 2015 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Filelist object represents a set of files based on a file pattern.
 *
 *	@author Marc Anton Dahmen <hello@marcdahmen.de>
 *	@copyright Copyright (c) 2015 Marc Anton Dahmen <hello@marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */


class Filelist {
	
	
	/**
	 * 	The Context.
	 */
	
	private $Context;
	
	
	/**
	 * 	The default options.
	 */
	
	private $defaults =	array(
					'glob' => '*.jpg, *.png, *.gif',
					'sortOrder' => 'asc'
				);
	
	/**
	 *	The set of matched files. 
	 */
	
	private $files = array();
	
	
	/**
	 *	The constructor.
	 *
	 *	@param object $Context
	 */
	
	public function __construct($Context) {
		
		$this->Context = $Context;
		$this->config($this->defaults);
		
	}
	
	
	/**
	 * 	Configure the files array.
	 *	
	 *	@param array $options
	 */
	
	public function config($options) {
		
		// Merge defaults and options.
		$options = array_merge($this->defaults, $options);
		
		// Find files.
		$files = Parse::fileDeclaration($options['glob'], $this->Context->get(), true);

		// Sorting files.
		if ($options['sortOrder'] == 'asc') {
			sort($files);
		} 
		
		if ($options['sortOrder'] == 'desc') {
			rsort($files);
		} 
				
		Debug::log($files);
		
		$this->files = $files;
		
	}
	
	
	/**
	 * 	Return the files array.
	 *
	 *	@return The array of matched files.
	 */
	
	public function getFiles() {
		
		return $this->files;
		
	}
	
	
}


?>