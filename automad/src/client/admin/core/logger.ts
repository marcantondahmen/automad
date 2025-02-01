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

import { KeyValueMap, Logger } from '@/admin/types';
import { App } from '.';

/**
 * The logger factory.
 *
 * @returns the logger instance based on the current environment
 */
export const getLogger = (): Logger => {
	if (DEVELOPMENT) {
		return new DevelopmentLogger();
	}

	return new ProductionLogger();
};

/**
 * Get the calling function from the stack.
 *
 * @returns the calling function
 */
const getCaller = (): string => {
	const funcRegex = /^\s+at\s(?<func>\S+)\s\([^\)]+\)$/gm;
	const stack = [];

	for (const match of new Error('').stack.matchAll(funcRegex)) {
		const func = match.groups.func;

		if (!func.match(/(Generator\.next|__awaiter|Object\.|Array\.)/)) {
			stack.push(func);
		}
	}

	return stack[3] || '';
};

/**
 * Compose an array of log items.
 *
 * @param args
 * @returns an array of items that should be logged
 */
const compose = (...args: any[]): any[] => {
	return [`%c[${getCaller()}]`, 'font-weight: bold;', ...args];
};

/**
 * A styled log entry.
 *
 * @param label
 * @param data
 * @param color
 * @param bg
 */
const styled = (
	label: string,
	data: KeyValueMap,
	color: string,
	bg: string
): void => {
	const items: (string | KeyValueMap)[] = [
		`%c${label}`,
		`padding: 3px 5px; font-weight: 500; color: ${color}; background-color: ${bg}`,
	];

	if (data !== null) {
		items.push(data);
	}

	console.log(...items);
};

/**
 * Create a fetch console entry.
 *
 * @param url
 * @param data
 * @param [prefix]
 * @param [color]
 * @param [bg]
 */
const fetch = (
	url: string,
	data: KeyValueMap,
	prefix: string = '',
	color: string = '',
	bg: string = ''
): void => {
	const route = url.split(`${App.apiURL}/`).pop();

	styled(`${prefix} ${route}`, data, color, bg);
};

/**
 * The logger class being used during development.
 */
class DevelopmentLogger implements Logger {
	error(...args: any[]): void {
		console.error(...compose(...args));
	}

	log(...args: any[]): void {
		console.log(...compose(...args));
	}

	request(url: string, data: KeyValueMap): void {
		fetch(url, data, '>>', '#b9b0eb', '#503bc4');
	}

	response(url: string, data: KeyValueMap): void {
		fetch(url, data, '<<', '#d3b0eb', '#613480');
	}

	bindingInfo(label: string, data: KeyValueMap): void {
		styled(label, data, '#888888', '#333333');
	}

	bindingSuccess(text: string): void {
		styled(text, null, '#71c9a1', '#356e54');
	}
}

/**
 * The logger class being used in production.
 */
class ProductionLogger implements Logger {
	error(...args: any[]): void {
		console.error(...args);
	}

	log(): void {}

	request(): void {}

	response(): void {}

	bindingInfo(): void {}

	bindingSuccess(): void {}
}
