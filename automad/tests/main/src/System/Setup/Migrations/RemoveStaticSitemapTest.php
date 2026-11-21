<?php

namespace Automad\System\Setup\Migrations;

use Automad\System\FileSystem;
use PHPUnit\Framework\TestCase;

class RemoveStaticSitemapTest extends TestCase {
	private string $dir = '';

	protected function setUp(): void {
		$this->dir = FileSystem::getTmpDir() . '/remove-static-sitemap-test-' . uniqid();
		FileSystem::makeDir($this->dir);
	}

	protected function tearDown(): void {
		array_map('unlink', FileSystem::glob("{$this->dir}/*"));
		rmdir($this->dir);
	}

	public function testRun(): void {
		file_put_contents("{$this->dir}/sitemap.xml", '<urlset></urlset>');
		file_put_contents("{$this->dir}/robots.txt", 'User-agent: *');
		file_put_contents("{$this->dir}/other.txt", 'keep');

		$Migration = new RemoveStaticSitemap($this->dir);
		$Migration->run();

		/** @disregard */
		$this->assertFileDoesNotExist("{$this->dir}/sitemap.xml");
		/** @disregard */
		$this->assertFileDoesNotExist("{$this->dir}/robots.txt");
		/** @disregard */
		$this->assertFileExists("{$this->dir}/other.txt");

		$Migration->run();

		/** @disregard */
		$this->assertFileExists("{$this->dir}/other.txt");
	}
}
