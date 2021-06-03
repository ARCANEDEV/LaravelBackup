<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\EventSubscribers;

use Arcanedev\LaravelBackup\Actions\Backup\BackupPassable;
use Arcanedev\LaravelBackup\Entities\{BackupDestinationCollection, Notifiable};
use Arcanedev\LaravelBackup\Events\BackupActionHasFailed;
use Arcanedev\LaravelBackup\Notifications\BackupHasFailedNotification;
use Arcanedev\LaravelBackup\Tests\TestCase;
use Exception;
use Illuminate\Support\Facades\Notification;

/**
 * Class     BackupEventSubscriberTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupEventSubscriberTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_will_send_a_notification_by_default_when_a_backup_has_failed(): void
    {
        $this->fireBackupHasFailedEvent();

        Notification::assertSentTo(new Notifiable, BackupHasFailedNotification::class);
    }

    /**
     * @test
     *
     * @dataProvider channelProvider
     *
     * @param array $expectedChannels
     */
    public function it_will_send_a_notification_via_the_configured_notification_channels(array $expectedChannels): void
    {
        $this->app['config']->set('backup.notifications.supported.'.BackupHasFailedNotification::class, $expectedChannels);

        $this->fireBackupHasFailedEvent();

        Notification::assertSentTo(new Notifiable, BackupHasFailedNotification::class, function ($notification, $usedChannels) use ($expectedChannels) {
            return $expectedChannels == $usedChannels;
        });
    }

    /* -----------------------------------------------------------------
     |  Data Providers
     | -----------------------------------------------------------------
     */

    /**
     * @return array
     */
    public function channelProvider(): array
    {
        return [
            [[]],
            [['mail']],
            [['mail', 'slack']],
            [['mail', 'slack', 'discord']],
        ];
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    protected function fireBackupHasFailedEvent(): void
    {
        $exception = new Exception('Dummy exception');
        $passable  = (new BackupPassable([]))
            ->setBackupDestinations(BackupDestinationCollection::makeFromConfig());

        event(new BackupActionHasFailed($passable, $exception));
    }
}
