<?php

namespace Arcanedev\LaravelBackup\Tests;

use Arcanedev\LaravelBackup\BackupServiceProvider;

/**
 * Class     BackupServiceProviderTest
 *
 * @package  Arcanedev\LaravelBackup\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BackupServiceProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    private $provider;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = $this->app->make(BackupServiceProvider::class);
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
            \Arcanedev\Support\Providers\PackageServiceProvider::class,
            BackupServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->provider);
        }
    }
}
