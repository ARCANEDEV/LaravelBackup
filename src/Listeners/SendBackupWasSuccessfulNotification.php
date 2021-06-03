<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Listeners;

use Arcanedev\LaravelBackup\Events\BackupActionWasSuccessful;
use Arcanedev\LaravelBackup\Listeners\Concerns\HandleNotifications;
use Arcanedev\LaravelBackup\Notifications\BackupWasSuccessfulNotification;

/**
 * Class     SendBackupWasSuccessfulNotification
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SendBackupWasSuccessfulNotification
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use HandleNotifications;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle the event.
     *
     * @param  \Arcanedev\LaravelBackup\Events\BackupActionWasSuccessful  $event
     *
     * @return mixed|void
     */
    public function handle(BackupActionWasSuccessful $event)
    {
        if ($event->passable->isNotificationsDisabled())
            return;

        $notification = $this->makeNotification(BackupWasSuccessfulNotification::class)
            ->setEvent($event);

        $this->getNotifiable()->notify($notification);
    }
}
