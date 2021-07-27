<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Components\Header;

use Automad\UI\Utils\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The block snippet arrays as JS variable.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class BlockSnippetArrays {
	/**
	 * Return a script tag to define the block snippet arrays for the block editor.
	 *
	 * @return string The script tag
	 */
	public static function render() {
		return 	'<script>var AutomadBlockTemplates = ' .
				json_encode(
					array(
						'filelist' => FileSystem::getPackagesDirectoryItems('/\/blocks\/filelist\/[^\/]+\.php$/'),
						'pagelist' => FileSystem::getPackagesDirectoryItems('/\/blocks\/pagelist\/[^\/]+\.php$/'),
						'snippets' => FileSystem::getPackagesDirectoryItems('/\/snippets\/[^\/]+\.php$/')
					),
					JSON_UNESCAPED_SLASHES
				) .
				'</script>';
	}
}
