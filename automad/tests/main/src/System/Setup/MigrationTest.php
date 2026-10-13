<?php

namespace Automad\System\Setup;

use Automad\System\Setup\Migrations\AbstractMigration;
use PHPUnit\Framework\TestCase;

class MigrationTest extends TestCase {
	public function testFailingMigrationDoesNotStopFollowingMigrations() {
		$log = new \ArrayObject();

		$Failing = new class($log) extends AbstractMigration {
			private \ArrayObject $log;

			public function __construct(\ArrayObject $log) {
				$this->log = $log;
			}

			public function getName(): string {
				return 'failing';
			}

			public function run(): void {
				$this->log->append('failing');

				throw new \Exception('Migration failed');
			}
		};

		$Passing = new class($log) extends AbstractMigration {
			private \ArrayObject $log;

			public function __construct(\ArrayObject $log) {
				$this->log = $log;
			}

			public function getName(): string {
				return 'passing';
			}

			public function run(): void {
				$this->log->append('passing');
			}
		};

		$results = (new Migration(array($Failing, $Passing)))->run();

		/** @disregard */
		$this->assertSame(array('failing', 'passing'), $log->getArrayCopy());
		/** @disregard */
		$this->assertSame(array('failing' => 'Migration failed', 'passing' => null), $results);
	}
}
