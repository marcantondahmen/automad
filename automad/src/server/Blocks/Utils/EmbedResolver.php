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
	 * Resolve a source URL against the supported embed services and return its embed url, width, height and preview markup.
	 *
	 * A service is only considered a match when a non-empty remote id can
	 * be extracted - a url that is merely shaped like a given service's
	 * urls (for example a YouTube url with no video id) is treated as not
	 * matching, so that callers can fall back accordingly.
	 *
	 * @param string $url
	 * @return array{service: string, url: string, width: int, height: int, html: string}|null
	 */
	public static function getEmbedData(string $url): ?array {
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
			$html = str_replace('{{ source }}', $embedUrl, $definition['html']);

			return array(
				'service' => $service,
				'url' => $embedUrl,
				'width' => $definition['width'],
				'height' => $definition['height'],
				'html' => $html,
			);
		}

		return null;
	}

	/**
	 * Return the supported embed services table.
	 *
	 * @return array<string, array{regex: string, embedUrl: string, width: int, height: int, html: string, id: (callable(string[]): string)|null}>
	 */
	private static function getServices(): array {
		return array(
			'codepen' => array(
				'regex' => '~https?://codepen\.io/([^/?&]*)/pen/([^/?&]*)~',
				'embedUrl' => 'https://codepen.io/{{ remoteId }}?height=300&theme-id=0&default-tab=css,result&embed-version=2',
				'width' => 600,
				'height' => 300,
				'html' => '<iframe src="{{ source }}" height="300" scrolling="no" frameborder="no" allowtransparency="true" allowfullscreen="true" style="width: 100%;"></iframe>',
				'id' => fn (array $groups): string => implode('/embed/', $groups),
			),
			'dailymotion' => array(
				'regex' => '~https?://www\.dailymotion\.com/video/(\w+)(\?.*?)?$~D',
				'embedUrl' => 'https://www.dailymotion.com/embed/video/{{ remoteId }}/',
				'width' => 640,
				'height' => 360,
				'html' => '<iframe src="{{ source }}" width="640" height="360" frameborder="0" allowFullScreen></iframe>',
				'id' => null,
			),
			'facebook' => array(
				'regex' => '~https?://www\.facebook\.com/([^/?&]*)/(.*)~',
				'embedUrl' => 'https://www.facebook.com/plugins/post.php?href=https://www.facebook.com/{{ remoteId }}&width=500',
				'width' => 0,
				'height' => 0,
				'html' => '<iframe src="{{ source }}" scrolling="no" frameborder="no" allowtransparency="true" allowfullscreen="true" style="margin: 0 auto; width: 500px; min-height: 500px; max-height: 1000px;"></iframe>',
				'id' => fn (array $groups): string => implode('/', $groups),
			),
			'giphy' => array(
				'regex' => '~https?://giphy\.com/(?:gifs|videos)/(?:[^/]*\-)?([a-zA-Z0-9]+)$~D',
				'embedUrl' => 'https://giphy.com/embed/{{ remoteId }}/',
				'width' => 600,
				'height' => 480,
				'html' => '<iframe src="{{ source }}" width="600" height="480" frameborder="0" allowFullScreen></iframe>',
				'id' => null,
			),
			'github' => array(
				'regex' => '~https?://gist\.github\.com/([^/?&]*)/([^/?&]*)~',
				'embedUrl' => 'data:text/html;charset=utf-8,<head><base target="_blank" /></head><body><script src="https://gist.github.com/{{ remoteId }}" ></script></body>',
				'width' => 600,
				'height' => 300,
				'html' => '<iframe src="{{ source }}" width="100%" height="350" frameborder="0" style="margin: 0 auto;"></iframe>',
				'id' => fn (array $groups): string => implode('/', $groups) . '.js',
			),
			'imgur' => array(
				'regex' => '~https?://(?:i\.)?imgur\.com(?:/gallery)?/([\w-]+)(?:\.gifv)?~',
				'embedUrl' => 'http://imgur.com/{{ remoteId }}',
				'width' => 540,
				'height' => 500,
				'html' => '<am-embed-service src="{{ source }}" type="imgur"></am-embed-service>',
				'id' => null,
			),
			'instagram' => array(
				'regex' => '~https?://www\.instagram\.com/p/([^/?&]+)/?~',
				'embedUrl' => 'https://www.instagram.com/p/{{ remoteId }}/embed',
				'width' => 400,
				'height' => 505,
				'html' => '<iframe src="{{ source }}" width="400" height="505" style="margin: 0 auto;" frameborder="0" scrolling="no" allowtransparency="true"></iframe>',
				'id' => null,
			),
			'mixcloud' => array(
				'regex' => '~https?://www\.mixcloud\.com/(.+)/$~D',
				'embedUrl' => 'https://www.mixcloud.com/widget/iframe/?hide_cover=1&feed=/{{ remoteId }}/',
				'width' => 0,
				'height' => 180,
				'html' => '<iframe src="{{ source }}" height="180" scrolling="no" frameborder="no" allowtransparency="true" allowfullscreen="true" style="width: 100%;"></iframe>',
				'id' => null,
			),
			'soundcloud' => array(
				'regex' => '~(https://soundcloud\.com/.+)$~D',
				'embedUrl' => 'https://w.soundcloud.com/player/?url={{ remoteId }}',
				'width' => 0,
				'height' => 180,
				'html' => '<iframe src="{{ source }}" height="180" scrolling="no" frameborder="no" allowtransparency="true" allowfullscreen="true" style="width: 100%;"></iframe>',
				'id' => null,
			),
			'twitter' => array(
				'regex' => '~^https?://(?:www\.)?(?:twitter\.com|x\.com)/(.+?)/status/(\d+)(?:\?.*)?$~D',
				'embedUrl' => 'https://twitter.com/{{ remoteId }}',
				'width' => 0,
				'height' => 0,
				'html' => '<am-embed-service src="{{ source }}" type="twitter"></am-embed-service>',
				'id' => fn (array $groups): string => implode('/status/', $groups),
			),
			// height/width are an aspect ratio (16/9), not pixels.
			'vimeo' => array(
				'regex' => '~^https?://(?:www\.)?vimeo\.com/(\d+).*$~D',
				'embedUrl' => 'https://player.vimeo.com/video/{{ remoteId }}?title=0&byline=0',
				'width' => 16,
				'height' => 9,
				'html' => '<iframe src="{{ source }}" style="width:100%; aspect-ratio: 16/9;" frameborder="0"></iframe>',
				'id' => null,
			),
			// height/width are an aspect ratio (16/9), not pixels.
			'youtube' => array(
				'regex' => '~(?:https?://)?(?:www\.)?(?:(?:youtu\.be/)|(?:youtube\.com)/(?:v/|u/\w/|embed/|watch)?)(?:(?:\?v=)?([^#&?=]*))?((?:[?&]\w*=\w*)*)~',
				'embedUrl' => 'https://www.youtube.com/embed/{{ remoteId }}',
				'width' => 16,
				'height' => 9,
				'html' => '<iframe src="{{ source }}" style="width: 100%; aspect-ratio: 16/9;" frameborder="0" allowfullscreen></iframe>',
				'id' => fn (array $groups): string => $groups[0] ?? '',
			),
		);
	}
}
