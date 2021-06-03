<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

/**
 * Class     DiscordChannel
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DiscordChannel
{
    /**
     * @param  \Arcanedev\LaravelBackup\Entities\Notifiable  $notifiable
     * @param  \Illuminate\Notifications\Notification|mixed  $notification
     */
    public function send($notifiable, Notification $notification): void
    {
        Http::post(
            $notifiable->routeNotificationForDiscord(),
            $notification->toDiscord()->toArray()
        );
    }
}
