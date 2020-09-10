<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Listeners;

use Arcanedev\LaravelBackup\Events\CleanupActionWasSuccessful;
use Arcanedev\LaravelBackup\Listeners\Concerns\HandleNotifications;
use Arcanedev\LaravelBackup\Notifications\CleanupWasSuccessfulNotification;

/**
 * Class     SendCleanupWasSuccessfulNotification
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SendCleanupWasSuccessfulNotification
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
     * @param  \Arcanedev\LaravelBackup\Events\CleanupActionWasSuccessful  $event
     *
     * @return mixed|void
     */
    public function handle(CleanupActionWasSuccessful $event)
    {
        if ($event->passable->isNotificationsDisabled())
            return;

        $notification = $this->makeNotification(CleanupWasSuccessfulNotification::class)
            ->setEvent($event);

        $this->getNotifiable()->notify($notification);
    }
}
