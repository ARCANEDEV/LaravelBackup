<?php

return [

    /* -----------------------------------------------------------------
     |  Main Settings
     | -----------------------------------------------------------------
     */

    'name'   => env('APP_NAME', 'laravel-backup'),

    /* -----------------------------------------------------------------
     |  Backup Destinations
     | -----------------------------------------------------------------
     */

    'destination' => [

        'filename-prefix' => '',

        'disks' => [
            'local',
        ],

    ],

    /* -----------------------------------------------------------------
     |  Backup action
     | -----------------------------------------------------------------
     */

    'backup'        => [

        'source' => [
            'files' => [
                'include'      => [
                    base_path(),
                ],

                'exclude'      => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],

                'follow-links' => false,
            ],

            'databases' => [
                'mysql',
            ],
        ],

        'temporary-directory' => storage_path('app/_backup-temp'),

        'tasks' => [
            Arcanedev\LaravelBackup\Actions\Backup\Tasks\CheckOptions::class,
            Arcanedev\LaravelBackup\Actions\Backup\Tasks\CheckBackupDestinations::class,
            Arcanedev\LaravelBackup\Actions\Backup\Tasks\CreateTemporaryDirectory::class,
            Arcanedev\LaravelBackup\Actions\Backup\Tasks\PrepareFilesToBackup::class,
            Arcanedev\LaravelBackup\Actions\Backup\Tasks\CreateBackupFile::class,
            Arcanedev\LaravelBackup\Actions\Backup\Tasks\MoveBackupToDisks::class,
        ],

    ],

    /* -----------------------------------------------------------------
     |  Cleanup action
     | -----------------------------------------------------------------
     */

    'cleanup'       => [

        'strategy' => [
            'default'      => Arcanedev\LaravelBackup\Actions\Cleanup\Strategies\DefaultStrategy::class,

            'keep-backups' => [
                /**
                 * The number of days for which backups must be kept.
                 */
                'all'     => 7,

                /**
                 * The number of days for which daily backups must be kept.
                 */
                'daily'   => 16,

                /**
                 * The number of weeks for which one weekly backup must be kept.
                 */
                'weekly'  => 8,

                /**
                 * The number of months for which one monthly backup must be kept.
                 */
                'monthly' => 4,

                /**
                 * The number of years for which one yearly backup must be kept.
                 */
                'yearly'  => 2,
            ],

            /*
             * After cleaning up the backups remove the oldest backup until this amount of megabytes has been reached.
             */
            'delete-backups' => [
                'oldest-when-size-reach' => 5000,
            ],
        ],

        'tasks' => [
            Arcanedev\LaravelBackup\Actions\Cleanup\Tasks\CheckBackupDestinations::class,
            Arcanedev\LaravelBackup\Actions\Cleanup\Tasks\ApplyCleanupStrategy::class,
        ],

    ],

    /* -----------------------------------------------------------------
     |  Monitor action
     | -----------------------------------------------------------------
     */

    'monitor'       => [

        'destinations' => [
            [
                'name'          => env('APP_NAME', 'laravel-backup'),
                'disks'         => ['local'],
                'health-checks' => [
                    Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\MaximumAgeInDays::class          => [1],
                    Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\MaximumStorageInMegabytes::class => [5000],
                ],
            ],

            /*
             [
                'name' => 'name of the second app',
                'disks' => ['local', 's3'],
                'health_checks' => [
                    Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\MaximumAgeInDays::class          => 1,
                    Arcanedev\LaravelBackup\Actions\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
                ],
            ],
            */
        ],

        'tasks' => [
            Arcanedev\LaravelBackup\Actions\Monitor\Tasks\CheckBackupsHealth::class,
        ],

    ],

    /* -----------------------------------------------------------------
     |  Notifications
     | -----------------------------------------------------------------
     */

    'notifications' => [

        'supported' => [
            Arcanedev\LaravelBackup\Notifications\BackupWasSuccessfulNotification::class      => ['mail'],
            Arcanedev\LaravelBackup\Notifications\BackupHasFailedNotification::class          => ['mail'],

            Arcanedev\LaravelBackup\Notifications\CleanupWasSuccessfulNotification::class     => ['mail'],
            Arcanedev\LaravelBackup\Notifications\CleanupHasFailedNotification::class         => ['mail'],

            Arcanedev\LaravelBackup\Notifications\HealthyBackupsWasFoundNotification::class   => ['mail'],
            Arcanedev\LaravelBackup\Notifications\UnhealthyBackupsWasFoundNotification::class => ['mail'],
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent. The default
         * notifiable will use the variables specified in this config file.
         */
        'notifiable' => Arcanedev\LaravelBackup\Entities\Notifiable::class,

        'mail' => [
            'to'   => 'your@example.com',
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name'    => env('MAIL_FROM_NAME', 'Example'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',

            /* If this is set to null the default channel of the webhook will be used. */
            'channel'     => null,
            'username'    => null,
            'icon'        => null,
        ],

    ],

    /* -----------------------------------------------------------------
     |  Events
     | -----------------------------------------------------------------
     */

    'events'        => [

        // Backup Action
        Arcanedev\LaravelBackup\Events\BackupActionWasSuccessful::class  => [
            Arcanedev\LaravelBackup\Listeners\SendBackupWasSuccessfulNotification::class,
        ],
        Arcanedev\LaravelBackup\Events\BackupActionHasFailed::class      => [
            Arcanedev\LaravelBackup\Listeners\SendBackupHasFailedNotification::class
        ],

        // Cleanup Action
        Arcanedev\LaravelBackup\Events\CleanupActionWasSuccessful::class => [
            Arcanedev\LaravelBackup\Listeners\SendCleanupWasSuccessfulNotification::class
        ],
        Arcanedev\LaravelBackup\Events\CleanupActionHasFailed::class     => [
            Arcanedev\LaravelBackup\Listeners\SendCleanupHasFailedNotification::class
        ],

        // Monitor Action
        Arcanedev\LaravelBackup\Events\HealthyBackupsWasFound::class      => [
            Arcanedev\LaravelBackup\Listeners\SendHealthyBackupWasFoundNotification::class
        ],
        Arcanedev\LaravelBackup\Events\UnhealthyBackupsWasFound::class    => [
            Arcanedev\LaravelBackup\Listeners\SendUnhealthyBackupWasFoundNotification::class
        ],

    ],

];
