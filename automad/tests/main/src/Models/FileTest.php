<?php

namespace Automad\Models;

use Automad\Core\Messenger;
use Automad\System\FileSystem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase {
	const PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

	private string $dir = '';

	protected function setUp(): void {
		$this->dir = FileSystem::getTmpDir() . '/import-test-' . uniqid() . '/';

		FileSystem::makeDir($this->dir);
	}

	protected function tearDown(): void {
		foreach (array_merge(FileSystem::glob($this->dir . '*'), FileSystem::glob($this->dir . '.[!.]*')) as $file) {
			unlink($file);
		}

		rmdir($this->dir);
	}

	public static function dataForTestImportRejectsReservedBasename() {
		return array(
			array('data', 'not an image'),
			array('.history', 'not an image'),
			array('.htaccess', base64_decode(self::PNG))
		);
	}

	#[DataProvider('dataForTestImportRejectsReservedBasename')]
	public function testImportRejectsReservedBasename(string $name, string $data) {
		$existing = $this->dir . 'data';

		file_put_contents($existing, 'original');

		$Messenger = new Messenger();

		/** @disregard */
		$this->assertSame('', File::save($this->dir, $name, $data, $Messenger));

		/** @disregard */
		$this->assertNotEmpty($Messenger->getError());

		/** @disregard */
		$this->assertSame('original', file_get_contents($existing));

		/** @disregard */
		$this->assertSame(array('data'), array_map('basename', array_merge(
			FileSystem::glob($this->dir . '*'),
			FileSystem::glob($this->dir . '.[!.]*')
		)));
	}

	public function testImportSavesAllowedFile() {
		$Messenger = new Messenger();

		/** @disregard */
		$this->assertSame('file.pdf', File::save($this->dir, 'File.pdf', 'pdf', $Messenger));

		/** @disregard */
		$this->assertSame('image.png', File::save($this->dir, 'image', base64_decode(self::PNG), $Messenger));

		/** @disregard */
		$this->assertSame('', $Messenger->getError());

		/** @disregard */
		$this->assertFileExists($this->dir . 'file.pdf');

		/** @disregard */
		$this->assertFileExists($this->dir . 'image.png');
	}

	public function testImportValidatesBeforePlacingInWebRoot() {
		$Messenger = new Messenger();

		/** @disregard */
		$this->assertSame('', File::save($this->dir, str_repeat('a', 96) . '.php.jpg', '<?php echo "pwned";', $Messenger));

		/** @disregard */
		$this->assertSame('', File::save($this->dir, 'script', '<?php echo "pwned";', $Messenger));

		/** @disregard */
		$this->assertNotEmpty($Messenger->getError());

		/** @disregard */
		$this->assertSame(array(), FileSystem::glob($this->dir . '*'));
	}
}
