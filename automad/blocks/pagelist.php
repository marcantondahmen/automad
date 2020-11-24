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
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Blocks;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The pagelist block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Pagelist {

	
	/**	
	 *	Render a pagelist block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {

		$Pagelist = $Automad->getPagelist();

		// Reset pagelist.
		$Pagelist->config($Pagelist->getDefaults());

		$defaults = array(
			'type' => '',
			'matchUrl' => '',
			'filter' => '',
			'template' => '',
			'excludeCurrent' => true,
			'limit' => NULL,
			'offset' => 0,
			'sortKey' => ':path',
			'sortOrder' => 'asc',
			'file' => ''
		);

		$options = array_merge($defaults, (array) $data);
		$options['sort'] = $options['sortKey'] . ' ' . $options['sortOrder'];

		if (!empty($options['matchUrl'])) {
			$options['match'] = json_encode(array('url' => '/(' . $options['matchUrl'] . ')/'));
		}

		$Pagelist->config($options);

		$options['file'] = AM_DIR_PACKAGES . $options['file'];

		if (!is_readable(AM_BASE_DIR . $options['file'])) {
			$options['file'] = '/automad/blocks/templates/pagelist.php';
		}

		return Snippet::render((object) $options, $Automad);	

	}


}