<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MailTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'test',
				'replaced',
				false,
				false,
				<<< JSON
				{
					"id": "1",
					"type": "mail",
					"data": {
						"to": "user@test.local",
						"labelAddress": "Test email",
						"errorAddress": "A vaild test email is required",
						"labelSubject": "Test subject",
						"errorSubject": "Please enter a test subject",
						"labelBody": "Test message",
						"errorBody": "Please enter a test message",
						"labelSend": "Send test email",
						"error": "A test error has occured.",
						"success": "The test email was send."
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "mail",
					"data": {
						"to": "user@replaced.local",
						"labelAddress": "replaced email",
						"errorAddress": "A vaild replaced email is required",
						"labelSubject": "replaced subject",
						"errorSubject": "Please enter a replaced subject",
						"labelBody": "replaced message",
						"errorBody": "Please enter a replaced message",
						"labelSend": "Send replaced email",
						"error": "A replaced error has occured.",
						"success": "The replaced email was send."
					},
					"tunes": []
				}
				JSON,
				'user@test.local Test email A vaild test email is required Test subject Please enter a test subject Test message Please enter a test message Send test email A test error has occured. The test email was send.'
			)
		);
	}

	#[DataProvider('dataForTestSearchAndReplaceIsSame')]
	public function testSearchAndReplaceIsSame(
		string $search,
		string $replace,
		bool $isRegex,
		bool $isCaseInsensitive,
		string $blockJson,
		string $expectedReplacedJson,
		string $expectedString
	) {
		Block::test(
			$this,
			'Mail',
			$search,
			$replace,
			$isRegex,
			$isCaseInsensitive,
			$blockJson,
			$expectedReplacedJson,
			$expectedString
		);
	}
}
