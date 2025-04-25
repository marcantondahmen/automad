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
 */

/**
 * @see {@link github https://github.com/editor-js/embed/blob/master/src/services.js}
 */
export const embedServices = {
	codepen: {
		regex: /https?:\/\/codepen\.io\/([^\/\?\&]*)\/pen\/([^\/\?\&]*)/,
		embedUrl:
			'https://codepen.io/<%= remote_id %>?height=300&theme-id=0&default-tab=css,result&embed-version=2',
		html: "<iframe height='300' scrolling='no' frameborder='no' allowtransparency='true' allowfullscreen='true' style='width: 100%;'></iframe>",
		height: 300,
		width: 600,
		id: (ids: string[]) => ids.join('/embed/'),
	},
	dailymotion: {
		regex: /https?:\/\/www\.dailymotion\.com\/video\/(\w+)(\?.*?)?$/,
		embedUrl: 'https://www.dailymotion.com/embed/video/<%= remote_id %>/',
		html: '<iframe width="640" height="360" frameborder="0" allowFullScreen></iframe>',
		height: 360,
		width: 640,
	},
	facebook: {
		regex: /https?:\/\/www.facebook.com\/([^\/\?\&]*)\/(.*)/,
		embedUrl:
			'https://www.facebook.com/plugins/post.php?href=https://www.facebook.com/<%= remote_id %>&width=500',
		html: "<iframe scrolling='no' frameborder='no' allowtransparency='true' allowfullscreen='true' style='margin: 0 auto; width: 500px; min-height: 500px; max-height: 1000px;'></iframe>",
		id: (ids: string[]) => {
			return ids.join('/');
		},
	},
	giphy: {
		regex: /https?:\/\/giphy\.com\/(?:gifs|videos)\/(?:[^\/]*\-)?([a-zA-Z0-9]+)$/,
		embedUrl: 'https://giphy.com/embed/<%= remote_id %>/',
		html: '<iframe width="600" height="480" frameborder="0" allowFullScreen></iframe>',
		height: 480,
		width: 600,
	},
	github: {
		regex: /https?:\/\/gist.github.com\/([^\/\?\&]*)\/([^\/\?\&]*)/,
		embedUrl:
			'data:text/html;charset=utf-8,<head><base target="_blank" /></head><body><script src="https://gist.github.com/<%= remote_id %>" ></script></body>',
		html: '<iframe width="100%" height="350" frameborder="0" style="margin: 0 auto;"></iframe>',
		height: 300,
		width: 600,
		id: (groups: string[]) => `${groups.join('/')}.js`,
	},
	imgur: {
		regex: /https?:\/\/(?:i\.)?imgur\.com(?:\/gallery)?\/([\w-]+)(?:\.gifv)?/,
		embedUrl: 'http://imgur.com/<%= remote_id %>',
		html: '<am-embed-service type="imgur"></am-embed-service>',
		height: 500,
		width: 540,
	},
	instagram: {
		regex: /https?:\/\/www\.instagram\.com\/p\/([^\/\?\&]+)\/?/,
		embedUrl: 'https://www.instagram.com/p/<%= remote_id %>/embed',
		html: '<iframe width="400" height="505" style="margin: 0 auto;" frameborder="0" scrolling="no" allowtransparency="true"></iframe>',
		height: 505,
		width: 400,
	},
	mixcloud: {
		regex: /https?:\/\/www\.mixcloud\.com\/(.+)\/$/,
		embedUrl:
			'https://www.mixcloud.com/widget/iframe/?hide_cover=1&feed=/<%= remote_id %>/',
		html: '<iframe height="180" scrolling="no" frameborder="no" allowtransparency="true" allowfullscreen="true" style="width: 100%;"></iframe>',
		height: 180,
		width: 0,
	},
	soundcloud: {
		regex: /(https:\/\/soundcloud\.com\/.+)$/,
		embedUrl: 'https://w.soundcloud.com/player/?url=<%= remote_id %>',
		html: '<iframe height="180" scrolling="no" frameborder="no" allowtransparency="true" allowfullscreen="true" style="width: 100%;"></iframe>',
		height: 180,
		width: 0,
	},
	twitter: {
		regex: /^https?:\/\/(?:www\.)?(?:twitter\.com|x\.com)\/(.+?)\/status\/(\d+)(?:\?.*)?$/,
		embedUrl: 'https://twitter.com/<%= remote_id %>',
		html: '<am-embed-service type="twitter"></am-embed-service>',
		height: 0,
		width: 0,
		id: (groups: string[]) => groups.join('/status/'),
	},
	vimeo: {
		regex: /^https?:\/\/(?:www\.)?vimeo\.com\/(\d+).*$/,
		embedUrl:
			'https://player.vimeo.com/video/<%= remote_id %>?title=0&byline=0',
		html: '<iframe style="width:100%; aspect-ratio: 16/9;" frameborder="0"></iframe>',
		height: 9,
		width: 16,
	},
	youtube: {
		regex: /(?:https?:\/\/)?(?:www\.)?(?:(?:youtu\.be\/)|(?:youtube\.com)\/(?:v\/|u\/\w\/|embed\/|watch))(?:(?:\?v=)?([^#&?=]*))?((?:[?&]\w*=\w*)*)/,
		embedUrl: 'https://www.youtube.com/embed/<%= remote_id %>',
		html: '<iframe style="width: 100%; aspect-ratio: 16/9;" frameborder="0" allowfullscreen></iframe>',
		height: 9,
		width: 16,
		id: ([id, params]: [string, any]) => {
			if (!params && id) {
				return id;
			}

			const paramsMap = {
				start: 'start',
				end: 'end',
				t: 'start',
				time_continue: 'start',
				list: 'list',
			} as const;

			params = params
				.slice(1)
				.split('&')
				.map((param: string) => {
					const [name, value] = param.split('=');

					if (!id && name === 'v') {
						id = value;

						return;
					}

					if (!paramsMap[name as keyof typeof paramsMap]) {
						return;
					}

					return `${
						paramsMap[name as keyof typeof paramsMap]
					}=${value}`;
				})
				.filter((param: string[]) => !!param);

			return id + '?' + params.join('&');
		},
	},
} as const;
