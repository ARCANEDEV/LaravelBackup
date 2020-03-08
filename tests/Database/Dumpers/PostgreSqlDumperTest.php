<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Database\Dumpers;

use Arcanedev\LaravelBackup\Database\Compressors\GzipCompressor;
use Arcanedev\LaravelBackup\Database\Dumpers\PostgreSqlDumper;
use Arcanedev\LaravelBackup\Exceptions\CannotSetDatabaseParameter;
use Arcanedev\LaravelBackup\Exceptions\CannotStartDatabaseDump;

/**
 * Class     PostgreSqlDumperTest
 *
 * @package  Arcanedev\LaravelBackup\Tests\Database\Dumpers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class PostgreSqlDumperTest extends DumpTestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Database\Dumpers\PostgreSqlDumper */
    protected $dumper;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->dumper = new PostgreSqlDumper;
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_provides_a_factory_method()
    {
        static::assertInstanceOf(PostgreSqlDumper::class, $this->dumper);
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_credentials_are_set()
    {
        $this->expectException(CannotStartDatabaseDump::class);

        $this->dumper->dump('test.sql');
    }

    /** @test */
    public function it_can_generate_a_dump_command()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getDumpCommand('dump.sql');

        $expected = '\'pg_dump\' -U username -h localhost -p 5432 > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_gzip_compressor_enabled()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useCompressor(new GzipCompressor)
            ->getDumpCommand('dump.sql');

        $expected = '((((\'pg_dump\' -U username -h localhost -p 5432; echo $? >&3) | gzip > "dump.sql") 3>&1) | (read x; exit $x))';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_absolute_path_having_space_and_brackets()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getDumpCommand('/save/to/new (directory)/dump.sql');

        $expected = '\'pg_dump\' -U username -h localhost -p 5432 > "/save/to/new (directory)/dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_using_inserts()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useInserts()
            ->getDumpCommand('dump.sql');

        $expected = '\'pg_dump\' -U username -h localhost -p 5432 --inserts > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_a_custom_port()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setPort('1234')
            ->getDumpCommand('dump.sql');

        $expected = '\'pg_dump\' -U username -h localhost -p 1234 > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_custom_binary_path()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setDumpBinaryPath('/custom/directory')
            ->getDumpCommand('dump.sql');

        $expected = '\'/custom/directory/pg_dump\' -U username -h localhost -p 5432 > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_a_custom_socket()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setSocket('/var/socket.1234')
            ->getDumpCommand('dump.sql');

        $expected = '\'pg_dump\' -U username -h /var/socket.1234 -p 5432 > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_for_specific_tables_as_array()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->includeTables(['tb1', 'tb2', 'tb3'])
            ->getDumpCommand('dump.sql');

        $expected = '\'pg_dump\' -U username -h localhost -p 5432 -t tb1 -t tb2 -t tb3 > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_for_specific_tables_as_string()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->includeTables('tb1, tb2, tb3')
            ->getDumpCommand('dump.sql');

        $expected = '\'pg_dump\' -U username -h localhost -p 5432 -t tb1 -t tb2 -t tb3 > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_exclude_tables_after_setting_tables()
    {
        $this->expectException(CannotSetDatabaseParameter::class);
        $this->expectExceptionMessage("Cannot set `excludeTables` because it conflicts with parameter `includeTables`");

        $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->includeTables('tb1, tb2, tb3')
            ->excludeTables('tb4, tb5, tb6');
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_tables_after_setting_exclude_tables()
    {
        $this->expectException(CannotSetDatabaseParameter::class);
        $this->expectExceptionMessage("Cannot set `includeTables` because it conflicts with parameter `excludeTables`");

        $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables('tb1, tb2, tb3')
            ->includeTables('tb4, tb5, tb6');
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_array()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables(['tb1', 'tb2', 'tb3'])
            ->getDumpCommand('dump.sql');

        $expected = '\'pg_dump\' -U username -h localhost -p 5432 -T tb1 -T tb2 -T tb3 > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_string()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables('tb1, tb2, tb3')
            ->getDumpCommand('dump.sql');

        $expected = '\'pg_dump\' -U username -h localhost -p 5432 -T tb1 -T tb2 -T tb3 > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_the_contents_of_a_credentials_file()
    {
        $credentials = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setHost('hostname')
            ->setPort('5432')
            ->getCredentials();

        $expected = 'hostname:5432:dbname:username:password';

        static::assertSame($expected, $credentials);
    }

    /** @test */
    public function it_can_get_the_name_of_the_db()
    {
        $dbDumper = $this->dumper->setDbName($dbName = 'testName');

        static::assertSame($dbName, $dbDumper->getDbName());
    }

    /** @test */
    public function it_can_add_an_extra_option()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('-something-else')
            ->getDumpCommand('dump.sql');

        $expected = '\'pg_dump\' -U username -h localhost -p 5432 -something-else > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_get_the_host()
    {
        $dumper = $this->dumper->setHost('myHost');

        static::assertSame('myHost', $dumper->getHost());
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_no_create_info()
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->doNotCreateTables()
            ->getDumpCommand('dump.sql');

        $expected = '\'pg_dump\' -U username -h localhost -p 5432 --data-only > "dump.sql"';

        static::assertSame($expected, $actual);
    }
}
