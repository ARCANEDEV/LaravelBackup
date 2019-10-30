<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Helpers;

use Arcanedev\LaravelBackup\Helpers\Format;
use Arcanedev\LaravelBackup\Tests\TestCase;
use Carbon\Carbon;

/**
 * Class     FormatTest
 *
 * @package  Arcanedev\LaravelBackup\Tests\Helpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class FormatTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /**
     * @test
     *
     * @dataProvider getFileSizesDataProvider
     *
     * @param  int|float  $size
     * @param  string     $expected
     */
    public function it_can_determine_a_human_readable_file_size(float $size, string $expected)
    {
        static::assertEquals($expected, Format::humanReadableSize($size));
    }

    /** @test */
    public function it_can_determine_the_age_in_days()
    {
        Carbon::setTestNow(Carbon::create(2016, 1, 1)->startOfDay());

        static::assertEquals('0.00 (1 second ago)', Format::ageInDays(Carbon::now()));
        static::assertEquals('0.04 (1 hour ago)', Format::ageInDays(Carbon::now()->subHours(1)));
        static::assertEquals('1.04 (1 day ago)', Format::ageInDays(Carbon::now()->subHours(1)->subDays(1)));
        static::assertEquals('30.04 (4 weeks ago)', Format::ageInDays(Carbon::now()->subHours(1)->subMonths(1)));
    }

    /* -----------------------------------------------------------------
     |  Data Providers
     | -----------------------------------------------------------------
     */

    /** @return array */
    public function getFileSizesDataProvider(): array
    {
        return [
            [10,          '10 B'],
            [100,         '100 B'],
            [1000,        '1000 B'],
            [10000,       '9.77 KB'],
            [1000000,     '976.56 KB'],
            [10000000,    '9.54 MB'],
            [100000000,   '95.37 MB'],
            [1000000000,  '953.67 MB'],
            [10000000000, '9.31 GB'],
        ];
    }
}
