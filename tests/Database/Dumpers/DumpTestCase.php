<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Database\Dumpers;

use Arcanedev\LaravelBackup\Tests\TestCase;

/**
 * Class     DumpTestCase
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class DumpTestCase extends TestCase
{
    /* -----------------------------------------------------------------
     |  Asserts
     | -----------------------------------------------------------------
     */

    /**
     * @param  string  $expected
     * @param  string  $actual
     */
    public function assertSameCommand(string $expected, string $actual)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $expected = str_replace('\'', '"', $expected);
        }

        static::assertSame($expected, $actual);
    }
}
