<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Notifications;

use Arcanedev\LaravelBackup\Entities\BackupDestination;
use Arcanedev\LaravelBackup\Notifications\Messages\DiscordMessage;
use Illuminate\Notifications\Messages\{MailMessage, SlackAttachment, SlackMessage};

/**
 * Class     CleanupHasFailedNotification
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CleanupHasFailedNotification extends AbstractNotification
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Send to mail channel.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(): MailMessage
    {
        $message = static::makeMailMessage()
            ->error()
            ->subject(__('Cleaning up the backups of :application_name failed', [
                'application_name' => $applicationName = $this->applicationName(),
            ]))
            ->line(__('An error occurred while cleaning up the backups of :application_name', [
                'application_name' => $applicationName,
            ]))
            ->line(__('Exception message: :message', [
                'message' => $this->getEventException()->getMessage(),
            ]))
            ->line(__('Exception trace: :trace', [
                'trace' => $this->getEventException()->getTraceAsString(),
            ]));

        $this->getBackupDestinations()->each(function (BackupDestination $destination) use ($message) {
            $this->backupDestinationProperties($destination)->each(function ($value, $name) use ($message) {
                $message->line("{$name}: $value");
            });
        });

        return $message;
    }

    /**
     * Send to slack channel.
     *
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack(): SlackMessage
    {
        $message = static::makeSlackMessage()
            ->error()
            ->content(__('Cleaning up the backups of :application_name failed.', [
                'application_name' => $this->applicationName(),
            ]))
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title(__('Exception message'))
                    ->content($this->getEventException()->getMessage());
            })
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->title(__('Exception trace'))
                    ->content($this->getEventException()->getTraceAsString());
            });

        $this->getBackupDestinations()->each(function (BackupDestination $destination) use ($message) {
            $message->attachment(function (SlackAttachment $attachment) use ($destination) {
                $attachment->fields(
                    $this->backupDestinationProperties($destination)->toArray()
                );
            });
        });

        return $message;
    }

    public function toDiscord(): DiscordMessage
    {
        return (new DiscordMessage)
            ->error()
            ->from(
                config('backup.notifications.discord.username'),
                config('backup.notifications.discord.avatar_url')
            )
            ->title(
                __('Cleaning up the backups of :application_name failed.', [
                    'application_name' => $this->applicationName(),
                ])
            )
            ->fields([
                trans('backup::notifications.exception_message_title') => $this->event->exception->getMessage(),
            ]);
    }
}
