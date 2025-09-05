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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine\Processors;

use Automad\Admin\InPage;
use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Core\FileUtils;
use Automad\Core\Image;
use Automad\Core\Request;
use Automad\Core\SessionData;
use Automad\Core\Str;
use Automad\Engine\Delimiters;
use Automad\Engine\PatternAssembly;
use Automad\Engine\Pipe;
use Automad\Engine\Runtime;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The content processor class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class ContentProcessor {
	/**
	 * The main Automad instance.
	 */
	private Automad $Automad;

	/**
	 * The InPage instance.
	 */
	private InPage $InPage;

	/**
	 * The Runtime instance.
	 */
	private Runtime $Runtime;

	/**
	 * The content processor constructor.
	 *
	 * @param Automad $Automad
	 * @param Runtime $Runtime
	 * @param InPage $InPage
	 */
	public function __construct(
		Automad $Automad,
		Runtime $Runtime,
		InPage $InPage
	) {
		$this->Automad = $Automad;
		$this->Runtime = $Runtime;
		$this->InPage = $InPage;
	}

	/**
	 * Process a file related snippet like `<@ foreach "*.jpg" { options } @> ... <@ end @>`.
	 *
	 * @param string $file
	 * @param array $options
	 * @param string $snippet
	 * @param string $directory
	 * @return string the processed file snippet
	 */
	public function processFileSnippet(
		string $file,
		array $options,
		string $snippet,
		string $directory,
	): string {
		// Shelve runtime data.
		$runtimeShelf = $this->Runtime->shelve();

		// Store current filename and its basename in the system variable buffer.
		$this->Runtime->set(Fields::FILE, $file);
		$this->Runtime->set(Fields::BASENAME, basename($file));

		// If $file is an image, also provide width and height (and possibly a new filename after a resize).
		if (FileUtils::fileIsImage($file)) {
			// The Original file size.
			$imgSize = getimagesize(AM_BASE_DIR . $file);

			if ($imgSize) {
				$this->Runtime->set(Fields::WIDTH, $imgSize[0]);
				$this->Runtime->set(Fields::HEIGHT, $imgSize[1]);
			}

			// If any options are given, create a resized version of the image.
			if (!empty($options)) {
				$options = 	array_merge(
					array(
						'width' => 0,
						'height' => 0,
						'crop' => false
					),
					$options
				);

				$img = new Image(AM_BASE_DIR . $file, $options['width'], $options['height'], $options['crop']);
				$this->Runtime->set(Fields::FILE_RESIZED, $img->file);
				$this->Runtime->set(Fields::WIDTH_RESIZED, $img->width);
				$this->Runtime->set(Fields::HEIGHT_RESIZED, $img->height);
			}
		}

		// Process snippet.
		$TemplateProcessor = TemplateProcessor::create($this->Automad, $this->InPage);

		$html = $TemplateProcessor->process($snippet, $directory);

		// Unshelve runtime data.
		$this->Runtime->unshelve($runtimeShelf);

		return $html;
	}

	/**
	 * Process content variables and optional string functions. Like {[ var | function1 ( parameters ) | function2 | ... ]}
	 *
	 * Find and replace all variables within $str with values from either the context page data array or, if not defined there, from the shared data array,
	 * or from the $_GET array (only those starting with a "?").
	 *
	 * By first checking the page data (handled by the Page class), basically all shared data variables can be easily overridden by a page.
	 * Optionally all values can be parsed as "JSON safe" ($isOptionString), by escaping all quotes and wrapping variable is quotes when needed.
	 * In case a variable is used as an option value for a method and is not part of a string, that variable doesn't need to be
	 * wrapped in double quotes to work within the JSON string - the double quotes get added automatically.
	 *
	 * By setting $inPageEdit to true, for every processed variable, a temporary markup for an edit button is appended to the actual value.
	 * That temporary button still has to be processed later by calling processInPageEditButtons().
	 *
	 * @param string $str
	 * @param bool $isOptionString
	 * @param bool $inPageEdit
	 * @return string The processed $str
	 */
	public function processVariables(string $str, bool $isOptionString = false, bool $inPageEdit = false): string {
		// Prepare JSON strings by wrapping all stand-alone variables in quotes.
		if ($isOptionString) {
			$str = preg_replace_callback(
				'/' . PatternAssembly::keyValue() . '/s',
				function ($pair) {
					if (strpos($pair['value'], Delimiters::VAR_OPEN) === 0) {
						$pair['value'] = '"' . trim($pair['value']) . '"';
					}

					return $pair['key'] . ':' . $pair['value'];
				},
				$str
			) ?? '';
		}

		return preg_replace_callback(
			'/' . PatternAssembly::variable('var') . '/s',
			function ($matches) use ($isOptionString, $inPageEdit) {
				// Get the value.
				$value = $this->getValue($matches['varName']);

				// Resolve URLs in content before passing it to pipe functions
				// to make sure images and files can be used correctly in custom
				// pipe functions.
				$value = URLProcessor::resolveUrls($value, 'relativeUrlToBase', array($this->Automad->Context->get()));

				// Get pipe functions.
				$functions = array();

				preg_match_all('/' . PatternAssembly::pipe('pipe') . '/s', $matches['varFunctions'], $pipes, PREG_SET_ORDER);

				foreach ($pipes as $pipe) {
					if (!empty($pipe['pipeFunction'])) {
						$parametersArray = array();

						if (isset($pipe['pipeParameters'])) {
							preg_match_all('/' . PatternAssembly::csv() . '/s', $pipe['pipeParameters'], $pipeParameters, PREG_SET_ORDER);

							foreach ($pipeParameters as $match) {
								$parameter = trim($match[1]);

								if (in_array($parameter, array('true', 'false'))) {
									$parameter = filter_var($parameter, FILTER_VALIDATE_BOOLEAN);
								} else {
									// Remove outer quotes and strip slashes.
									$parameter = preg_replace('/^([\'"])(.*)\1$/s', '$2', $parameter) ?? '';
									$parameter = stripcslashes($this->processVariables($parameter));
								}

								$parametersArray[] = $parameter;
							}
						}

						$functions[] = array(
							'name' => $pipe['pipeFunction'],
							'parameters' => $parametersArray
						);
					}

					// Math.
					if (!empty($pipe['pipeOperator'])) {
						$functions[] = array(
							'name' => $pipe['pipeOperator'],
							'parameters' => $this->processVariables($pipe['pipeNumber'])
						);
					}
				}

				// Modify $value by processing all matched string functions.
				$value = Pipe::process($value, $functions);

				// Escape values to be used in option strings.
				if ($isOptionString) {
					$value = Str::escape($value);
				}

				// Inject "in-page edit" button in case varName starts with a word-char and an user is logged in.
				// The button needs to be wrapped in delimiters to enable a secondary cleanup step to remove buttons within HTML tags.
				if ($inPageEdit) {
					$value = $this->InPage->injectTemporaryEditButton(
						$value,
						$matches['varName'],
						$this->Automad->Context
					);
				}

				return $value;
			},
			$str
		) ?? '';
	}

	/**
	 * Get the value of a given variable key depending on the current context - either from the page data,
	 * the system variables or from the $_GET array.
	 *
	 * @param string $key
	 * @return string The value
	 */
	private function getValue(string $key): string {
		// Query string parameter.
		if (strpos($key, '?') === 0) {
			$key = substr($key, 1);

			return Request::query($key);
		}

		// Session variable.
		if (strpos($key, '%') === 0) {
			return SessionData::get($key);
		}

		// Blocks variable.
		if (strpos($key, '+') === 0) {
			$value = Blocks::render(
				$this->Automad->Context->get()->get($key, true),
				$this->Automad
			);

			return $value;
		}

		// First try to get the value from the current Runtime object.
		$value = $this->Runtime->get($key);

		// If $value is NULL (!), try the current context.
		if (is_null($value)) {
			$value = $this->Automad->Context->get()->get($key);
		}

		return strval($value);
	}
}
