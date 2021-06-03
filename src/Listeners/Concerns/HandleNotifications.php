<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Listeners\Concerns;

/**
 * Trait     HandleNotifications
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
trait HandleNotifications
{
    /**
     * Make the notification instance.
     *
     * @param  string  $class
     *
     * @return \Arcanedev\LaravelBackup\Notifications\AbstractNotification|mixed
     */
    protected function makeNotification(string $class)
    {
        return app()->make($class);
    }

    /**
     * Get the notifiable.
     *
     * @return \Arcanedev\LaravelBackup\Entities\Notifiable|mixed
     */
    protected function getNotifiable()
    {
        return app()->make(config('backup.notifications.notifiable'));
    }
}
