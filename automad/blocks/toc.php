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
use Automad\Core\Str as Str;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The table of contents block.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Toc {


	/**	
	 *	Render a toc block.
	 *	
	 *	@param object $data
	 *	@param object $Automad
	 *	@return string the rendered HTML
	 */

	public static function render($data, $Automad) {

		$Page = $Automad->Context->get();
		$content = json_decode($Page->get($data->key));
		$headers = array();
		$lastLevel = 1;
		$open = 0;
		$html = '';
		
		if ($data->style == 'ordered') {
			$tag = 'ol';
		} else {
			$tag = 'ul';
		}
		
		if (empty($content->blocks)) {
			return false;
		} else {
			$blocks = $content->blocks;
		}

		foreach ($blocks as $block) {

			if ($block->type == 'header') {

				if ($block->data->level > 1 && $block->data->level < 5) {
					$headers[] = (object) array(
						'level' => $block->data->level, 
						'text' => $block->data->text
					);
				}

			}

		}

		foreach ($headers as $header) {

			if ($header->level > $lastLevel) {

				$diff = $header->level - $lastLevel;

				for ($i = 1; $i <= $diff; $i++) {
					$open++;
					$html .= "<$tag><li>";
				}

			}

			if ($header->level < $lastLevel) {

				$diff = $lastLevel - $header->level;

				for ($i = 1; $i <= $diff; $i++) {
					$open--;
					$html .= "</li></$tag>";
				}

			}

			if ($header->level <= $lastLevel) {
				$html .= '</li><li>';
			}

			$html .= '<a href="#' . Str::sanitize($header->text, true) . '">' . $header->text . '</a>';
			$lastLevel = $header->level;

		}

		for ($i = 1; $i <= $open; $i++) {
			$html .= "</li></$tag>";
		}

		return '<figure class="am-toc">' . $html . '</figure>';

	}

}