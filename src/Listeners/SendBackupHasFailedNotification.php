<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Listeners;

use Arcanedev\LaravelBackup\Events\BackupActionHasFailed;
use Arcanedev\LaravelBackup\Listeners\Concerns\HandleNotifications;
use Arcanedev\LaravelBackup\Notifications\BackupHasFailedNotification;

/**
 * Class     SendBackupHasFailedNotification
 *
 * @package  Arcanedev\LaravelBackup\Listeners
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SendBackupHasFailedNotification
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
     * @param  \Arcanedev\LaravelBackup\Events\BackupActionHasFailed  $event
     *
     * @return mixed|void
     */
    public function handle(BackupActionHasFailed $event)
    {
        if ($event->passable->isNotificationsDisabled())
            return;

        $notification = $this->makeNotification(BackupHasFailedNotification::class)
            ->setEvent($event);

        $this->getNotifiable()->notify($notification);
    }
}
