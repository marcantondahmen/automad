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

		$this->assertSame(serialize($results), serialize($expected));

	}

	public function dataForTestSearchPerFileIsSame() {

		return array(
			array(
				'find',
				false,
				false,
				array(
					new FileResults(
						'/pages/01.text-search/default.txt',
						array(
							new FieldResults(
								'text',
								array('find'),
								'<mark>find</mark> this lower case str'
							)
						),
						'/text-search'
					),
					new FileResults(
						'/pages/01.block-search/default.txt',
						array(
							new FieldResults(
								'+main',
								array('Find', 'Find'),
								'<mark>Find</mark> this String ... <mark>Find</mark> this String'
							)
						),
						'/block-search'
					)
				)
			),
			array(
				'find',
				false,
				true,
				array(
					new FileResults(
						'/pages/01.text-search/default.txt',
						array(
							new FieldResults(
								'text',
								array('find'),
								'<mark>find</mark> this lower case str'
							)
						),
						'/text-search'
					)
				)
			),
			array(
				'this\s*lo.er.*?case',
				true,
				false,
				array(
					new FileResults(
						'/pages/01.text-search/default.txt',
						array(
							new FieldResults(
								'text',
								array('this lower case'),
								'find <mark>this lower case</mark> string'
							)
						),
						'/text-search'
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
				'text-search',
				false,
				true,
				array()
			),
			array(
				'left',
				true,
				true,
				array()
			)
		);

	}

}