<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Notifications;

use Arcanedev\LaravelBackup\Entities\BackupDestinationStatus;
use Illuminate\Notifications\Messages\{MailMessage, SlackAttachment, SlackMessage};

/**
 * Class     HealthyBackupsWasFoundNotification
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class HealthyBackupsWasFoundNotification extends AbstractNotification
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
            ->success()
            ->subject(__('The backups for :application_name are considered healthy. Good job!', [
                'application_name' => $applicationName = $this->applicationName(),
            ]));

        $this->getStatuses()->each(function (BackupDestinationStatus $status) use ($applicationName, $message) {
            $destination = $status->backupDestination();

            $message->line(__('The backups for :application_name on disk :disk_name are healthy', [
                'application_name' => $applicationName,
                'disk_name'        => $destination->diskName(),
            ]));

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
        $message = self::makeSlackMessage()
            ->success()
            ->content(__('The backups for :application_name are healthy', [
                'application_name' => $this->applicationName(),
            ]));

        $this->getStatuses()->each(function (BackupDestinationStatus $status) use ($message) {
            $message->attachment(function (SlackAttachment $attachment) use ($status) {
                $attachment->fields($this->backupDestinationProperties($status->backupDestination())->toArray());
            });
        });

        return $message;
    }
}
