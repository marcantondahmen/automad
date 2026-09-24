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

namespace Automad\Ai\Mcp\Resources;

use Automad\Ai\Mcp\SchemaReference;
use Automad\Blocks\AbstractBlock;
use Automad\System\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page schema resource.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SchemaPage extends AbstractResource {
	/**
	 * @return string
	 */
	public function getDescription(): string {
		return 'A complete Automad page with metadata and template-defined content fields.';
	}

	/**
	 * @return callable
	 */
	public function getHandler(): callable {
		return function () {
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

				$blocks[$type] = array(
					'type' => $type,
					'description' => $blockClass::getDescription(),
					'properties' => array(
						'id' => array('$ref' => SchemaReference::BLOCK_ID),
						...$blockClass::getAgentSchema()
					),
					'required' => $blockClass::getRequiredFromAgentSchema(),
					'additionalProperties' => false
				);

				$blockRefs[] = array('$ref' => '#/$defs/' . $type);
			}

			return array(
				'$schema' => 'https://json-schema.org/draft/2020-12/schema',
				'title' => 'Automad Page',
				'description' => 'A complete Automad page schema with metadata and template-defined content fields.',
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
						'description'=> 'Content for the +... fields provided by the selected template.',
						'additionalProperties'=> array(
							'type'=> 'array',
							'items'=> array(
								'$ref'=> SchemaReference::BLOCK
							)
						)
					)
				),
				'required'=> array('parent', 'title', 'template'),
				'$defs' => array(
					'block' => array(
						'description' => 'An Automad content block.',
						'oneOf' => $blockRefs
					),
					'blockId' => array(
						'type' => 'string',
						'description' => 'The stable ID of an existing block. Omit when creating a new block.'
					),
					'nesdtedListItem'=> array(
						'type'=> 'object',
						'description'=> 'An item in a list. List items can contain nested lists.',
						'properties'=> array(
							'content'=> array(
								'type'=> 'string'
							),
							'items'=> array(
								'$ref'=> SchemaReference::NESTED_LIST_ITEM
							)
						),
						'required'=> array('content'),
						'additionalProperties'=> false
					),
					...$blocks
				)
			);
		};
	}

	/**
	 * The resource's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'schema/page';
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return 'Schema / Page';
	}
}
