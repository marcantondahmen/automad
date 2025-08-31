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
 * Copyright (c) 2013-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine;

use Automad\Admin\InPage;
use Automad\Core\Automad;
use Automad\Core\Debug;
use Automad\Core\Resolve;
use Automad\Engine\Processors\PostProcessor;
use Automad\Engine\Processors\TemplateProcessor;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The View class is responsible for rendering the requeste page.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class View {
	/**
	 * The main Automad instance.
	 */
	private Automad $Automad;

	/**
	 * The template for the currently rendered view.
	 */
	private string $template;

	/**
	 * The view constructor.
	 *
	 * @param Automad $Automad
	 */
	public function __construct(Automad $Automad) {
		$Page = $Automad->Context->get();

		// Redirect page, if the defined URL variable differs from the original URL.
		if ($Page->url != $Page->origUrl) {
			$url = Resolve::absoluteUrlToRoot(Resolve::relativeUrlToBase($Page->url, $Page));
			header('Location: ' . $url, true, 301);
			exit();
		}

		$this->Automad = $Automad;
		$this->template = $Page->getTemplate();
		$this->loadGlobals();

		Debug::log($Page, 'New instance created for the current page');
	}

	/**
	 * Render a page.
	 *
	 * @return string the rendered page
	 */
	public function render(): string {
		Debug::log($this->template, 'Render template');

		$InPage = new InPage($this->Automad);
		$TemplateProcessor = TemplateProcessor::create($this->Automad, $InPage);

		$output = $this->Automad->loadTemplate($this->template);
		$directory = dirname($this->template);

		// Process template first in order to collect all snippet definitions
		// without saving the generated output in order to enable snippet overrides.
		$TemplateProcessor->process($output, $directory, true);

		// Process template a second time but without overriding any registered snippet
		// definition and saving the output. Note that unknown snippets that haven't been registered
		// and that are defined in an override that has not been evaluated in the first step
		// are registered in this step.
		$output = $TemplateProcessor->process($output, $directory, false);

		$PostProcessor = new PostProcessor($this->Automad, $InPage);

		$output = $PostProcessor->process($output);

		return trim($output);
	}

	/**
	 * Load global template helpers such as func().
	 */
	private function loadGlobals(): void {
		require_once __DIR__ . '/Globals/func.php';
		require_once __DIR__ . '/Globals/inc.php';
	}
}
