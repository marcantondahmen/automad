<?php

namespace Automad\Models\Search;

use Automad\Test\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Models\Search\Search
 */
class SearchTest extends TestCase {
	public static function dataForTestSearchPerFileIsSame() {
		return array(
			array(
				'simple',
				false,
				false,
				<<< JSON
				[
					{
						"count": 1,
						"fieldResultsArray": [
							{
								"context": "A <mark>simple</mark> sample text with a [link](/url/to/page)",
								"field": "text",
								"matches": [
									"simple"
								]
							}
						],
						"path": "/text/",
						"url": "/text"
					},
					{
						"count": 3,
						"fieldResultsArray": [
							{
								"context": "A header block text A <mark>Simple</mark> First Column Table Header Second Column Table ... and column First table row and second column A <mark>simple</mark> paragraph text List item Second list item A ... list Second level Third level item Another <mark>simple</mark> item Some text containing the word me and the",
								"field": "+main",
								"matches": [
									"Simple",
									"simple",
									"simple"
								]
							}
						],
						"path": "/blocks-slug/",
						"url": "/blocks"
					}
				]
				JSON
			),
			array(
				'Simple',
				false,
				true,
				<<< JSON
				[
					{
						"count": 1,
						"fieldResultsArray": [
							{
								"context": "A header block text A <mark>Simple</mark> First Column Table Header Second Column Table",
								"field": "+main",
								"matches": [
									"Simple"
								]
							}
						],
						"path": "/blocks-slug/",
						"url": "/blocks"
					}
				]
				JSON
			),
			array(
				'si.*?fi.st co.umn .able',
				true,
				false,
				<<< JSON
				[
					{
						"count": 1,
						"fieldResultsArray": [
							{
								"context": "A header block text A <mark>Simple First Column Table</mark> Header Second Column Table Header First table row",
								"field": "+main",
								"matches": [
									"Simple First Column Table"
								]
							}
						],
						"path": "/blocks-slug/",
						"url": "/blocks"
					}
				]
				JSON
			),
			array(
				'default.*content',
				true,
				false,
				<<< JSON
				[
					{
						"count": 1,
						"fieldResultsArray": [
							{
								"context": "Shared <mark>default text content</mark>",
								"field": "shared",
								"matches": [
									"default text content"
								]
							}
						],
						"path": null,
						"url": null
					}
				]
				JSON
			),
			array(
				'left', // Test ignoring blacklisted properties
				true,
				true,
				'[]'
			),
			array(
				'table.(row|header)',
				true,
				false,
				<<< JSON
				[
					{
						"count": 4,
						"fieldResultsArray": [
							{
								"context": "A header block text A Simple First Column <mark>Table Header</mark> Second Column <mark>Table Header</mark> First <mark>table row</mark> and ... First <mark>table row</mark> and second column A simple paragraph text List",
								"field": "+main",
								"matches": [
									"Table Header",
									"Table Header",
									"table row",
									"table row"
								]
							}
						],
						"path": "/blocks-slug/",
						"url": "/blocks"
					}
				]
				JSON,
			),
			array(
				'third level',
				false,
				false,
				<<< JSON
				[
					{
						"count": 1,
						"fieldResultsArray": [
							{
								"context": "item Second list item A nested list Second level <mark>Third level</mark> item Another simple item Some text containing the",
								"field": "+main",
								"matches": [
									"Third level"
								]
							}
						],
						"path": "/blocks-slug/",
						"url": "/blocks"
					}
				]
				JSON
			),
			array(
				'test',
				false,
				false,
				<<< JSON
				[
					{
						"count": 2,
						"fieldResultsArray": [
							{
								"context": "My <mark>Test</mark> Site",
								"field": "sitename",
								"matches": ["Test"]
							},
							{
								"context": "<mark>test</mark>",
								"field": "+default",
								"matches": ["test"]
							}
						],
						"path": null,
						"url": null
					},
					{
						"count": 4,
						"fieldResultsArray": [
							{
								"context": "<mark>Test</mark> String",
								"field": "test",
								"matches": ["Test"]
							},
							{
								"context": "&quot;Quoted&quot; &quot;<mark>Test</mark>&quot; &quot;String&quot;",
								"field": "quoted",
								"matches": ["Test"]
							},
							{
								"context": "<mark>test</mark>",
								"field": "link",
								"matches": ["test"]
							},
							{
								"context": "Component <mark>test</mark>",
								"field": "+component",
								"matches": ["test"]
							}
						],
						"path": "/page-slug/",
						"url": "/page"
					},
					{
						"count": 1,
						"fieldResultsArray": [
							{
								"context": "Breadcrumbs<mark>Test</mark>",
								"field": "title",
								"matches": ["Test"]
							}
						],
						"path": "/page-slug/subpage/breadcrumbs-test",
						"url": "/page/subpage/breadcrumbs-test"
					}
				]
				JSON
			),
			array(
				'word',
				false,
				false,
				<<< JSON
				[
					{
						"count": 3,
						"fieldResultsArray": [
							{
								"context": "A longer paragraph that includes not only the <mark>word</mark> find but also the <mark>word</mark> me.",
								"field": "findMe",
								"matches": ["word", "word"]
							},
							{
								"context": "This paragraph only contains the <mark>word</mark> find and no other search term.",
								"field": "doNotFindMe",
								"matches": ["word"]
							}
						],
						"path": "/page-slug/",
						"url": "/page"
					},
					{
						"count": 2,
						"fieldResultsArray": [
							{
								"context": "item Another simple item Some text containing the <mark>word</mark> me and the <mark>word</mark> find nested in the list",
								"field": "+main",
								"matches": ["word", "word"]
							}
						],
						"path": "/blocks-slug/",
						"url": "/blocks"
					}
				]
				JSON
			),
		);
	}

	#[DataProvider('dataForTestSearchPerFileIsSame')]
	public function testSearchPerFileIsSame($searchValue, $isRegex, $isCaseSensitive, $expected) {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad('default');
		$Search = new Search(
			$searchValue,
			$isRegex,
			$isCaseSensitive,
			$Automad->getPages(),
			$Automad->SearchIndexCache
		);

		$flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
		$result = $Search->searchPerFile();

		/** @disregard */
		$this->assertSame(
			json_encode(json_decode($expected), $flags),
			json_encode($result, $flags)
		);
	}
}
