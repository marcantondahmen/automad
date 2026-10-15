<?php

namespace Automad\Ai\Mcp\Transformer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PageIdTransformerTest extends TestCase {
	public static function dataForTestFromInput() {
		return array(
			'empty' => array('', ''),
			'homepage id' => array('/', '/'),
			'page id' => array('/work/project', '/work/project'),
			'homepage url' => array('https://domain.com', '/'),
			'homepage url with trailing slash' => array('https://domain.com/', '/'),
			'page url' => array('https://domain.com/work/project', '/work/project'),
			'page url with port, trailing slash, query and fragment' => array('http://domain.com:8000/work/project/?key=value#top', '/work/project'),
			'page url with base index' => array('https://domain.com' . AM_BASE_INDEX . '/work/project', '/work/project')
		);
	}

	#[DataProvider('dataForTestFromInput')]
	public function testFromInput(string $input, string $expected) {
		/** @disregard */
		$this->assertSame($expected, (new PageIdTransformer())->fromInput($input));
	}
}
