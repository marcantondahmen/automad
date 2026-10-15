<?php

namespace Automad\Controllers\Api;

use Automad\Core\Messenger;
use Automad\Models\UserCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase {
	protected function setUp(): void {
		$UserCollection = new UserCollection();
		$Messenger = new Messenger();

		$UserCollection->createUser('existing', 'Secret#123', 'Secret#123', 'existing@example.com', $Messenger);
		$UserCollection->save($Messenger);
	}

	protected function tearDown(): void {
		$_POST = array();

		if (file_exists(UserCollection::FILE_ACCOUNTS)) {
			unlink(UserCollection::FILE_ACCOUNTS);
		}
	}

	public static function dataForTestResetPasswordUniformErrorForUnknownUser(): array {
		return array(
			array('123456', 'weak', 'weak'),
			array('123456', 'Strong#456', 'Strong#456'),
			array('', 'Strong#456', 'Strong#456')
		);
	}

	#[DataProvider('dataForTestResetPasswordUniformErrorForUnknownUser')]
	public function testResetPasswordUniformErrorForUnknownUser(string $code, string $password1, string $password2): void {
		$errors = array();

		foreach (array('existing', 'unknown') as $nameOrEmail) {
			$_POST = array(
				'nameOrEmail' => $nameOrEmail,
				'code' => $code,
				'password1' => $password1,
				'password2' => $password2
			);

			$errors[] = json_decode(UserController::resetPassword()->json())->error ?? null;
		}

		/** @disregard */
		$this->assertNotEmpty($errors[0]);

		/** @disregard */
		$this->assertSame($errors[0], $errors[1]);
	}
}
