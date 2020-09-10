<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Database\Dumpers;

use Arcanedev\LaravelBackup\Database\Compressors\GzipCompressor;
use Arcanedev\LaravelBackup\Database\Dumpers\SqliteDumper;

/**
 * Class     SqliteDumperTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SqliteDumperTest extends DumpTestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Database\Dumpers\SqliteDumper */
    protected $dumper;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        static::initTempDirectory();
        static::copyStubsDatabasesInto();

        $this->dumper = new SqliteDumper;
    }

    protected function tearDown(): void
    {
        self::deleteTempDirectory();

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_provides_a_factory_method()
    {
        static::assertInstanceOf(SqliteDumper::class, $this->dumper);
    }

    /** @test */
    public function it_can_generate_a_dump_command()
    {
        $actual = $this->dumper
            ->setDbName('database.sqlite')
            ->getDumpCommand('dump.sql');

        $expected = "echo 'BEGIN IMMEDIATE;\n.dump' | 'sqlite3' --bail 'database.sqlite' > \"dump.sql\"";

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_gzip_compressor_enabled()
    {
        $actual = $this->dumper
            ->setDbName('database.sqlite')
            ->useCompressor(new GzipCompressor)
            ->getDumpCommand('dump.sql');

        $expected = '((((echo \'BEGIN IMMEDIATE;
.dump\' | \'sqlite3\' --bail \'database.sqlite\'; echo $? >&3) | gzip > "dump.sql") 3>&1) | (read x; exit $x))';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_absolute_paths()
    {
        $actual = $this->dumper
            ->setDbName('/path/to/database.sqlite')
            ->setDumpBinaryPath('/usr/bin')
            ->getDumpCommand('/save/to/dump.sql');

        $expected = "echo 'BEGIN IMMEDIATE;\n.dump' | '/usr/bin/sqlite3' --bail '/path/to/database.sqlite' > \"/save/to/dump.sql\"";

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_absolute_paths_having_space_and_brackets()
    {
        $actual = $this->dumper
            ->setDbName('/path/to/database.sqlite')
            ->setDumpBinaryPath('/usr/bin')
            ->getDumpCommand('/save/to/new (directory)/dump.sql');

        $expected = "echo 'BEGIN IMMEDIATE;\n.dump' | '/usr/bin/sqlite3' --bail '/path/to/database.sqlite' > \"/save/to/new (directory)/dump.sql\"";

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_successfully_creates_a_backup()
    {
        $dbPath       = static::getTempDirectory('databases/database.sqlite');
        $dbBackupPath = static::getTempDirectory('databases/backup.sql');

        $this->dumper
            ->setDbName($dbPath)
            ->useCompressor(new GzipCompressor)
            ->dump($dbBackupPath);

        static::assertFileExists($dbBackupPath);
        static::assertGreaterThan(0, filesize($dbBackupPath), 'Sqlite dump cannot be empty');
    }
}
