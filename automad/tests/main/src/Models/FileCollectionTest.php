<?php

namespace Automad\Models;

use Automad\Core\Messenger;
use Automad\System\FileSystem;
use PHPUnit\Framework\TestCase;

class FileCollectionTest extends TestCase {
	private string $chunkCacheDir = AM_BASE_DIR . AM_DIR_CACHE;
	private bool $chunkCacheDirExisted = false;
	private string $dir = '';
	private string $uuid = '';

	protected function setUp(): void {
		$this->chunkCacheDirExisted = is_dir($this->chunkCacheDir);
		$this->uuid = 'test-' . uniqid();
		$this->dir = FileSystem::getTmpDir() . '/upload-test-' . $this->uuid . '/';

		FileSystem::makeDir($this->dir);
	}

	protected function tearDown(): void {
		foreach (FileSystem::glob($this->dir . '*') as $file) {
			unlink($file);
		}

		rmdir($this->dir);

		$chunk = $this->chunkCacheDir . '/tmp/' . $this->uuid . '.chunks';

		if (file_exists($chunk)) {
			unlink($chunk);
		}

		if (!$this->chunkCacheDirExisted) {
			rmdir($this->chunkCacheDir . '/tmp');
			rmdir($this->chunkCacheDir);
		}
	}

	public function testUploadRejectsNameTruncatingToPhp() {
		// The slug is truncated to 100 chars, which turns "aaa….php.jpg" into "aaa….php".
		$name = str_repeat('a', 96) . '.php.jpg';
		$tmp = $this->dir . 'chunk.tmp';

		file_put_contents($tmp, '<?php echo "pwned";');

		// Use a final, non-first chunk, since move_uploaded_file() only accepts real HTTP uploads.
		$chunk = (object) array(
			'dzchunkindex' => '1',
			'dzchunkbyteoffset' => '1',
			'dzchunksize' => 100,
			'dztotalchunkcount' => 2,
			'dzuuid' => $this->uuid,
			'tmp_name' => $tmp,
			'name' => $name
		);

		$Messenger = new Messenger();

		FileCollection::upload($chunk, $this->dir, $Messenger);

		/** @disregard */
		$this->assertSame(array(), FileSystem::glob($this->dir . '*.php'));

		/** @disregard */
		$this->assertNotEmpty($Messenger->getError());
	}
}
