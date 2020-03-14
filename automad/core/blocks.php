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


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');


/**
 *	The Blocks class.
 *
 *	@author Marc Anton Dahmen
 *	@copyright Copyright (c) 2020 by Marc Anton Dahmen - <http://marcdahmen.de>
 *	@license MIT license - http://automad.org/license
 */

class Blocks {
	
	
	/**	
	 * 	Render blocks created by the EditorJS block editor.
	 * 	
	 * 	@param string $json
	 * 	@return string the rendered HTML
	 */

	public static function render($json) {
		
		$data = json_decode($json);
		$html = '';

		if (!is_object($data)) {
			return false;
		}

		if (!isset($data->blocks)) {
			return false;
		}

		foreach ($data->blocks as $block) {

			try {

				switch ($block->type) {

					case 'header':
						$html .= self::headerBlock($block->data);
						break;

					case 'list':
						$html .= self::listBlock($block->data);
						break;

					case 'quote':
						$html .= self::quoteBlock($block->data);
						break;

					case 'embed':
						$html .= self::embedBlock($block->data);
						break;

					case 'raw':
						$html .= self::rawBlock($block->data);
						break;

					case 'table':
						$html .= self::tableBlock($block->data);
						break;

					case 'code':
						$html .= self::codeBlock($block->data);
						break;

					case 'image':
						$html .= self::imageBlock($block->data);
						break;

					case 'delimiter':
						$html .= self::delimiterBlock($block->data);
						break;

					default:
						$html .= self::paragraphBlock($block->data);

				}

			} catch (\Exception $e) {
				continue;
			}

		}

		return $html;

	}
	

	/**	
	 *	Render a header block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	private static function headerBlock($data) {

		return "<h$data->level>$data->text</h$data->level>";

	}


	/**	
	 *	Render a list block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	private static function listBlock($data) {

		if ($data->style == 'ordered') {
			$open = '<ol>';
			$close = '</ol>';
		} else {
			$open = '<ul>';
			$close = '</ul>';
		}

		$html = $open;

		foreach ($data->items as $item) {
			$html .= "<li>$item</li>";
		}

		$html .= $close;

		return $html;

	}


	/**	
	 *	Render a quote block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	private static function quoteBlock($data) {

		return <<< HTML
				<figure>
					<blockquote>$data->text</blockquote>
   					<figcaption style="text-align: $data->alignment;">$data->caption</figcaption>
				</figure>	
HTML;

	}


	/**	
	 *	Render a header block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	private static function embedBlock($data) {

		return <<< HTML
			<iframe 
			src="$data->embed"
			height="$data->height"
			width="$data->width"
			scrolling='no' 
			frameborder='no' 
			allowtransparency='true' 
			allowfullscreen='true' 
			style='width: 100%;'
			>
			</iframe>
HTML;
	
	}


	/**	
	 *	Render a raw block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	private static function rawBlock($data) {

		return $data->html;

	}


	/**	
	 *	Render a table block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	private static function tableBlock($data) {

		$html = '<table>'; 
		
		foreach ($data->content as $row) {

			$html .= '<tr>'; 
			
			foreach ($row as $item) {
				$html .= "<th>$item</th>";
			}
			
			$html .= '</tr>';

		}
		
		$html .= '</table>';

		return $html;

	}


	/**	
	 *	Render a code block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	private static function codeBlock($data) {

		return "<pre><code>$data->code</code></pre>";

	}


	/**	
	 *	Render an image block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	private static function imageBlock($data) {

		if (empty($data->caption)) {

			return <<< HTML
					<img src="$data->url" />
HTML;

		} else {

			return <<< HTML
					<figure>
						<img src="$data->url" />
						<figcaption>$data->caption</figcaption>
					</figure>
HTML;

		}

	}


	/**	
	 *	Render a delimiter block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	private static function delimiterBlock($data) {

		return '<hr>';

	}


	/**	
	 *	Render a paragraph block.
	 *	
	 *	@param object $data
	 *	@return string the rendered HTML
	 */

	private static function paragraphBlock($data) {

		return "<p>$data->text</p>";

	}

	
}
