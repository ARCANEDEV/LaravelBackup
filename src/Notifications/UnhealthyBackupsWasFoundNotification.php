<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Notifications;

use Arcanedev\LaravelBackup\Entities\BackupDestinationStatus;
use Arcanedev\LaravelBackup\Notifications\Messages\DiscordMessage;
use Illuminate\Notifications\Messages\{MailMessage, SlackAttachment, SlackMessage};

/**
 * Class     UnhealthyBackupsWasFoundNotification
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class UnhealthyBackupsWasFoundNotification extends AbstractNotification
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
        $message = self::makeMailMessage()
            ->error()
            ->subject(__('Important: The backups for :application_name are unhealthy', [
                'application_name' => $applicationName = $this->applicationName(),
            ]));

        $this->getStatuses()->each(function (BackupDestinationStatus $status) use ($applicationName, $message) {
            $destination = $status->backupDestination();

            $message->line(__('The backups for :application_name on disk :disk_name are unhealthy.', [
                'application_name' => $applicationName,
                'disk_name'        => $destination->diskName(),
            ]))
            ->line(static::problemDescription($status));

            $this->backupDestinationProperties($destination)->each(function ($value, $name) use ($message) {
                $message->line("{$name}: $value");
            });

            $failure = $status->getHealthCheckFailure();

            if ($failure->wasUnexpected()) {
                $message
                    ->line(__('Health check: :name', ['name' => $failure->healthCheck()->name()]))
                    ->line(__('Exception message: :message', ['message' => $failure->exception()->getMessage()]))
                    ->line(__('Exception trace: :trace', ['trace' => $failure->exception()->getTraceAsString()]));
            }
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
        $message = self::makeSlackMessage()
            ->error()
            ->content(__('Important: The backups for :application_name are unhealthy', [
                'application_name' => $this->applicationName(),
            ]));

        $this->getStatuses()->each(function (BackupDestinationStatus $status) use ($message) {
            $message->attachment(function (SlackAttachment $attachment) use ($status) {
                $attachment->fields(
                    $this->backupDestinationProperties($status->backupDestination())->toArray()
                );
            });

            $failure = $status->getHealthCheckFailure();

            if ($failure->wasUnexpected()) {
                $message
                    ->attachment(function (SlackAttachment $attachment) use ($failure) {
                        $attachment
                            ->title(__('Health check'))
                            ->content($failure->healthCheck()->name());
                    })
                    ->attachment(function (SlackAttachment $attachment) use ($failure) {
                        $attachment
                            ->title(__('Exception message'))
                            ->content($failure->exception()->getMessage());
                    })
                    ->attachment(function (SlackAttachment $attachment) use ($failure) {
                        $attachment
                            ->title(__('Exception trace'))
                            ->content($failure->exception()->getTraceAsString());
                    });
            }
        });

        return $message;
    }

    /**
     * Send to discord channel.
     *
     * @return \Arcanedev\LaravelBackup\Notifications\Messages\DiscordMessage
     */
    public function toDiscord(): DiscordMessage
    {
        $message = (new DiscordMessage)
            ->error()
            ->from(
                config('backup.notifications.discord.username'),
                config('backup.notifications.discord.avatar_url')
            )
            ->title(
                __('Important: The backups for :application_name are unhealthy', [
                    'application_name' => $applicationName = $this->applicationName(),
                ])
            );

        $this->getStatuses()->each(function (BackupDestinationStatus $status) use ($message) {
            $message->fields(
                $this->backupDestinationProperties($status->backupDestination())->toArray()
            );

            $failure = $status->getHealthCheckFailure();

            if ($failure->wasUnexpected()) {
                $message
                    ->fields(['Health Check' => $failure->healthCheck()->name()])
                    ->fields([
                        trans('backup::notifications.exception_message_title') => $failure->exception()->getMessage(),
                    ]);
            }
        });

        return $message;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the problem description.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestinationStatus  $status
     *
     * @return string
     */
    protected static function problemDescription(BackupDestinationStatus $status): string
    {
        $failure = $status->getHealthCheckFailure();

        return $failure->wasUnexpected()
            ? __('Sorry, an exact reason cannot be determined.')
            : $failure->exception()->getMessage();
    }
}
