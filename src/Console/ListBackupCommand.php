<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Console;

use Arcanedev\LaravelBackup\Actions\Monitor\MonitorAction;
use Arcanedev\LaravelBackup\Entities\Backup;
use Arcanedev\LaravelBackup\Entities\BackupDestinationStatus;
use Arcanedev\LaravelBackup\Helpers\Format;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\TableStyle;

/**
 * Class     BackupListCommand
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ListBackupCommand extends Command
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var string */
    protected $signature = 'backup:list';

    /** @var string */
    protected $description = 'Display a list of all backups.';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function handle(MonitorAction $action)
    {
        $options = Arr::only($this->options(), [
            'disable-notifications',
        ]);

        /** @var  \Arcanedev\LaravelBackup\Actions\Monitor\MonitorPassable  $passable */
        $passable = $action->execute($options);

        $statuses = $passable->getAllStatuses();

        $this->displayOverview($statuses);
        $this->displayFailures($statuses);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * @param  \Illuminate\Support\Collection  $backupDestinationStatuses
     */
    protected function displayOverview(Collection $backupDestinationStatuses): void
    {
        $headers = ['Name', 'Disk', 'Reachable', 'Healthy', '# of backups', 'Newest backup', 'Used storage'];

        $rows = $backupDestinationStatuses->map(function (BackupDestinationStatus $backupDestinationStatus) {
            return $this->convertToRow($backupDestinationStatus);
        });

        $this->table($headers, $rows, 'default', [
            4 => static::rightAlignedTableStyle(),
            6 => static::rightAlignedTableStyle(),
        ]);
    }

    protected static function rightAlignedTableStyle(): TableStyle
    {
        return (new TableStyle)->setPadType(STR_PAD_LEFT);
    }

    /**
     * @param  \Arcanedev\LaravelBackup\Entities\BackupDestinationStatus  $backupDestinationStatus
     *
     * @return array
     */
    protected function convertToRow(BackupDestinationStatus $backupDestinationStatus): array
    {
        $destination = $backupDestinationStatus->backupDestination();

        $row = [
            $destination->backupName(),
            'disk'        => $destination->diskName(),
            Format::statusEmoji($destination->isReachable()),
            Format::statusEmoji($backupDestinationStatus->isHealthy()),
            'amount'      => $destination->backups()->count(),
            'newest'      => $this->getFormattedBackupDate($destination->newestBackup()),
            'usedStorage' => Format::humanReadableSize($destination->usedStorage()),
        ];

        if ( ! $destination->isReachable()) {
            foreach (['amount', 'newest', 'usedStorage'] as $propertyName) {
                $row[$propertyName] = '/';
            }
        }

        if ($backupDestinationStatus->getHealthCheckFailure() !== null) {
            $row['disk'] = '<error>'.$row['disk'].'</error>';
        }

        return $row;
    }

    /**
     * @param  \Illuminate\Support\Collection  $backupDestinationStatuses
     */
    protected function displayFailures(Collection $backupDestinationStatuses): void
    {
        $failed = $backupDestinationStatuses
            ->filter(function (BackupDestinationStatus $backupDestinationStatus) {
                return $backupDestinationStatus->getHealthCheckFailure() !== null;
            })
            ->map(function (BackupDestinationStatus $backupDestinationStatus) {
                return [
                    $backupDestinationStatus->backupDestination()->backupName(),
                    $backupDestinationStatus->backupDestination()->diskName(),
                    $backupDestinationStatus->getHealthCheckFailure()->healthCheck()->name(),
                    $backupDestinationStatus->getHealthCheckFailure()->exception()->getMessage(),
                ];
            });

        if ($failed->isNotEmpty()) {
            $this->warn('');
            $this->warn('Unhealthy backup destinations');
            $this->warn('-----------------------------');
            $this->table(['Name', 'Disk', 'Failed check', 'Description'], $failed->all());
        }
    }

    /**
     * @param  \Arcanedev\LaravelBackup\Entities\Backup|null  $backup
     *
     * @return string
     */
    protected function getFormattedBackupDate(Backup $backup = null): string
    {
        return is_null($backup)
            ? 'No backups present'
            : Format::ageInDays($backup->date());
    }
}
