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

namespace Automad\Ai\Mcp;

use Automad\System\Asset;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The MCP server card. Provides the card data as described in SEP-1649, its JSON representation
 * for MCP clients and a self-contained, human readable HTML representation for browsers.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 *
 * @see https://github.com/modelcontextprotocol/modelcontextprotocol/issues/1649
 */
class Card {
	/**
	 * The system monospace font stack used on the HTML card.
	 */
	private const FONT_STACK = 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace';

	/**
	 * The path of the JSON server card.
	 */
	public const JSON_PATH = '/.well-known/mcp/server-card.json';

	/**
	 * Build the server card data.
	 *
	 * @return array
	 */
	public static function get(): array {
		return array(
			'$schema' => 'https://static.modelcontextprotocol.io/schemas/mcp-server-card/v1.json',
			'version' => '1.0',
			'protocolVersion' => '2025-11-25',
			'serverInfo' => array(
				'name' => Server::getName(),
				'title' => Server::getTitle(),
				'version' => AM_VERSION
			),
			'transport' => array(
				'type' => 'streamable-http',
				'endpoint' => AM_BASE_INDEX . AM_MCP_SERVER_URL
			),
			'capabilities' => array(
				'tools' => new \stdClass()
			),
			'authentication' => array(
				'required' => false,
				'schemes' => array('bearer')
			),
			'tools' => array('dynamic')
		);
	}

	/**
	 * Render the server card as a fully self-contained HTML page for humans.
	 * All CSS is inlined in the head and no external assets are required.
	 *
	 * @return string
	 */
	public static function html(): string {
		$card = self::get();
		$title = self::escape($card['serverInfo']['title']);
		$name = self::escape($card['serverInfo']['name']);
		$version = self::escape($card['serverInfo']['version']);
		$protocol = self::escape($card['protocolVersion']);
		$transport = self::escape($card['transport']['type']);
		$endpoint = self::escape(AM_SERVER . $card['transport']['endpoint']);
		$schemes = self::escape(implode(', ', $card['authentication']['schemes']));
		$jsonUrl = self::escape(AM_SERVER . AM_BASE_INDEX . self::JSON_PATH);
		$font = self::FONT_STACK;

		$cliSnippet = self::escape(
			'claude mcp add --transport http \ ' . "\n  " .
				$card['serverInfo']['name'] . " \ \n  " .
				AM_SERVER . $card['transport']['endpoint'] . " \ \n  " .
				'--header "Authorization: Bearer <token>"'
		);

		$jsonSnippet = self::escape(
			str_replace('    ', '  ', strval(json_encode(
				array(
					'mcpServers' => array(
						$card['serverInfo']['name'] => array(
							'type' => 'http',
							'url' => AM_SERVER . $card['transport']['endpoint'],
							'headers' => array('Authorization' => 'Bearer <token>')
						)
					)
				),
				JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
			)))
		);

		$favicons = Asset::favicons();
		$styles = Asset::css('dist/build/mcp/index.css');

		return <<< HTML
			<!DOCTYPE html>
			<html lang="en">
				<head>
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1">
					<meta name="robots" content="noindex">
					<title>MCP Server | Automad</title>
					$favicons
					$styles
				</head>
				<body>
					<main>
						<h1>MCP Server</h1>
						<div>
							<span class="badge"><span>●</span> $title</span>
						</div>
						<p>
							This is a Model Context Protocol (MCP) endpoint.
							It is meant to be used by an MCP client and not by a web browser.
						</p>

						<hr>
						<h2>Server</h2>
						<dl>
							<dt>name</dt>
							<dd>$name</dd>
							<dt>version</dt>
							<dd>$version</dd>
							<dt>protocol</dt>
							<dd>$protocol</dd>
							<dt>transport</dt>
							<dd>$transport</dd>
							<dt>endpoint</dt>
							<dd>$endpoint</dd>
							<dt>capabilities</dt>
							<dd>tools</dd>
						</dl>

						<hr>
						<h2>Authentication</h2>
						<p>
							Public tools are available without authentication.
							To get access to all tools, send an access token using the $schemes scheme
							in the <code>Authorization</code> header.
							Access tokens can be created in the dashboard.
						</p>

						<hr>
						<h2>Connect</h2>
						<p>Add the server to Claude Code:</p>
						<pre><code>$cliSnippet</code></pre>
						<p>Or add it to the <code>.mcp.json</code> of a project:</p>
						<pre><code>$jsonSnippet</code></pre>

						<hr>
						<h2>Server card</h2>
						<p><a href="$jsonUrl">$jsonUrl</a></p>
					</main>
				</body>
			</html>
			HTML;
	}

	/**
	 * Build the server card as JSON.
	 *
	 * @return string
	 */
	public static function json(): string {
		return strval(json_encode(self::get(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}

	/**
	 * Escape a value for the use in HTML.
	 *
	 * @param string $value
	 * @return string
	 */
	private static function escape(string $value): string {
		return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
	}
}
