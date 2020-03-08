<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Listeners;

use Arcanedev\LaravelBackup\Events\UnhealthyBackupsWasFound;
use Arcanedev\LaravelBackup\Listeners\Concerns\HandleNotifications;
use Arcanedev\LaravelBackup\Notifications\UnhealthyBackupsWasFoundNotification;

/**
 * Class     SendUnhealthyBackupWasFoundNotification
 *
 * @package  Arcanedev\LaravelBackup\Listeners
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SendUnhealthyBackupWasFoundNotification
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
     * @param  \Arcanedev\LaravelBackup\Events\UnhealthyBackupsWasFound  $event
     *
     * @return mixed|void
     */
    public function handle(UnhealthyBackupsWasFound $event)
    {
        if ( ! $event->passable->isNotificationsDisabled())
            return;

        $notification = $this->makeNotification(UnhealthyBackupsWasFoundNotification::class)
            ->setEvent($event);

        $this->getNotifiable()->notify($notification);
    }
}
