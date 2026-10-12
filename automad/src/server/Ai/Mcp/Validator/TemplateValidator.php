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

namespace Automad\Ai\Mcp\Validator;

use Automad\System\ThemeCollection;
use Mcp\Exception\ToolCallException;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The validator for theme and template combinations that are provided by MCP clients.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class TemplateValidator {
	/**
	 * The map of installed theme paths to the lists of their template names.
	 *
	 * @var array<string, array<int, string>>
	 */
	private array $templates;

	/**
	 * The constructor.
	 *
	 * @param array<string, array<int, string>> $templates
	 */
	public function __construct(array $templates) {
		$this->templates = $templates;
	}

	/**
	 * Make sure that a theme is installed and that it contains the given template.
	 *
	 * @param string $theme
	 * @param string $template
	 * @throws ToolCallException
	 */
	public function assertValid(string $theme, string $template): void {
		if (!array_key_exists($theme, $this->templates)) {
			$themes = join(', ', array_map(fn (string $t): string => "`$t`", array_keys($this->templates)));

			throw new ToolCallException(
				"Theme [$theme] not found. Available themes: $themes. " .
				'Use the `page_template_list` tool to get all available themes and templates.'
			);
		}

		if (!in_array($template, $this->templates[$theme], true)) {
			$templates = join(', ', array_map(fn (string $t): string => "`$t`", $this->templates[$theme]));

			throw new ToolCallException(
				"Template [$template] does not exist in theme [$theme]. Available templates: $templates. " .
				'Use the `page_template_list` tool to get all available themes and templates.'
			);
		}
	}

	/**
	 * Create a validator for all installed themes.
	 *
	 * @param ThemeCollection $ThemeCollection
	 * @return TemplateValidator
	 */
	public static function fromThemeCollection(ThemeCollection $ThemeCollection): TemplateValidator {
		$templates = array();

		foreach ($ThemeCollection->getThemes() as $Theme) {
			$templates[$Theme->path] = array_map(
				fn (string $file): string => basename($file, '.php'),
				$Theme->templates
			);
		}

		return new TemplateValidator($templates);
	}
}
