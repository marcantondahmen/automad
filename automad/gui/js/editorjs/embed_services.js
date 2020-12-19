/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


+function(editorJS) {

	editorJS.embedServices = {

		youtube: true,
		codepen: true,
		vimeo: true,
		imgur: true,
		instagram: true,

		dailymotion: {
			regex: /https?:\/\/www\.dailymotion\.com\/video\/(\w+)(\?.*?)?$/,
			embedUrl: 'https://www.dailymotion.com/embed/video/<%= remote_id %>/',
			html: '<iframe width="640" height="360" frameborder="0" allowFullScreen></iframe>',
			height: 360,
			width: 640
		},
		
		giphy: {
			regex: /https?:\/\/giphy\.com\/(?:gifs|videos)\/(?:[^\/]*\-)?([a-zA-Z0-9]+)$/,
			embedUrl: 'https://giphy.com/embed/<%= remote_id %>/',
			html: '<iframe width="600" height="480" frameborder="0" allowFullScreen></iframe>',
			height: 480,
			width: 600
		},

		mixcloud: {
			regex: /https?:\/\/www\.mixcloud\.com\/(.+)\/$/,
			embedUrl: 'https://www.mixcloud.com/widget/iframe/?hide_cover=1&feed=/<%= remote_id %>/',
			html: '<iframe height="180" scrolling="no" frameborder="no" allowtransparency="true" allowfullscreen="true" style="width: 100%;"></iframe>',
			height: 180,
			width: 0
		},

		soundcloud: {
			regex: /(https:\/\/soundcloud\.com\/.+)$/,
			embedUrl: 'https://w.soundcloud.com/player/?url=<%= remote_id %>',
			html: '<iframe height="180" scrolling="no" frameborder="no" allowtransparency="true" allowfullscreen="true" style="width: 100%;"></iframe>',
			height: 180,
			width: 0
		},

		twitter: {
			regex: /^https?:\/\/twitter\.com\/(?:#!\/)?(\w+)\/status(?:es)?\/(\d+)(?:\/.*)?$/,
			embedUrl: 'https://twitframe.com/show?url=https://twitter.com/<%= remote_id %>',
			html: '<iframe width="500" height="300" style="display: block; width: 500px; border-bottom: 1px solid #E0E0E0; margin: 0 auto;" frameborder="0" scrolling="no" allowtransparency="true"></iframe>',
			height: 0,
			width: 0,
			id: ids => ids.join('/status/')
		}

	}

}(function(Automad) {

	Automad.editorJS = Automad.editorJS || {};
	return Automad.editorJS;

}(window.Automad = window.Automad || {}));