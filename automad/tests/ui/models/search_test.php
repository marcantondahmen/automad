<?php

namespace Automad\UI\Models;

use Automad\Tests\Mock;
use Automad\UI\Models\Search\FieldResults;
use Automad\UI\Models\Search\FileResults;
use PHPUnit\Framework\TestCase;


/**
 *	@testdox Automad\UI\Models\Search
 */

class Search_Test extends TestCase {


	/**
	 *	@dataProvider dataForTestSearchPerFileIsSame
	 *	@testdox searchPerFile()
	 */

	public function testSearchPerFileIsSame($searchValue, $isRegex, $isCaseSensitive, $expected) {


		$Mock = new Mock();
		$Search = new Search(
			$Mock->createAutomad('default'),
			$searchValue,
			$isRegex,
			$isCaseSensitive
		);

		$results = $Search->searchPerFile();

		$this->assertSame(
			json_encode($results, JSON_PRETTY_PRINT), 
			json_encode($expected, JSON_PRETTY_PRINT)
		);

	}

	public function dataForTestSearchPerFileIsSame() {

		return array(
			array(
				'simple',
				false,
				false,
				array(
					new FileResults(
						'/pages/01.text/default.txt',
						array(
							new FieldResults(
								'text',
								array('simple'),
								'A <mark>simple</mark> sample text'
							)
						),
						'/text'
					),
					new FileResults(
						'/pages/01.blocks/default.txt',
						array(
							new FieldResults(
								'+main',
								array('Simple', 'simple'),
								'A <mark>Simple</mark> First Column Table ... A <mark>simple</mark> paragraph text'
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
					new FileResults(
						'/pages/01.blocks/default.txt',
						array(
							new FieldResults(
								'+main',
								array('Simple'),
								'A <mark>Simple</mark> First Column Table'
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
					new FileResults(
						'/pages/01.blocks/default.txt',
						array(
							new FieldResults(
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
					new FileResults(
						'/shared/data.txt',
						array(
							new FieldResults(
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
					new FileResults(
						'/pages/01.blocks/default.txt',
						array(
							new FieldResults(
								'+main',
								array(
									'Table Header',
									'Table Header',
									'table row',
									'table row'
								),
								'Simple First Column <mark>Table Header</mark> ... Second Column <mark>Table Header</mark> ... First <mark>table row</mark> and column ... First <mark>table row</mark> and second column'
							)
						),
						'/blocks'
					)
				)
			)
		);

	}

}