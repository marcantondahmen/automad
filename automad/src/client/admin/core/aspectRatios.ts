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

import { AspectRatioBreakpoints } from '@/admin/types';

/**
 * Convert a breakpoints object into the input formatted string.
 *
 * @param breakpoints
 * @return the formatted string
 */
export const aspectRatioBreakpointsToString = (
	breakpoints: AspectRatioBreakpoints
): string => {
	return Object.keys(breakpoints).reduce((out: string, maxWidth: string) => {
		return `${out} ${maxWidth}:${breakpoints[maxWidth].aspectRatio}`.trim();
	}, '');
};

/**
 * Convert a formatted input string into a breakpoints object.
 *
 * @param breakpointsString
 * @return the breakpoints object
 */
export const aspectRatioBreakpointsFromString = (
	breakpointsString: string
): AspectRatioBreakpoints => {
	const breakpoints: AspectRatioBreakpoints = {};

	breakpointsString.split(' ').forEach((pair: string) => {
		const [maxWidth, aspectRatio] = pair.split(':');

		if (!maxWidth || !aspectRatio) {
			return;
		}

		if (!maxWidth.match(/^\d+$/)) {
			return;
		}

		if (!aspectRatio.match(/^\d+(\.\d+)?\/\d+(\.\d+)?$/)) {
			return;
		}

		breakpoints[maxWidth] = { aspectRatio };
	});

	return breakpoints;
};
