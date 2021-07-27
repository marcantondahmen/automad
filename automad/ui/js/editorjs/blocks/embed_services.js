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
 * Copyright (c) 2020-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

+(function (editorJS) {
	editorJS.embedServices = {
		vimeo: {
			regex: /(?:http[s]?:\/\/)?(?:www.)?vimeo\.co(?:.+\/([^\/]\d+)(?:#t=[\d]+)?s?$)/,
			embedUrl:
				'https://player.vimeo.com/video/<%= remote_id %>?title=0&byline=0',
			html: '<iframe style="width:100%;" height="320" frameborder="0"></iframe>',
			height: 9,
			width: 16,
		},

		youtube: {
			regex: /(?:https?:\/\/)?(?:www\.)?(?:(?:youtu\.be\/)|(?:youtube\.com)\/(?:v\/|u\/\w\/|embed\/|watch))(?:(?:\?v=)?([^#&?=]*))?((?:[?&]\w*=\w*)*)/,
			embedUrl: 'https://www.youtube.com/embed/<%= remote_id %>',
			html: '<iframe style="width:100%;" height="320" frameborder="0" allowfullscreen></iframe>',
			height: 9,
			width: 16,
			id: ([id, params]) => {
				if (!params && id) {
					return id;
				}

				const paramsMap = {
					start: 'start',
					end: 'end',
					t: 'start',
					// eslint-disable-next-line camelcase
					time_continue: 'start',
					list: 'list',
				};

				params = params
					.slice(1)
					.split('&')
					.map((param) => {
						const [name, value] = param.split('=');

						if (!id && name === 'v') {
							id = value;

							return;
						}

						if (!paramsMap[name]) {
							return;
						}

						return `${paramsMap[name]}=${value}`;
					})
					.filter((param) => !!param);

				return id + '?' + params.join('&');
			},
		},

		imgur: {
			regex: /https?:\/\/(?:i\.)?imgur\.com.*\/([a-zA-Z0-9]+)(?:\.gifv)?/,
			embedUrl: 'http://imgur.com/<%= remote_id %>/embed',
			html: '<iframe allowfullscreen="true" scrolling="no" id="imgur-embed-iframe-pub-<%= remote_id %>" class="imgur-embed-iframe-pub" style="height: 500px; width: 100%; border: 1px solid #000"></iframe>',
			height: 500,
			width: 540,
		},

		codepen: {
			regex: /https?:\/\/codepen\.io\/([^\/\?\&]*)\/pen\/([^\/\?\&]*)/,
			embedUrl:
				'https://codepen.io/<%= remote_id %>?height=300&theme-id=0&default-tab=css,result&embed-version=2',
			html: "<iframe height='300' scrolling='no' frameborder='no' allowtransparency='true' allowfullscreen='true' style='width: 100%;'></iframe>",
			height: 300,
			width: 600,
			id: (ids) => ids.join('/embed/'),
		},

		instagram: {
			regex: /https?:\/\/www\.instagram\.com\/p\/([^\/\?\&]+)\/?/,
			embedUrl: 'https://www.instagram.com/p/<%= remote_id %>/embed',
			html: '<iframe width="400" height="505" style="margin: 0 auto;" frameborder="0" scrolling="no" allowtransparency="true"></iframe>',
			height: 505,
			width: 400,
		},

		dailymotion: {
			regex: /https?:\/\/www\.dailymotion\.com\/video\/(\w+)(\?.*?)?$/,
			embedUrl:
				'https://www.dailymotion.com/embed/video/<%= remote_id %>/',
			html: '<iframe width="640" height="360" frameborder="0" allowFullScreen></iframe>',
			height: 360,
			width: 640,
		},

		giphy: {
			regex: /https?:\/\/giphy\.com\/(?:gifs|videos)\/(?:[^\/]*\-)?([a-zA-Z0-9]+)$/,
			embedUrl: 'https://giphy.com/embed/<%= remote_id %>/',
			html: '<iframe width="600" height="480" frameborder="0" allowFullScreen></iframe>',
			height: 480,
			width: 600,
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
			regex: /^https?:\/\/twitter\.com\/(?:#!\/)?(\w+)\/status(?:es)?\/(\d+)(?:\/.*)?$/,
			embedUrl:
				'https://twitframe.com/show?url=https://twitter.com/<%= remote_id %>',
			html: '<iframe width="500" height="300" style="display: block; width: 500px; border-bottom: 1px solid #E0E0E0; margin: 0 auto;" frameborder="0" scrolling="no" allowtransparency="true"></iframe>',
			height: 0,
			width: 0,
			id: (ids) => ids.join('/status/'),
		},
	};
})(
	(function (Automad) {
		Automad.editorJS = Automad.editorJS || {};
		return Automad.editorJS;
	})((window.Automad = window.Automad || {}))
);
