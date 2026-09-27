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

namespace Automad\Ai\Mcp\Schema;

use Automad\Blocks\AbstractBlock;
use Automad\System\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page schema.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageSchema {
	const string BLOCK = '#/$defs/block';
	const string BLOCK_ID = '#/$defs/blockId';
	const string NESTED_LIST_ITEM = '#/$defs/nestedListItem';

	/**
	 * Generate the input schema for creating a page.
	 *
	 * @return array
	 */
	public static function create(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Page Creation Schema',
			'description' => 'A complete Automad page creation schema with metadata and template-defined content fields.',
			'type' => 'object',
			'properties' => array(
				'parent' => array(
					'type' => 'string',
					'description' => 'The path of the parent page.'
				),
				'title' =>array(
					'type' => 'string',
					'description' => 'The title of the page.'
				),
				'template'=> array(
					'type' => 'string',
					'description' => 'The template used by the page.'
				),
				'tags'=> array(
					'type'=> 'array',
					'description'=> 'Optional tags assigned to the page.',
					'items'=> array(
						'type'=> 'string'
					)
				),
				'fields' => array(
					'type'=> 'object',
					'description'=> 'Content for the +... fields (+main, +hero, etc.) provided by the selected template. Critical: all content fields start with a `+` symbol! Do not remove that `+` symbol!',
					'additionalProperties'=> array(
						'type'=> 'array',
						'items'=> array(
							'$ref'=> PageSchema::BLOCK
						)
					)
				)
			),
			'required'=> array('parent', 'title', 'template'),
			...self::blockDefs()
		);
	}

	/**
	 * The page deletion schema.
	 *
	 * @return array
	 */
	public static function delete(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Page Deletion Schema',
			'description' => 'A complete Automad page deletion schema',
			'type' => 'object',
			'properties' => array(
				'id' => array(
					'type' => 'string',
					'description' => <<< TXT
						The ID is the local absolute URL path of the page like 
						for example `/about` or `/work/project`.
						TXT
				)
			)
		);
	}

	/**
	 * The page search schema.
	 *
	 * @return array
	 */
	public static function search(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Search Schema',
			'description' => 'A input schema for searching an Automad website.',
			'type' => 'object',
			'properties' => array(
				'search' => array(
					'type' => 'string',
					'description' => <<< TXT
						The search string. Can be multiple words. 
						More words will narrow down the search results.
						TXT
				)
			)
		);
	}

	/**
	 * Generate the input schema for creating a page.
	 *
	 * @return array
	 */
	public static function update(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Page Update Schema',
			'description' => 'A complete Automad page update schema with metadata and template-defined content fields.',
			'type' => 'object',
			'properties' => array(
				'id' => array(
					'type' => 'string',
					'description' => 'The ID of the page that will be updated.'
				),
				'title' =>array(
					'type' => 'string',
					'description' => 'The title of the page.'
				),
				'template'=> array(
					'type' => 'string',
					'description' => 'The template used by the page.'
				),
				'tags'=> array(
					'type'=> 'array',
					'description'=> 'Optional tags assigned to the page.',
					'items'=> array(
						'type'=> 'string'
					)
				),
				'fields' => array(
					'type'=> 'object',
					'description'=> 'Content for the +... fields (+main, +hero, etc.) provided by the selected template. Critical: all content fields start with a `+` symbol! Do not remove that `+` symbol!',
					'additionalProperties'=> array(
						'type'=> 'array',
						'items'=> array(
							'$ref'=> PageSchema::BLOCK
						)
					)
				)
			),
			'required'=> array('id'),
			...self::blockDefs()
		);
	}

	/**
	 * The blocks schema.
	 *
	 * @return array
	 */
	private static function blockDefs(): array {
		$blockClassFiles = FileSystem::glob(AM_BASE_DIR . '/automad/src/server/Blocks/*.php');

		foreach ($blockClassFiles as $file) {
			require_once $file;
		}

		$blockClasses = array_filter(get_declared_classes(), function ($class) {
			return is_subclass_of($class, AbstractBlock::class);
		});

		$blocks = array();
		$blockRefs = array();

		foreach ($blockClasses as $blockClass) {
			$type = lcfirst(basename(str_replace('\\', '/', $blockClass)));

			$blockSchema = $blockClass::getAgentSchema();
			$required = array('type', ...(empty($blockSchema['data']) ? array() : array('data')));

			$blocks[$type] = array(
				'type' => 'object',
				'description' => $blockClass::getDescription(),
				'properties' => array(
					'id' => array('$ref' => self::BLOCK_ID),
					'type' => array('const' => $type),
					...$blockClass::getAgentSchema()
				),
				'required' => $required,
				'additionalProperties' => false
			);

			$blockRefs[] = array('$ref' => '#/$defs/' . $type);
		}

		return array(
			'$defs' => array(
				'block' => array(
					'description' => 'An Automad content block.',
					'oneOf' => $blockRefs
				),
				'blockId' => array(
					'type' => 'string',
					'description' => 'The stable ID of an existing block. Omit when creating a new block.'
				),
				'nestedListItem'=> array(
					'type'=> 'object',
					'description'=> 'An item in a list. List items can contain nested lists.',
					'properties'=> array(
						'content'=> array(
							'type'=> 'string'
						),
						'items'=> array(
							'type'=> 'array',
							'items'=> array(
								'$ref'=> self::NESTED_LIST_ITEM
							)
						)
					),
					'required'=> array('content'),
					'additionalProperties'=> false
				),
				...$blocks
			)
		);
	}
}
