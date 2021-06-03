<?php

return [

    /* -----------------------------------------------------------------
     |  Main Settings
     | -----------------------------------------------------------------
     */

    /*
     * The name of this application. You can use this name to monitor the backups.
     */
    'name'   => env('APP_NAME', 'laravel-backup'),

    /* -----------------------------------------------------------------
     |  Backup Destinations
     | -----------------------------------------------------------------
     */

    'destination' => [

        // The filename prefix used for the backup zip file.
        'filename-prefix' => '',

        // The disk names on which the backups will be stored.
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

                // The list of directories and files that will be included in the backup.
                'include'      => [
                    base_path(),
                ],

                /*
                 * These directories and files will be excluded from the backup.
                 *
                 * Directories used by the backup process will automatically be excluded.
                 */
                'exclude'      => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],

                // Determines if symlinks should be followed.
                'follow-links' => false,

                // Determines if it should avoid unreadable folders.
                'ignore-unreadable-directories' => false,

                /*
                 * This path is used to make directories in resulting zip-file relative
                 * Set to `null` to include complete absolute path
                 * Example: base_path()
                 */
                'relative-path' => null,

            ],

            /*
             * The names of the connections to the databases that should be backed up
             * MySQL, PostgreSQL, SQLite and Mongo databases are supported.
             *
             * The content of the database dump may be customized for each connection
             * by adding a 'dump' key to the connection settings in config/database.php.
             * E.g.
             * 'mysql' => [
             *       ...
             *      'dump' => [
             *           'excludeTables' => [
             *                'table_to_exclude_from_backup',
             *                'another_table_to_exclude'
             *            ]
             *       ],
             * ],
             *
             * If you are using only InnoDB tables on a MySQL server, you can
             * also supply the useSingleTransaction option to avoid table locking.
             *
             * E.g.
             * 'mysql' => [
             *       ...
             *      'dump' => [
             *           'useSingleTransaction' => true,
             *       ],
             * ],
             *
             * For a complete list of available customization options, see https://github.com/spatie/db-dumper
             */
            'databases' => [
                'mysql',
            ],

        ],

        'db-dump' => [
            /*
             * The database dump can be compressed to decrease disk space usage.
             *
             * Out of the box LaravelBackup supplies:
             * Arcanedev\LaravelBackup\Database\Compressors\GzipCompressor::class
             *
             * You can also use a custom compressor by implementing the contract:
             * Arcanedev\LaravelBackup\Database\Contracts\Compressor
             *
             * If you do not want any compressor at all, set it to `null`.
             */
            'compressor' => null,

            /*
             * The file extension used for the database dump files.
             *
             * If not specified, the file extension will be `.archive` for MongoDB and `.sql` for all other databases
             * The file extension should be specified without a leading `.`
             */
            'file-extension' => '',
        ],

        // The directory where the temporary files will be stored.
        'temporary-directory' => storage_path('app/_backup-temp'),

        /*
         * The password to be used for archive encryption.
         * Set to `null` to disable encryption.
         */
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),

        /*
         * The encryption algorithm to be used for archive encryption.
         * You can set it to `null` or `false` to disable encryption.
         *
         * When set to 'default', we'll use ZipArchive::EM_AES_256 if it is
         * available on your system.
         */
        'encryption' => 'default',

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

    /*
     * The strategy that will be used to cleanup old backups. The default strategy will keep all backups
     * for a certain amount of days. After that period only a daily backup will be kept.
     * After that period only weekly backups will be kept and so on.
     *
     * No matter how you configure it the default strategy will never delete the newest backup.
     */
    'cleanup'       => [

        'strategy' => [
            'default'      => Arcanedev\LaravelBackup\Actions\Cleanup\Strategies\DefaultStrategy::class,

            'keep-backups' => [
                // The number of days for which backups must be kept.
                'all'     => 7,

                // The number of days for which daily backups must be kept.
                'daily'   => 16,

                // The number of weeks for which one weekly backup must be kept.
                'weekly'  => 8,

                // The number of months for which one monthly backup must be kept.
                'monthly' => 4,

                // The number of years for which one yearly backup must be kept.
                'yearly'  => 2,
            ],

            // After cleaning up the backups remove the oldest backup until this amount of megabytes has been reached.
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

    /*
     * Here you can specify which backups should be monitored.
     * If a backup does not meet the specified requirements the UnhealthyBackupsWasFound event will be fired.
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

    /*
     * You can get notified when specific events occur. Out of the box you can use 'mail' and 'slack'.
     *
     * For 'slack' you need to install 'laravel/slack-notification-channel'.
     *
     * You can also use your own notification classes, just make sure the class is named after one of
     * the `Arcanedev\LaravelBackup\Events` classes.
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

            // If this is set to null the default channel of the webhook will be used.
            'channel'     => null,
            'username'    => null,
            'icon'        => null,
        ],

        'discord' => [
            'webhook_url' => '',

            'username'    => null,
            'avatar_url'  => null,
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
        Arcanedev\LaravelBackup\Events\BackupZipWasCreated::class => [
            Arcanedev\LaravelBackup\Listeners\EncryptBackupArchive::class,
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
