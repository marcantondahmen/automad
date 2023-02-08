<?php

namespace Automad\Core;

use PHPUnit\Framework\TestCase;

/**
 * @testdox Automad\Core\DataFile
 */
class DataFileTest extends TestCase {
	/**
	 * @testdox readLegacyFormat('/path')
	 * @param mixed $legacy
	 */
	public function testReadLegacyFormatIsSame() {
		$this->assertSame(
			json_encode(DataFile::read('/blocks')),
			json_encode(Parse::dataFile(AM_BASE_DIR . AM_DIR_PAGES . '/legacy/blocks.txt'))
		);
	}
}
