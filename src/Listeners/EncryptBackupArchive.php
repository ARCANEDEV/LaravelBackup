<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Listeners;

use Arcanedev\LaravelBackup\Events\BackupZipWasCreated;
use Arcanedev\LaravelBackup\Helpers\Zip;

/**
 * Class     EncryptBackupArchive
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class EncryptBackupArchive
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the event.
     *
     * @param  \Arcanedev\LaravelBackup\Events\BackupZipWasCreated  $event
     */
    public function handle(BackupZipWasCreated $event): void
    {
        $this->encrypt($event->zip);
    }

    /**
     * Encrypt the archive.
     *
     * @param  \Arcanedev\LaravelBackup\Helpers\Zip  $zip
     */
    protected function encrypt(Zip $zip): void
    {
        $wasClosed = ! $zip->isOpened();

        if ($wasClosed)
            $zip->open();

        $zip->encrypt();

        if ($wasClosed && $zip->isOpened())
            $zip->close();
    }
}
