<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Notifications;

use Arcanedev\LaravelBackup\Entities\BackupDestination;
use Illuminate\Notifications\Messages\{MailMessage, SlackAttachment, SlackMessage};

/**
 * Class     CleanupWasSuccessfulNotification
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CleanupWasSuccessfulNotification extends AbstractNotification
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
        $message = static::makeMailMessage();

        $message->success()->subject(__('Clean up of :application_name backups successful', [
            'application_name' => $applicationName = $this->applicationName(),
        ]));

        $this->getBackupDestinations()->each(function (BackupDestination $destination) use ($applicationName, $message) {
            $message->line(__('The clean up of the :application_name backups on the disk named :disk_name was successful.', [
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
        $message = static::makeSlackMessage()
            ->success()
            ->content(__('Clean up of backups successful!'));

        $this->getBackupDestinations()->each(function (BackupDestination $destination) use ($message) {
            $message->attachment(function (SlackAttachment $attachment) use ($destination) {
                $attachment->fields(
                    $this->backupDestinationProperties($destination)->toArray()
                );
            });
        });

        return $message;
    }
}
