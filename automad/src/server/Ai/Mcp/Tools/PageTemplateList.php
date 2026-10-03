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

namespace Automad\Ai\Mcp\Tools;

use Automad\System\Fields;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page template list tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageTemplateList extends AbstractTool {
	/**
	 * @return string
	 */
	public function getDescription(): string {
		return 'A list of available page templates.';
	}

	/**
	 * The tool's main handler. Its parameters are reflected on to derive the tool's input schema
	 * and to map incoming call arguments by name.
	 *
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
	 * The tool's input schema.
	 *
	 * @return array
	 */
	public function getInputSchema(): array {
		return array();
	}

	/**
	 * The tool's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page_template_list';
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page: Template List';
	}

	/**
	 * Return true in order to make the tool private.
	 *
	 * @return bool
	 */
	public static function requiresAuth(): bool {
		return true;
	}
}
