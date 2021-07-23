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
	 * @param string $searchValue
	 * @param string $key
	 * @param string $value
	 */

	public function __construct($searchValue, $key, $value) {

		$this->key = $key;
		$this->searchValue = $searchValue;

		preg_match_all(
			'/(.{0,20})(' . $searchValue . ')(.{0,20})/is',
			$value,
			$matches,
			PREG_SET_ORDER
		);

		if (!empty($matches[0])) {

			$parts = array();

			foreach ($matches as $match) {
				$parts[] = preg_replace('/\s+/', ' ', trim("{$match[1]}<mark>{$match[2]}</mark>{$match[3]}"));
			}

			$this->context = join(' ... ', $parts);

			foreach ($matches as $match) {
				$this->matches[] = $match[2];
			}

		}

	}


}