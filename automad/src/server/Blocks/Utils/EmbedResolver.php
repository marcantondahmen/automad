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

namespace Automad\Blocks\Utils;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The EmbedResolver class resolves a 3rd-party source URL into an embeddable URL along with its service name and dimensions.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class EmbedResolver {
	/**
	 * Resolve a source URL against the supported embed services
	 * and return its embed HMTL and service key.
	 *
	 * @param string $url
	 * @return string|null
	 */
	public static function getHtml(string $url): string|null {
		foreach (self::getServices() as $service => $definition) {
			if (!preg_match($definition['regex'], $url, $matches)) {
				continue;
			}

			$groups = array_slice($matches, 1);
			$idFn = $definition['id'];
			$remoteId = $idFn !== null ? $idFn($groups) : ($groups[0] ?? '');

			if ($remoteId === '') {
				continue;
			}

			$embedUrl = str_replace('{{ remoteId }}', $remoteId, $definition['embedUrl']);
			$html = str_replace(array('{{ embedUrl }}', '{{ url }}'), array($embedUrl, $url), $definition['html']);

			$iframeAttr = 'frameborder="no" allowtransparency="true" allowfullscreen="true"';
			$html = str_replace('<iframe', "<iframe $iframeAttr", $html);

			return $html;
		}

		return null;
	}

	/**
	 * Return a list of keys of registered services.
	 *
	 * @return string[]
	 */
	public static function getServiceKeys(): array {
		return array_keys(self::getServices());
	}

	/**
	 * Return the supported embed services table.
	 *
	 * @return array<string, array{regex: string, embedUrl: string, html: string, id: (callable(string[]): string)|null}>
	 */
	private static function getServices(): array {
		return array(
			'codepen' => array(
				'regex' => '~https?://codepen\.io/([^?&]*)/pen/([^/?&]*)~',
				'embedUrl' => 'https://codepen.io/{{ remoteId }}?height=300&theme-id=0&default-tab=css,result&embed-version=2',
				'html' => '<iframe src="{{ embedUrl }}" height="300" style="width: 100%; aspect-ratio: 16/9;" scrolling="no"></iframe>',
				'id' => fn (array $groups): string => implode('/embed/', $groups),
			),
			'dailymotion' => array(
				'regex' => '~https?://www\.dailymotion\.com/video/(\w+)(\?.*?)?$~D',
				'embedUrl' => 'https://www.dailymotion.com/embed/video/{{ remoteId }}/',
				'html' => '<iframe src="{{ embedUrl }}" style="aspect-ratio: 16/9; width: 100%;"></iframe>',
				'id' => null,
			),
			'facebook' => array(
				'regex' => '~https?://www\.facebook\.com/([^/?&]*)/(.*)~',
				'embedUrl' => 'https://www.facebook.com/plugins/post.php?href=https://www.facebook.com/{{ remoteId }}&width=500',
				'html' => '<iframe src="{{ embedUrl }}" style="margin: 0 auto; width: 500px; min-height: 500px; max-height: 1000px;"></iframe>',
				'id' => fn (array $groups): string => implode('/', $groups),
			),
			'giphy' => array(
				'regex' => '~https?://giphy\.com/(?:gifs|videos)/(?:[^/]*\-)?([a-zA-Z0-9]+)$~D',
				'embedUrl' => 'https://giphy.com/embed/{{ remoteId }}/',
				'html' => '<iframe src="{{ embedUrl }}" width="600" height="480" style="width: 100%;"></iframe>',
				'id' => null,
			),
			'github' => array(
				'regex' => '~https?://gist\.github\.com/([^/?&]*)/([^/?&]*)~',
				'embedUrl' => 'data:text/html;charset=utf-8,<head><base target="_blank"></head><body><script src="https://gist.github.com/{{ remoteId }}"></script></body>',
				'html' => '<iframe src=\'{{ embedUrl }}\' width="100%" height="500" style="margin: 0 auto; width: 100%;"></iframe>',
				'id' => fn (array $groups): string => implode('/', $groups) . '.js',
			),
			'imgur' => array(
				'regex' => '~https?://(?:i\.)?imgur\.com(?:/gallery)?/([\w-]+)(?:\.gifv)?~',
				'embedUrl' => '{{ remoteId }}',
				'html' => <<<HTML
					<blockquote class="imgur-embed-pub" lang="en" data-id="a/{{ embedUrl }}">
						<a href="//imgur.com/a/{{ embedUrl }}"></a>
					</blockquote>
					<script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script>
				HTML,
				'id' => function (array $groups): string {
					/** @var string */
					return preg_replace('/^.*?\-([^-]+)$/', '$1', $groups[0] ?? '');
				},
			),
			'instagram' => array(
				'regex' => '~https?://www\.instagram\.com/(?:[^/]+/)?p/([^/?&]+)/?~',
				'embedUrl' => 'https://www.instagram.com/p/{{ remoteId }}/embed',
				'html' => '<iframe src="{{ embedUrl }}" width="400" height="505" style="margin: 0 auto;"></iframe>',
				'id' => null,
			),
			'mixcloud' => array(
				'regex' => '~https?://www\.mixcloud\.com/(.+)/$~D',
				'embedUrl' => 'https://www.mixcloud.com/widget/iframe/?hide_cover=1&feed=/{{ remoteId }}/',
				'html' => '<iframe src="{{ embedUrl }}" height="180" style="width: 100%;"></iframe>',
				'id' => null,
			),
			'soundcloud' => array(
				'regex' => '~(https://soundcloud\.com/.+)$~D',
				'embedUrl' => 'https://w.soundcloud.com/player/?url={{ remoteId }}',
				'html' => '<iframe src="{{ embedUrl }}" height="180" style="width: 100%;"></iframe>',
				'id' => null,
			),
			'twitter' => array(
				'regex' => '~^(https?://(?:www\.)?(?:twitter\.com|x\.com)/(.+?)/status/(\d+)(?:\?.*)?$)~D',
				'embedUrl' => 'https://twitter.com/{{ remoteId }}',
				'html' => <<< HTML
					<blockquote class="twitter-tweet tw-align-center">
						<a href="{{ url }}">
							<am-consent-placeholder></am-consent-placeholder>
						</a>
					</blockquote>
					<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
					HTML,
				'id' => null,
			),
			'vimeo' => array(
				'regex' => '~^https?://(?:www\.)?vimeo\.com/(\d+).*$~D',
				'embedUrl' => 'https://player.vimeo.com/video/{{ remoteId }}?title=0&byline=0',
				'html' => '<iframe src="{{ embedUrl }}" style="width:100%; aspect-ratio: 16/9;"></iframe>',
				'id' => null,
			),
			'youtube' => array(
				'regex' => '~(?:https?://)?(?:www\.)?(?:(?:youtu\.be/)|(?:youtube\.com)/(?:v/|u/\w/|embed/|watch)?)(?:(?:\?v=)?([^#&?=]*))?((?:[?&]\w*=\w*)*)~',
				'embedUrl' => 'https://www.youtube.com/embed/{{ remoteId }}',
				'html' => '<iframe src="{{ embedUrl }}" style="width: 100%; aspect-ratio: 16/9;"></iframe>',
				'id' => fn (array $groups): string => $groups[0] ?? '',
			),
		);
	}
}
