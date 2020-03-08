<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Listeners;

use Arcanedev\LaravelBackup\Events\HealthyBackupsWasFound;
use Arcanedev\LaravelBackup\Listeners\Concerns\HandleNotifications;
use Arcanedev\LaravelBackup\Notifications\HealthyBackupsWasFoundNotification;

/**
 * Class     SendHealthyBackupWasFoundNotification
 *
 * @package  Arcanedev\LaravelBackup\Listeners
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SendHealthyBackupWasFoundNotification
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
     * @param  \Arcanedev\LaravelBackup\Events\HealthyBackupsWasFound  $event
     *
     * @return mixed|void
     */
    public function handle(HealthyBackupsWasFound $event)
    {
        if ( ! $event->passable->isNotificationsDisabled())
            return;

        $notification = $this->makeNotification(HealthyBackupsWasFoundNotification::class)
            ->setEvent($event);

        $this->getNotifiable()->notify($notification);
    }
}
