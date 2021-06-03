<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Console;

use Arcanedev\LaravelBackup\Tests\TestCase;
use Illuminate\Console\Command;

/**
 * Class     ListBackupCommandTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ListBackupCommandTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_run(): void
    {
        $this->artisan('backup:list')
             ->assertExitCode(Command::SUCCESS);
    }
}
