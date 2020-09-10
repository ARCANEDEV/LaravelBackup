<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Providers;

use Arcanedev\LaravelBackup\Actions\Cleanup\Strategies;
use Arcanedev\LaravelBackup\Database\DbDumperManager;
use Arcanedev\Support\Providers\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class     DeferredServiceProvider
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DeferredServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->singleton(DbDumperManager::class);
        $this->singleton(Strategies\CleanupStrategy::class, Strategies\DefaultStrategy::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            DbDumperManager::class,
            Strategies\CleanupStrategy::class,
        ];
    }
}
