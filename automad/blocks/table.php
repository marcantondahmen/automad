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
 *	Copyright (c) 2020-2021 by Marc Anton Dahmen
 *	https://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	https://automad.org/license
 */


namespace Automad\Blocks;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The table block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 *	@license MIT license - https://automad.org/license
 */

class Table extends Paragraph {


	/**	
	 *	Render a table block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {

		$class = self::classAttr();
		$html = "<am-table $class><table><thead>"; 
		$first = true;
		
		// Initially set cell tag to "th".
		$tag = 'th';
		
		foreach ($data->content as $row) {

			$html .= '<tr>'; 
			
			foreach ($row as $item) {
				$html .= "<$tag>$item</$tag>";
			}
			
			$html .= '</tr>';

			if ($first) {
				$html .= '</thead><tbody>';
				$first = false;
				// For every row after the first one, set the cell tag to "td".
				$tag = 'td';
			}

		}
		
		$html .= '</tbody></table></am-table>';

		return $html;

	}


}