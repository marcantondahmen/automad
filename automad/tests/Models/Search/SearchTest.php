<?php

namespace Automad\Models\Search;

use Automad\Test\Mock;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Models\Search\Search
 */
class SearchTest extends TestCase {
	public function dataForTestSearchPerFileIsSame() {
		return array(
			array(
				'simple',
				false,
				false,
				array(
					new FileResults(
						array(
							new FieldResults(
								'text',
								array('simple'),
								'A <mark>simple</mark> sample text with a [link](/url/to/page)'
							)
						),
						'/pages/text/',
						'/text'
					),
					new FileResults(
						array(
							new FieldResults(
								'+main',
								array('Simple', 'simple', 'simple'),
								'A <mark>Simple</mark> First Column Table Header ... A <mark>simple</mark> paragraph text ... Another <mark>simple</mark> item'
							)
						),
						'/pages/blocks-slug/',
						'/blocks'
					)
				)
			),
			array(
				'Simple',
				false,
				true,
				array(
					new FileResults(
						array(
							new FieldResults(
								'+main',
								array('Simple'),
								'A <mark>Simple</mark> First Column Table Header'
							)
						),
						'/pages/blocks-slug/',
						'/blocks'
					)
				)
			),
			array(
				'si.*?fi.st co.umn .able',
				true,
				false,
				array(
					new FileResults(
						array(
							new FieldResults(
								'+main',
								array('Simple First Column Table'),
								'A <mark>Simple First Column Table</mark> Header'
							)
						),
						'/pages/blocks-slug/',
						'/blocks'
					)
				)
			),
			array(
				'default.*content',
				true,
				false,
				array(
					new FileResults(
						array(
							new FieldResults(
								'shared',
								array('default text content'),
								'Shared <mark>default text content</mark>'
							)
						),
						null,
						null,
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
					new FileResults(
						array(
							new FieldResults(
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
						'/pages/blocks-slug/',
						'/blocks'
					)
				)
			),
			array(
				'third level',
				false,
				false,
				array(
					new FileResults(
						array(
							new FieldResults(
								'+main',
								array(
									'Third level'
								),
								'<mark>Third level</mark> item'
							)
						),
						'/pages/blocks-slug/',
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
		$Automad = $Mock->createAutomad('default');
		$Search = new Search(
			$searchValue,
			$isRegex,
			$isCaseSensitive,
			$Automad->getCollection(),
			$Automad->Shared
		);

		$results = $Search->searchPerFile();

		$this->assertSame(
			json_encode($expected, JSON_PRETTY_PRINT),
			json_encode($results, JSON_PRETTY_PRINT)
		);
	}
}
