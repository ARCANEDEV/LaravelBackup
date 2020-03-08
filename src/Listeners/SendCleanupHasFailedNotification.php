<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Listeners;

use Arcanedev\LaravelBackup\Events\CleanupActionHasFailed;
use Arcanedev\LaravelBackup\Listeners\Concerns\HandleNotifications;
use Arcanedev\LaravelBackup\Notifications\CleanupHasFailedNotification;

/**
 * Class     SendCleanupHasFailedNotification
 *
 * @package  Arcanedev\LaravelBackup\Listeners
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SendCleanupHasFailedNotification
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
     * @param  \Arcanedev\LaravelBackup\Events\CleanupActionHasFailed  $event
     *
     * @return mixed|void
     */
    public function handle(CleanupActionHasFailed $event)
    {
        if ($event->passable->isNotificationsDisabled())
            return;

        $notification = $this->makeNotification(CleanupHasFailedNotification::class)
            ->setEvent($event);

        $this->getNotifiable()->notify($notification);
    }
}
