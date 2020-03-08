<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Notifications;

use Arcanedev\LaravelBackup\Actions\Passable;
use Arcanedev\LaravelBackup\Entities\{BackupDestination, BackupDestinationStatusCollection};
use Illuminate\Notifications\Messages\{MailMessage, SlackMessage};
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * Class     AbstractNotification
 *
 * @package  Arcanedev\LaravelBackup\Notifications
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractNotification extends Notification
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var mixed|null */
    protected $event;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the event.
     *
     * @return mixed|null
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set the event.
     *
     * @param  mixed  $event
     *
     * @return $this
     */
    public function setEvent($event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\Notifiable|mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable): array
    {
        $notificationChannels = config('backup.notifications.supported.'.static::class, []);

        return array_filter($notificationChannels);
    }

    /**
     * Get the application name.
     *
     * @return string
     */
    public function applicationName(): string
    {
        return (string) (config('app.name') ?? config('app.url') ?? 'Laravel application');
    }

    /**
     * Get the action's passable.
     *
     * @return \Arcanedev\LaravelBackup\Actions\Passable
     */
    public function getPassable(): Passable
    {
        return $this->getEvent()->passable;
    }

    /**
     * Get the backup destinations.
     *
     * @return \Illuminate\Support\Collection|null
     */
    public function getBackupDestinations(): Collection
    {
        return $this->getPassable()->getBackupDestinations();
    }

    /**
     * Get the backup's statuses.
     *
     * @return \Arcanedev\LaravelBackup\Entities\BackupDestinationStatusCollection
     */
    public function getStatuses(): BackupDestinationStatusCollection
    {
        return $this->getEvent()->statuses;
    }

    /**
     * Get the event exception.
     *
     * @return \Exception|mixed
     */
    protected function getEventException()
    {
        return $this->getEvent()->exception;
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the backup destination's properties.
     *
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestination  $backupDestination
     *
     * @return \Illuminate\Support\Collection
     */
    protected function backupDestinationProperties(BackupDestination $backupDestination): Collection
    {
        if ( ! $backupDestination) {
            return new Collection;
        }

        $backupDestination->fresh();

        $newestBackup = $backupDestination->newestBackup();
        $oldestBackup = $backupDestination->oldestBackup();

        return Collection::make([
            __('Application name')   => $this->applicationName(),
            __('Backup name')        => $backupDestination->backupName(),
            __('Disk')               => $backupDestination->diskName(),
            __('Newest backup size') => $newestBackup ? $newestBackup->humanReadableSize() : __('No backups were made yet'),
            __('Number of backups')  => (string) $backupDestination->backups()->count(),
            __('Total storage used') => $backupDestination->backups()->humanReadableSize(),
            __('Newest backup date') => $newestBackup ? $newestBackup->date()->format('Y/m/d H:i:s') : __('No backups were made yet'),
            __('Oldest backup date') => $oldestBackup ? $oldestBackup->date()->format('Y/m/d H:i:s') : __('No backups were made yet'),
        ])->filter();
    }

    /**
     * Make a new mail message.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected static function makeMailMessage(): MailMessage
    {
        $address = config('backup.notifications.mail.from.address', config('mail.from.address'));
        $name    = config('backup.notifications.mail.from.name', config('mail.from.name'));

        return (new MailMessage)->from($address, $name);
    }

    /**
     * Make a new slack message.
     *
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    protected static function makeSlackMessage(): SlackMessage
    {
        $username = config('backup.notifications.slack.username');
        $icon     = config('backup.notifications.slack.icon');
        $channel  = config('backup.notifications.slack.channel');

        return (new SlackMessage)->from($username, $icon)->to($channel);
    }
}
