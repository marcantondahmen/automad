<?php

namespace Automad\UI\Models;

use Automad\Test\Mock;
use Automad\UI\Models\Search\FieldResultsModel;
use Automad\UI\Models\Search\FileResultsModel;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\UI\Models\SearchModel
 */
class SearchTest extends TestCase {
	public function dataForTestSearchPerFileIsSame() {
		return array(
			array(
				'simple',
				false,
				false,
				array(
					new FileResultsModel(
						'/pages/01.text/default.txt',
						array(
							new FieldResultsModel(
								'text',
								array('simple'),
								'A <mark>simple</mark> sample text with a [link](/url/to/page)'
							)
						),
						'/text'
					),
					new FileResultsModel(
						'/pages/01.blocks/default.txt',
						array(
							new FieldResultsModel(
								'+main',
								array('Simple', 'simple', 'simple'),
								'A <mark>Simple</mark> First Column Table Header ... A <mark>simple</mark> paragraph text ... Another <mark>simple</mark> item'
							)
						),
						'/blocks'
					)
				)
			),
			array(
				'Simple',
				false,
				true,
				array(
					new FileResultsModel(
						'/pages/01.blocks/default.txt',
						array(
							new FieldResultsModel(
								'+main',
								array('Simple'),
								'A <mark>Simple</mark> First Column Table Header'
							)
						),
						'/blocks'
					)
				)
			),
			array(
				'si.*?fi.st co.umn .able',
				true,
				false,
				array(
					new FileResultsModel(
						'/pages/01.blocks/default.txt',
						array(
							new FieldResultsModel(
								'+main',
								array('Simple First Column Table'),
								'A <mark>Simple First Column Table</mark> Header'
							)
						),
						'/blocks'
					)
				)
			),
			array(
				'default.*content',
				true,
				false,
				array(
					new FileResultsModel(
						'/shared/data.txt',
						array(
							new FieldResultsModel(
								'shared',
								array('default text content'),
								'Shared <mark>default text content</mark>'
							)
						),
						false
					)
				)
			),
			array(
				'left', // Test ignoring blacklisted properties
				true,
				true,
				array()
			),
			array(
				'table.(row|header)',
				true,
				false,
				array(
					new FileResultsModel(
						'/pages/01.blocks/default.txt',
						array(
							new FieldResultsModel(
								'+main',
								array(
									'Table Header',
									'Table Header',
									'table row',
									'table row'
								),
								'A Simple First Column <mark>Table Header</mark> ... Second Column <mark>Table Header</mark> ... First <mark>table row</mark> and column ... First <mark>table row</mark> and second column'
							)
						),
						'/blocks'
					)
				)
			),
			array(
				'third level',
				false,
				false,
				array(
					new FileResultsModel(
						'/pages/01.blocks/default.txt',
						array(
							new FieldResultsModel(
								'+main',
								array(
									'Third level'
								),
								'<mark>Third level</mark> item'
							)
						),
						'/blocks'
					)
				)
			)
		);
	}

	/**
	 * @dataProvider dataForTestSearchPerFileIsSame
	 * @testdox searchPerFile()
	 * @param mixed $searchValue
	 * @param mixed $isRegex
	 * @param mixed $isCaseSensitive
	 * @param mixed $expected
	 */
	public function testSearchPerFileIsSame($searchValue, $isRegex, $isCaseSensitive, $expected) {
		$Mock = new Mock();
		$SearchModel = new SearchModel(
			$Mock->createAutomad('default'),
			$searchValue,
			$isRegex,
			$isCaseSensitive
		);

		$results = $SearchModel->searchPerFile();

		$this->assertSame(
			json_encode($results, JSON_PRETTY_PRINT),
			json_encode($expected, JSON_PRETTY_PRINT)
		);
	}
}
