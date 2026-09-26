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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Ai\Mcp\Resources;

use Automad\System\Fields;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page templates resource.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class TemplatesPage extends AbstractResource {
	/**
	 * @return string
	 */
	public function getDescription(): string {
		return 'A list of available page templates.';
	}

	/**
	 * @return callable
	 */
	public function getHandler(): callable {
		return function () {
			$templates = array();
			$ThemesCollection = new ThemeCollection();

			foreach ($ThemesCollection->getThemes() as $Theme) {
				foreach ($Theme->templates as $file) {
					/** @var string $file */
					$template = str_replace('.php', '', basename($file));
					$templateName = ucwords(str_replace('_', ' ', basename($template)));
					$fields = array_filter(Fields::inTemplate($file), fn ($f) => str_starts_with($f, '+') && !in_array($f, $Theme->masks['page']));
					$fieldsStr = join(', ', array_map(fn ($f) => "`$f`", $fields));
					$fieldsData = array_map(
						function (string $f) use ($Theme): array {
							return array(
								'field' => $f,
								'description' => $Theme->tooltips[$f] ?? ''
							);
						},
						$fields
					);

					$templates[] = array(
						'template' => $template,
						'theme' => $Theme->path,
						'fields' => $fieldsData,
						'description' => <<< TXT
							The [$templateName] template of the [$Theme->name] theme, $Theme->description.
							TXT
					);
				}
			}

			return $templates;
		};
	}

	/**
	 * The resource's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'templates/page';
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return 'Templates / Page';
	}
}
