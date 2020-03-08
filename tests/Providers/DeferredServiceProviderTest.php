<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Providers;

use Arcanedev\LaravelBackup\Actions\Cleanup\Strategies\CleanupStrategy;
use Arcanedev\LaravelBackup\Database\DbDumperManager;
use Arcanedev\LaravelBackup\Providers\DeferredServiceProvider;
use Arcanedev\LaravelBackup\Tests\TestCase;

/**
 * Class     DeferredServiceProviderTest
 *
 * @package  Arcanedev\LaravelBackup\Tests\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DeferredServiceProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Providers\DeferredServiceProvider */
    private $provider;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = $this->app->getProvider(DeferredServiceProvider::class);
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Illuminate\Support\ServiceProvider::class,
            \Arcanedev\Support\Providers\ServiceProvider::class,
            DeferredServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->provider);
        }
    }

    /** @test */
    public function it_can_provides()
    {
        $expected = [
            DbDumperManager::class,
            CleanupStrategy::class,
        ];

        static::assertEquals($expected, $this->provider->provides());
    }
}
