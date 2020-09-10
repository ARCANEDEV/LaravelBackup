<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Entities;

use Arcanedev\LaravelBackup\Entities\{BackupDestination, BackupDestinationCollection};
use Arcanedev\LaravelBackup\Tests\TestCase;

/**
 * Class     BackupDestinationCollectionTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupDestinationCollectionTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_make_from_disks_names(): void
    {
        $backupName = $this->getBackupName();
        $diskNames  = $this->getBackupDestinationDisks();
        $collection = BackupDestinationCollection::makeFromDisksNames($diskNames, $backupName);

        $expectations = [
            \Illuminate\Support\Collection::class,
            \Arcanedev\LaravelBackup\Entities\BackupDestinationCollection::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $collection);
        }

        foreach ($collection as $item) {
            static::assertInstanceOf(BackupDestination::class, $item);
        }
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the backup name.
     *
     * @return string
     */
    protected function getBackupName(): string
    {
        return $this->app['config']->get('backup.name');
    }

    /**
     * Get the backup destination's disks.
     *
     * @return array
     */
    protected function getBackupDestinationDisks(): array
    {
        return $this->app['config']->get('backup.destination.disks', []);
    }
}
