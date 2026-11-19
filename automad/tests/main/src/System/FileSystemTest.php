<?php

namespace Automad\System;

use PHPUnit\Framework\TestCase;

class FileSystemTest extends TestCase {
	public function testTmpDirIsPrivate() {
		if (PHP_OS_FAMILY === 'Windows') {
			/** @disregard */
			$this->markTestSkipped('Unix permissions are not supported on Windows.');
		}

		// Simulate an existing installation with a world-readable temp directory.
		chmod(FileSystem::getTmpDir(), 0755);
		clearstatcache();

		$dir = FileSystem::getTmpDir();
		clearstatcache();

		/** @disregard */
		$this->assertSame(0700, fileperms($dir) & 0777);
	}

	public function testTmpDirHasNoSharedParent() {
		$dir = FileSystem::getTmpDir();

		/** @disregard */
		$this->assertStringStartsWith('automad-', basename($dir));
		/** @disregard */
		$this->assertContains(dirname($dir), array('/tmp', rtrim(sys_get_temp_dir(), '/')));
	}
}
