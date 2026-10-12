<?php

namespace Automad\System\Setup\Migrations;

use Automad\System\FileSystem;
use PHPUnit\Framework\TestCase;

class ConfigPhpStringsTest extends TestCase {
	public function testRun() {
		$dir = FileSystem::getTmpDir() . '/php-string-migration-test-' . uniqid();
		$serialized = 'O:*:"~":1:{s:6:"apiKey";s:6:"secret";}';
		$nowdoc = "<?php return <<< 'JSON'\r\n{\r\n    \"AM_MAIL_SMTP_PASSWORD\": \"secret\"\r\n}\r\nJSON;\r\n";

		FileSystem::makeDir($dir);
		file_put_contents("$dir/legacy.php", $serialized);
		file_put_contents("$dir/config.php", $nowdoc);
		FileSystem::writePhpString("$dir/wrapped.php", $serialized);

		$wrapped = file_get_contents("$dir/wrapped.php");
		$Migration = new ConfigPhpStrings($dir);
		$Migration->run();

		/** @disregard */
		$this->assertSame(array("$dir/legacy.php"), $Migration->getMigrated());
		/** @disregard */
		$this->assertStringStartsWith("<?php\ndefined('AUTOMAD') or die();", strval(file_get_contents("$dir/legacy.php")));
		/** @disregard */
		$this->assertSame($serialized, FileSystem::readPhpString("$dir/legacy.php"));
		/** @disregard */
		$this->assertSame($nowdoc, file_get_contents("$dir/config.php"));
		/** @disregard */
		$this->assertSame($wrapped, file_get_contents("$dir/wrapped.php"));

		$Migration->run();

		/** @disregard */
		$this->assertSame(array(), $Migration->getMigrated());

		array_map('unlink', FileSystem::glob("$dir/*.php"));
		rmdir($dir);
	}
}
