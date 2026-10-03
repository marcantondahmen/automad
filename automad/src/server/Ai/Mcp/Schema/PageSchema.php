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
				'template' => array(
					'type' => 'string',
					'description' => 'The template used by the page. Use the `page_template_list` tool to get a list of available templates.'
				),
				'theme' => array(
					'type' => 'string',
					'description' => 'The theme that belongs to the selected `template` used by the page.'
				),
				'date' => array(
					'type' => 'string',
					'format' => 'date-time',
					'description' => 'The page date in the ISO 8601 date-time format like for example `2026-10-02T08:03:25+00:00`.'
				),
				'private' => array(
					'type' => 'boolean',
					'description' => 'Whether the page should be kept private and not accessible without authentication.'
				),
				'hidden' => array(
					'type' => 'boolean',
					'description' => 'Whether the page should be hidden from page lists and navigation.'
				),
				'tags' => array(
					'type' => 'array',
					'description'=> 'Optional tags assigned to the page.',
					'items'=> array(
						'type'=> 'string'
					)
				),
				'content' => array(
					'type' => 'object',
					'description'=> 'Content for the +... fields (+main, +hero, etc.) provided by the selected template. Critical: all content fields start with a `+` symbol! Do not remove that `+` symbol!',
					'propertyNames' => array(
						'pattern' => '^\\+.+$'
					),
					'additionalProperties' => array(
						'type' => 'array',
						'items' => array(
							'$ref' => PageSchema::BLOCK
						)
					)
				)
			),
			'required' => array('parent', 'title', 'template', 'theme'),
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
			'description' => 'The Automad page deletion input schema.',
			'type' => 'object',
			'properties' => array(
				'id' => array(
					'type' => 'string',
					'description' => <<< TXT
						The ID is the local absolute URL path of the page like 
						for example `/about` or `/work/project`.
						TXT
				)
			),
			'required' => array('id')
		);
	}

	/**
	 * The page duplication schema.
	 *
	 * @return array
	 */
	public static function duplicate(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Page Duplication Schema',
			'description' => 'The Automad page duplication input schema.',
			'type' => 'object',
			'properties' => array(
				'id' => array(
					'type' => 'string',
					'description' => <<< TXT
						The ID is the page that will be duplicated.
						TXT
				)
			),
			'required' => array('id')
		);
	}

	/**
	 * The page move schema.
	 *
	 * @return array
	 */
	public static function move(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Page Move Schema',
			'description' => 'An input schema for moving a page to another location on an Automad website.',
			'type' => 'object',
			'properties' => array(
				'id' => array(
					'type' => 'string',
					'description' => <<< TXT
						The `id` of the page that will be moved.
						TXT
				),
				'target' => array(
					'type' => 'string',
					'description' => <<< TXT
						The target page `id` where the page will be moved to.
						TXT
				)
			),
			'required' => array('id', 'target')
		);
	}

	/**
	 * The page publish schema.
	 *
	 * @return array
	 */
	public static function publish(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Page Publication Schema',
			'description' => 'The Automad page publication input schema.',
			'type' => 'object',
			'properties' => array(
				'id' => array(
					'type' => 'string',
					'description' => <<< TXT
						The ID is the page that will be published.
						TXT
				)
			),
			'required' => array('id')
		);
	}

	/**
	 * The page read schema.
	 *
	 * @return array
	 */
	public static function read(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Page Read Schema',
			'description' => <<< TXT
				An input schema for reading a single page on an Automad website.

				The `id` is required. 
			
				Whenever this tool is used to retrieve page content for the `page_update`
				tool, the `format` property must be `structured`.
				TXT,
			'type' => 'object',
			'properties' => array(
				'id' => array(
					'type' => 'string',
					'description' => <<< TXT
						The ID is the local absolute URL path of the page such as `/about` or `/work/project` for example.
						TXT
				),
				'format' => array(
					'type' => 'string',
					'description' => <<< TXT
						The output format.  

						`text` returns a lossy plain-text rendering of each content field, without block IDs or types. 
						Use it for reading and summarizing only; it must not be used as input for `page_update`.
					
						To edit a page, read it with `structured` and pass that data to `page_update`.
						TXT,
					'enum' => array('structured', 'text'),
					'default' => 'structured'
				)
			),
			'required' => array('id')
		);
	}

	/**
	 * The page reorder schema.
	 *
	 * @return array
	 */
	public static function reorderChildren(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Page Reorder Schema',
			'description' => 'An input schema for reordering sub-pages for a given parent page by an ordered list of page IDs.',
			'type' => 'object',
			'properties' => array(
				'parent' => array(
					'type' => 'string',
					'description' => <<< TXT
						The `id` of the parent page where sub-pages will be reordered.
						TXT
				),
				'order' => array(
					'type' => 'array',
					'items' => array(
						'type' => 'string'
					),
					'description' => <<< TXT
						The reordered list of page ID basenames.
						Critical: page IDs are basically absolute URLs. 
						The ordered list must only contain page ID basenames (without any slashes).
						A page with the ID `/work/project-1` becomes just `project-1`. 
						TXT
				)
			),
			'required' => array('parent', 'order')
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
			'title' => 'Automad Page Search Schema',
			'description' => 'An input schema for searching an Automad website.',
			'type' => 'object',
			'properties' => array(
				'search' => array(
					'type' => 'string',
					'description' => <<< TXT
						The search string. Can be multiple words. 
						More words will narrow down the search results.
						TXT
				),
				'scopeId' => array(
					'type' => 'string',
					'description' => <<< TXT
						An optional scope for the search.
						When used, results will only include pages below the given scope.
						TXT
				)
			),
			'required' => array('search')
		);
	}

	/**
	 * The page tree schema.
	 *
	 * @return array
	 */
	public static function tree(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Page Tree Schema',
			'description' => <<< TXT
				An input schema for retrieving the full or partial hierarchical structure of an Automad website.

				The `id` property can be optionally used to start the tree at a specific page instead of the 
				homepage ("/"). Any existing page `id` can be used here.

				The `depth` property can be used to limit the returned tree to a given depth.
				TXT,
			'type' => 'object',
			'properties' => array(
				'id' => array(
					'type' => 'string',
					'description' => <<< TXT
						The ID is the local absolute URL path of the page such as `/about` or `/work/project` 
						that is used as a starting point for the tree. The returned tree will only include pages
						below the given page.

						The default is '/' (the homepage).
						TXT
				),
				'depth' => array(
					'type' => 'integer',
					'description' => <<< TXT
						The depth of the returned tree relative to its starting level.
						TXT
				)
			)
		);
	}

	/**
	 * Generate the input schema for updating a page.
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
				'title' => array(
					'anyOf' => array(
						array('type' => 'string'),
						array('type' => 'null')
					),
					'description' => 'The title of the page. Skip it in order to keep the current title.'
				),
				'template' => array(
					'anyOf' => array(
						array('type' => 'string'),
						array('type' => 'null')
					),
					'description' => 'The template used by the page. Use the `page_template_list` tool to get a list of available templates. Skip it in order to keep the current template.'
				),
				'theme' => array(
					'anyOf' => array(
						array('type' => 'string'),
						array('type' => 'null')
					),
					'description' => 'The theme that belongs to the selected `template` used by the page. Skip it in order to keep the current theme.'
				),
				'date' => array(
					'anyOf' => array(
						array('type' => 'string', 'format' => 'date-time'),
						array('type' => 'string', 'enum' => array('')),
						array('type' => 'null')
					),
					'description' => 'The page date in the ISO 8601 date-time format like for example `2026-10-02T08:03:25+00:00`. Skip it in order to keep the current date or use an empty string in order to remove the date.'
				),
				'private' => array(
					'anyOf' => array(
						array('type' => 'boolean'),
						array('type' => 'null')
					),
					'description' => 'Whether the page should be kept private and not accessible without authentication. Skip it in order to keep the current state.'
				),
				'hidden' => array(
					'anyOf' => array(
						array('type' => 'boolean'),
						array('type' => 'null')
					),
					'description' => 'Whether the page should be hidden from page lists and navigation. Skip it in order to keep the current state.'
				),
				'tags' => array(
					'anyOf' => array(
						array(
							'type' => 'array',
							'items' => array(
								'type' => 'string'
							)
						),
						array('type' => 'null')
					),
					'description' => 'Optional tags assigned to the page. Skip it in order to keep the current tags or use an empty array in order to remove all tags.'
				),
				'content' => array(
					'anyOf' => array(
						array(
							'type' => 'object',
							'propertyNames' => array(
								'pattern' => '^\\+.+$'
							),
							'additionalProperties' => array(
								'type' => 'array',
								'items' => array(
									'$ref' => PageSchema::BLOCK
								)
							)
						),
						array('type' => 'null')
					),
					'description' => <<< TXT
						Content for the +... fields (+main, +hero, etc.) provided by the selected template.
						Critical: all content fields start with a `+` symbol! Do not remove that `+` symbol!
						Fields that are not part of `content` are kept as they are. Use an empty array
						in order to remove all blocks of a field.
						TXT
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
			// The data property is only required when it contains required fields.
			// Blocks where all fields are optional can be sent without any data.
			$required = array('type', ...(empty($blockSchema['data']['required']) ? array() : array('data')));

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
