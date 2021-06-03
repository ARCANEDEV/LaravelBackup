<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Providers;

use Arcanedev\LaravelBackup\Notifications\Channels\DiscordChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

/**
 * Class     NotificationServiceProvider
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class NotificationServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerDiscordChannel();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the discord channel.
     */
    protected function registerDiscordChannel(): void
    {
        Notification::resolved(function (ChannelManager $service): void {
            $service->extend('discord', function ($app) {
                return new DiscordChannel;
            });
        });
    }
}
