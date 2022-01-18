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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Views;

use Automad\System\Asset;
use Automad\UI\Utils\UICache;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The base for all dashboard views.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
abstract class AbstractView {
	/**
	 * The Automad object.
	 */
	protected $Automad = null;

	/**
	 * This property stores a simple helper to output expression in strings.
	 */
	protected $fn;

	/**
	 * The page constructor.
	 */
	public function __construct() {
		$this->Automad = UICache::get();
		$this->fn = function ($expression) {
			return $expression;
		};
	}

	public function render() {
		$fn = $this->fn;

		return <<< HTML
			<!DOCTYPE html>
			<html lang="en" class="am-ui">
			<head>
				<meta charset="utf-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="robots" content="noindex">
				<meta
				name="viewport"
				content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"
				>
				<title>{$fn($this->title())}</title>
				<script type="text/javascript" src="{$this->dashboard()}/bootstrap.js"></script>
				{$fn(Asset::js('ui.bundle.js'))}
				{$fn(Asset::css('ui.bundle.css'))}
				{$fn(Asset::icon('favicon.ico'))}
			</head>
			{$fn($this->body())}
			</html>
			HTML;
	}

	/**
	 * Render a dashboard page.
	 *
	 * @return string the rendered dashboard
	 */
	abstract protected function body();

	/**
	 * Return the absolute dashboard URL.
	 *
	 * @return string the absolute dashboard URL
	 */
	protected function dashboard() {
		return AM_BASE_INDEX . AM_PAGE_DASHBOARD;
	}

	/**
	 * Get the title for the dashboard view.
	 *
	 * @return string the rendered items
	 */
	abstract protected function title();
}
