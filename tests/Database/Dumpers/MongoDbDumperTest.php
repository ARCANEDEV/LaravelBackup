<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Database\Dumpers;

use Arcanedev\LaravelBackup\Database\Compressors\GzipCompressor;
use Arcanedev\LaravelBackup\Database\Dumpers\MongoDbDumper;
use Arcanedev\LaravelBackup\Exceptions\CannotStartDatabaseDump;

/**
 * Class     MongoDbDumperTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MongoDbDumperTest extends DumpTestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Database\Dumpers\MongoDbDumper */
    protected $dumper;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->dumper = new MongoDbDumper;
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_provides_a_factory_method()
    {
        static::assertInstanceOf(MongoDbDumper::class, $this->dumper);
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_credentials_are_set()
    {
        $this->expectException(CannotStartDatabaseDump::class);

        $this->dumper->dump('test.gz');
    }

    /** @test */
    public function it_can_generate_a_dump_command()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->getDumpCommand('dbname.gz');

        $expected = '\'mongodump\' --db dbname --archive --host localhost --port 27017 > "dbname.gz"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_gzip_compressor_enabled()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->useCompressor(new GzipCompressor)
            ->getDumpCommand('dbname.gz');

        $expected = '((((\'mongodump\' --db dbname --archive --host localhost --port 27017; echo $? >&3) | gzip > "dbname.gz") 3>&1) | (read x; exit $x))';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_absolute_path_having_space_and_brackets()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->getDumpCommand('/save/to/new (directory)/dbname.gz');

        $expected = '\'mongodump\' --db dbname --archive --host localhost --port 27017 > "/save/to/new (directory)/dbname.gz"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_username_and_password()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getDumpCommand('dbname.gz');

        $expected = '\'mongodump\' --db dbname --archive --username \'username\' --password \'password\' --host localhost --port 27017 > "dbname.gz"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_command_with_custom_host_and_port()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setHost('mongodb.test.com')
            ->setPort('27018')
            ->getDumpCommand('dbname.gz');

        $expected = '\'mongodump\' --db dbname --archive --host mongodb.test.com --port 27018 > "dbname.gz"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_backup_command_for_a_single_collection()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setCollection('mycollection')
            ->getDumpCommand('dbname.gz');

        $expected = '\'mongodump\' --db dbname --archive --host localhost --port 27017 --collection mycollection > "dbname.gz"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_custom_binary_path()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setDumpBinaryPath('/custom/directory')
            ->getDumpCommand('dbname.gz');

        $expected = '\'/custom/directory/mongodump\' --db dbname --archive --host localhost --port 27017 > "dbname.gz"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_authentication_database()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setAuthenticationDatabase('admin')
            ->getDumpCommand('dbname.gz');

        $expected = '\'mongodump\' --db dbname --archive --host localhost --port 27017 --authenticationDatabase admin > "dbname.gz"';

        static::assertSameCommand($expected, $actual);
    }
}
