<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Notifications;

use Arcanedev\LaravelBackup\Entities\BackupDestination;
use Illuminate\Notifications\Messages\{MailMessage, SlackAttachment, SlackMessage};

/**
 * Class     BackupWasSuccessfulNotification
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupWasSuccessfulNotification extends AbstractNotification
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
        return tap(static::makeMailMessage(), function (MailMessage $message) {
            $message->subject(__('Successful new backup of :application_name', [
                'application_name' => $this->applicationName(),
            ]));

            $this->getBackupDestinations()->each(function (BackupDestination $destination) use ($message) {
                $message->line(__('Great news, a new backup of :application_name was successfully created on the disk named :disk_name.', [
                    'application_name' => $this->applicationName(),
                    'disk_name'        => $destination->diskName(),
                ]));

                $this->backupDestinationProperties($destination)->each(function ($value, $name) use ($message) {
                    $message->line("{$name}: $value");
                });
            });
        });
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
            ->content(__('Successful new backup!'));

        $this->getBackupDestinations()->each(function (BackupDestination $destination) use ($message) {
            $message->attachment(function (SlackAttachment $attachment) use ($destination) {
                $attachment->fields($this->backupDestinationProperties($destination)->toArray());
            });
        });

        return $message;
    }
}
