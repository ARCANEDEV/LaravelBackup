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
    ],

    /* -----------------------------------------------------------------
     |  Notifications
     | -----------------------------------------------------------------
     */

    'notifications' => [
        //
    ],

];
