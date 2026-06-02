<?php

namespace Automad\Controllers\API;

use Automad\Core\FileSystem;
use Automad\Models\UserCollection;
use PHPUnit\Framework\TestCase;

class UserCollectionControllerTest extends TestCase {
	public function testFirstUserResponseIsSame() {
		if (file_exists(UserCollection::FILE_ACCOUNTS)) {
			unlink(UserCollection::FILE_ACCOUNTS);
		}

		$Response = UserCollectionController::createFirstUser();
		$json = $Response->json();
		$data = json_decode($json);

		/** @disregard */
		$this->assertSame($data->code, 200);

		FileSystem::write(UserCollection::FILE_ACCOUNTS, '');

		$Response = UserCollectionController::createFirstUser();
		$json = $Response->json();
		$data = json_decode($json);

		/** @disregard */
		$this->assertSame($data->code, 403);

		/** @disregard */
		$this->assertSame($data->error, 'Another user has been created already');

		unlink(UserCollection::FILE_ACCOUNTS);
	}
}
