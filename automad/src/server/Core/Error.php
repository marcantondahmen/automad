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
 * Copyright (c) 2024-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Core;

use Automad\API\Response;
use ErrorException;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * Display error messages.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2024-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Error {
	/**
	 * Set error handlers that output styled error messages on HTML pages.
	 */
	public static function setHtmlOutputHandler(): void {
		self::setErrorHandler();

		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		set_exception_handler(
			function ($error) {
				if ($error instanceof AbortSignal) {
					http_response_code($error->getCode());

					self::printError($error->getMessage(), $error->getDetails());
				} else {
					http_response_code(500);

					$file = preg_replace('#^' . AM_BASE_DIR . '#i', '', $error->getFile());
					$line = $error->getLine();

					self::printError(
						$error->getMessage(),
						"In file $file<br>on line $line",
						$error->getTrace()
					);
				}

				exit(1);
			}
		);
	}

	/**
	 * Set error handlers that output a JSON response including an exception object.
	 */
	public static function setJsonResponseHandler(): void {
		self::setErrorHandler();

		set_exception_handler(
			function ($error) {
				$Response = new Response();

				if ($error instanceof AbortSignal) {
					$Response->setError($error->getMessage())->setCode($error->getCode());
				} else {
					$file = preg_replace('#^' . AM_BASE_DIR . '#i', '', $error->getFile());
					$line = $error->getLine();

					$Response->setException(array(
						'message' => $error->getMessage(),
						'file' => $error->getFile(),
						'line' => $error->getLine(),
						'trace' => $error->getTrace()
					))->setCode(500);
				}

				exit($Response->json());
			}
		);
	}

	/**
	 * Print a stlyed error message.
	 *
	 * @param string $message
	 * @param string $details
	 * @param array|null $trace
	 */
	private static function printError(string $message, string $details, ?array $trace = null): void {
		$code = '';

		if ($trace) {
			$code = json_encode($trace, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			$code = "<p>Stack trace:</p><pre><code>$code</code></pre>";
		}

		echo <<< HTML
			<!DOCTYPE html>
			<html>
				<head>
					<title>Error: $message</title>
					<style>
						:root {
							--color: hsl(6, 96%, 60%);
							--bg: hsl(7, 74%, 5%);
							--bg-code: hsl(7, 74%, 8%);

							color-scheme: dark;
						}
						
						html {
							margin: 0;
							padding: 0;
						}

						body {
							display: flex;
							justify-content: center;
							padding: 60px 40px;
							margin: 0;
							font-family: system-ui, sans-serif;
							overflow-x: hidden;
							color: var(--color);
							background-color: var(--bg);

							&:after {
								content: '';
								position: fixed;
								inset: 0 0 auto 0;
								border-top: 4px solid;
							}
						}

						main {
							width: 100%;
							max-width: 920px;
						}

						h1 {
							margin: 0 0 30px 0;
							font-size: 46px;
							font-weight: bold;
							line-height: 1.15;
							text-wrap: balance;
							overflow-wrap: break-word;
						}

						p {
							font-size: 20px;
							line-height: 1.45;
							margin: 0 0 20px 0;
							overflow-wrap: break-word;
						}

						pre {
							padding: 20px;
							background-color: var(--bg-code);
							font-size: 16px;
						}

						code {
							font-family: ui-monospace, 'Cascadia Code', 'Source Code Pro', Menlo, Consolas, 'DejaVu Sans Mono', monospace;
							font-size: inherit;
							white-space: pre-wrap;
							overflow-wrap: break-word;
						}

						p code {
							padding: 5px 10px;
							background-color: var(--bg-code);
							font-size: 0.9em;
						}

						a {
							color: inherit;
						}
					</style>
				</head>
				<body>
					<main>
						<h1>$message</h1>
						<p>$details</p>
						$code
					</main>
				</body>
			</html>
			HTML;
	}

	/**
	 * Set up the error handler to throw a new exception on error.
	 */
	private static function setErrorHandler(): void {
		set_error_handler(function (int $serverity, string $message, string $file, int $line) {
			if (
				$serverity != E_DEPRECATED &&
				$serverity != E_USER_DEPRECATED &&
				$serverity != E_USER_WARNING &&
				$serverity != E_WARNING &&
				$serverity != E_NOTICE
			) {
				throw new ErrorException($message, 0, $serverity, $file, $line);
			}

			$levels = array(
				E_DEPRECATED => '🟠 [DEPRECATED]',
				E_USER_DEPRECATED => '🟠 [USER_DEPRECATED]',
				E_WARNING => '🔴 [WARNING]',
				E_USER_WARNING => '🔴 [USER_WARNING]',
				E_NOTICE => '🟡 [NOTICE]'
			);

			Debug::warn("$message in $file line $line", $levels[$serverity]);
		});
	}
}
