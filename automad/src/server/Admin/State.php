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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin;

use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\FileUtils;
use Automad\Core\Parse;
use Automad\Core\Session;
use Automad\Models\MailConfig;
use Automad\Models\UserCollection;
use Automad\System\Fields;
use Automad\System\PackageCollection;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The State class contains all functionality to create the application state.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class State {
	/**
	 * The state data.
	 */
	private array $data;

	/**
	 * The constructor
	 */
	public function __construct() {
		$this->data = $this->create();
	}

	/**
	 * Return the current state array.
	 *
	 * @return array the state array
	 */
	public function get(): array {
		return $this->data;
	}

	/**
	 * Create a fresh state.
	 *
	 * @return array the state data
	 */
	private function create(): array {
		$Automad = Automad::fromCache();
		$UserCollection = new UserCollection();
		$ThemeCollection = new ThemeCollection();
		$themes = $ThemeCollection->getThemes();
		$Cache = new Cache();
		$MailConfig = new MailConfig();

		$data = array(
			'allowedFileTypes' => FileUtils::allowedFileTypes(),
			'contentFields' => $this->getContentFields($themes),
			'feed' => AM_SERVER . AM_BASE_INDEX . AM_FEED_URL,
			'mainTheme' => $Automad->Shared->get(Fields::THEME),
			'pages' => $Automad->getNavigationMetaData(),
			'siteMTime' => date(DATE_ATOM, $Cache->getSiteMTime()),
			'sitename' => $Automad->Shared->get(Fields::SITENAME),
			'sharedPublicationState' => $Automad->Shared->get(Fields::PUBLICATION_STATE),
			'componentsPublicationState' => $Automad->ComponentCollection->getPublicationState(),
			'components' => $Automad->ComponentCollection->get(),
			'files' => array(
				'pagelist' => PackageCollection::getPackagesDirectoryItems('/\/blocks\/pagelist\/[^\/]+\.php$/'),
				'filelist' => PackageCollection::getPackagesDirectoryItems('/\/blocks\/filelist\/[^\/]+\.php$/'),
				'snippets' => PackageCollection::getPackagesDirectoryItems('/\/snippets\/[^\/]+\.php$/'),
			),
			'system' => array(
				'cache' => array(
					'enabled' => AM_CACHE_ENABLED,
					'lifetime' => AM_CACHE_LIFETIME,
					'monitorDelay' => AM_CACHE_MONITOR_DELAY
				),
				'debug' => AM_DEBUG_ENABLED,
				'feed' => array(
					'enabled' => AM_FEED_ENABLED,
					'fields' => Parse::csv(AM_FEED_FIELDS)
				),
				'i18n' => AM_I18N_ENABLED,
				'mail' => array(
					'transport' => $MailConfig->transport,
					'from' => $MailConfig->from,
					'fromDefault' => $MailConfig->getDefaultFrom(),
					'smtpServer' => $MailConfig->smtpServer,
					'smtpUsername' => $MailConfig->smtpUsername,
					'smtpPort' => $MailConfig->smtpPort,
					'smtpPasswordIsSet' => strlen($MailConfig->smtpPassword) > 0
				),
				'translation' => AM_FILE_UI_TRANSLATION,
				'users'=> array_values($UserCollection->getCollection())
			),
			'tags' => $Automad->Pagelist->getTags(),
			'themes' => $themes,
			'user' => $UserCollection->getUser(Session::getUsername())
		);

		return $data;
	}

	/**
	 * Get all relevant text based fields from all themes.
	 *
	 * @param array<int, Theme> $themes
	 * @return array the fields array
	 */
	private function getContentFields(array $themes): array {
		$fields = array();

		foreach ($themes as $Theme) {
			foreach ($Theme->templates as $file) {
				$fields = array_merge($fields, Fields::inTemplate($file));
			}
		}

		$fields = array_unique($fields);
		$fields = array_filter($fields, function ($field) {
			return preg_match('/^(\+|text)/', $field);
		});

		return array_values($fields);
	}
}
