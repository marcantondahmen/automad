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
 * Copyright (c) 2013-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Engine;

use Automad\Core\Automad;
use Automad\Core\Debug;
use Automad\Core\Resolve;
use Automad\Engine\Processors\ContentProcessor;
use Automad\Engine\Processors\PostProcessor;
use Automad\Engine\Processors\TemplateProcessor;
use Automad\UI\InPage;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The View class holds all methods to render the current page using a template file.
 *
 * When render() is called, first the template file gets loaded.
 * The output, basically the raw template HTML (including the generated HTML by PHP in the template file)
 * gets stored in $output.
 *
 * In a second step all statements and content in $output gets processed.
 *
 * That way, it is possible that the template.php file can include HTML as well as PHP, while the "user-generated" content in the text files
 * can not have any executable code (PHP). There are no "eval" functions needed, since all the PHP gets only included from the template files,
 * which should not be edited by users anyway.
 *
 * In a last step, all URLs within the generated HTML get resolved to be relative to the server's root (or absolute), before $output gets returned.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class View {
	private $Automad;
	private $headless;

	public function __construct(Automad $Automad, bool $headless = false) {
		$this->Automad = $Automad;
		$this->headless = $headless;

		$Page = $Automad->Context->get();

		// Redirect page, if the defined URL variable differs from the original URL.
		if ($Page->url != $Page->origUrl) {
			$url = Resolve::absoluteUrlToRoot(Resolve::relativeUrlToBase($Page->url, $Page));
			header('Location: ' . $url, true, 301);
			exit();
		}

		// Set template.
		if ($this->headless) {
			$this->template = Headless::getTemplate();
		} else {
			$this->template = $Page->getTemplate();
		}

		Debug::log($Page, 'New instance created for the current page');
	}

	public function render() {
		Debug::log($this->template, 'Render template');

		$Runtime = new Runtime($this->Automad);
		$InPage = new InPage();

		$ContentProcessor = new ContentProcessor(
			$this->Automad,
			$Runtime,
			$InPage,
			$this->headless
		);

		$TemplateProcessor = new TemplateProcessor(
			$this->Automad,
			$Runtime,
			$ContentProcessor
		);

		$output = $TemplateProcessor->process(
			$this->Automad->loadTemplate($this->template),
			dirname($this->template)
		);

		$PostProcessor = new PostProcessor($InPage, $this->headless);

		$output = $PostProcessor->process($output);

		return trim($output);
	}
}
