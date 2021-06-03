<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities;

use Illuminate\Notifications\Notifiable as NotifiableTrait;

/**
 * Class     Notifiable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Notifiable
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use NotifiableTrait;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * @return string|mixed
     */
    public function routeNotificationForMail(): string
    {
        return (string) config('backup.notifications.mail.to');
    }

    /**
     * @return string
     */
    public function routeNotificationForSlack(): string
    {
        return (string) config('backup.notifications.slack.webhook_url');
    }

    /**
     * @return string
     */
    public function routeNotificationForDiscord(): string
    {
        return config('backup.notifications.discord.webhook_url');
    }

    /**
     * @return int
     */
    public function getKey()
    {
        return 1;
    }
}
