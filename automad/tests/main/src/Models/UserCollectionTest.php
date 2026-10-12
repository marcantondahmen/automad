<?php

namespace Automad\Models;

use Automad\Core\Messenger;
use Automad\System\FileSystem;
use PHPUnit\Framework\TestCase;

class UserCollectionTest extends TestCase {
	public function testGeneratePHPReturnsUsers() {
		$UserCollection = new UserCollection();
		$Messenger = new Messenger();

		/** @disregard */
		$this->assertTrue($UserCollection->createUser('test-user', "pass'word\\", "pass'word\\", 'test@example.com', $Messenger));

		$file = FileSystem::getTmpDir() . '/accounts-test-' . uniqid() . '.php';
		file_put_contents($file, $UserCollection->generatePHP());

		ob_start();
		$serialized = FileSystem::readPhpString($file);
		$output = ob_get_clean();

		unlink($file);

		/** @disregard */
		$this->assertSame('', $output);
		/** @disregard */
		$this->assertIsString($serialized);

		$users = unserialize(str_replace('O:*:"~"', 'O:17:"Automad\Auth\User"', strval($serialized)));
		$User = $users[0];

		/** @disregard */
		$this->assertSame('test-user', $User->name);
		/** @disregard */
		$this->assertSame('test@example.com', $User->email);
		/** @disregard */
		$this->assertTrue($User->verifyPassword("pass'word\\"));
	}
}
