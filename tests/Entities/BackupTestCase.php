<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Entities;

use Arcanedev\LaravelBackup\Entities\Backup;
use Arcanedev\LaravelBackup\Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class     BackupTestCase
 *
 * @package  Arcanedev\LaravelBackup\Tests\Entities
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class BackupTestCase extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  string */
    protected $diskName;

    /** @var  string */
    protected $backupName;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->diskName = 'primary-storage';
        $this->backupName = $this->app['config']->get('app.name');
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make a backup file.
     *
     * @param  string  $path
     * @param  int     $ageInDays
     *
     * @return \Arcanedev\LaravelBackup\Entities\Backup
     */
    protected function makeBackupFile(string $path, int $ageInDays = 0): Backup
    {
        $date = Carbon::now()->subDays($ageInDays)->toDateTime();
        $path = static::createFileOnDisk($this->diskName, $path, $date);

        return new Backup(Storage::disk($this->diskName), $path);
    }
}
