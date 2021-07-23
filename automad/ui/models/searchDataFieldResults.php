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
 *	Copyright (c) 2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\UI\Models;

defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	A wrapper class for all results for a given data field.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class SearchDataFieldResults {


	/**
	 *	The field name.
	 */

	public $key;


	/**
	 *	The original search string or regex.
	 */

	public $searchValue;


	/**
	 *	An array with all found matches in the field value. 
	 *	Note that the matches can differ in case the search value is an unescaped regex string.
	 */

	public $matches = false;


	/**
	 *	A presenation string of all joined matches with wrapping context.
	 */

	public $context = '';
	

	/**
	 * Initialize a new field results instance.
	 *
	 * @param string $key
	 * @param string $searchValue
	 * @param array $matches
	 * @param string $context
	 */

	public function __construct($key, $searchValue, $matches, $context) {

		$this->key = $key;
		$this->searchValue = $searchValue;
		$this->matches = $matches;
		$this->context = $context;

	}


}