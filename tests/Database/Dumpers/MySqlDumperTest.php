<?php

declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Tests\Database\Dumpers;

use Arcanedev\LaravelBackup\Database\Compressors\GzipCompressor;
use Arcanedev\LaravelBackup\Database\Dumpers\MySqlDumper;
use Arcanedev\LaravelBackup\Exceptions\{CannotSetDatabaseParameter, CannotStartDatabaseDump};

/**
 * Class     MySqlDumperTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MySqlDumperTest extends DumpTestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Database\Dumpers\MySqlDumper */
    protected $dumper;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->dumper = new MySqlDumper;
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_provides_a_factory_method(): void
    {
        static::assertInstanceOf(MySqlDumper::class, $this->dumper);
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_credentials_are_set(): void
    {
        $this->expectException(CannotStartDatabaseDump::class);

        $this->dumper->dump('test.sql');
    }

    /** @test */
    public function it_can_generate_a_dump_command(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_column_statistics(): void
    {
        $dumpCommand = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->doNotUseColumnStatistics()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --column-statistics=0 dbname > "dump.sql"';

        static::assertSameCommand($expected, $dumpCommand);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_gzip_compressor_enabled(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useCompressor(new GzipCompressor)
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '((((\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert dbname; echo $? >&3) | gzip > "dump.sql") 3>&1) | (read x; exit $x))';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_absolute_path_having_space_and_brackets(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->getDumpCommand('/save/to/new (directory)/dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert dbname > "/save/to/new (directory)/dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_without_using_comments(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dontSkipComments()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --extended-insert dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_without_using_extended_insterts(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dontUseExtendedInserts()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --skip-extended-insert dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_custom_binary_path(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setDumpBinaryPath('/custom/directory')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'/custom/directory/mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_without_using_extending_inserts(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->dontUseExtendedInserts()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --skip-extended-insert dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_using_single_transaction(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useSingleTransaction()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --single-transaction dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_using_skip_lock_tables(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->skipLockTables()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --skip-lock-tables dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_using_quick(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->useQuick()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --quick dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_a_custom_socket(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setSocket(1234)
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --socket=1234 dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_for_specific_tables_as_array(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->includeTables(['tb1', 'tb2', 'tb3'])
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert dbname --tables tb1 tb2 tb3 > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_for_specific_tables_as_string(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->includeTables('tb1 tb2 tb3')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert dbname --tables tb1 tb2 tb3 > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_exclude_tables_after_setting_tables(): void
    {
        $this->expectException(CannotSetDatabaseParameter::class);

        $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->includeTables('tb1 tb2 tb3')
            ->excludeTables('tb4 tb5 tb6');
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_array(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables(['tb1', 'tb2', 'tb3'])
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --ignore-table=dbname.tb1 --ignore-table=dbname.tb2 --ignore-table=dbname.tb3 dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_string(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables('tb1, tb2, tb3')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --ignore-table=dbname.tb1 --ignore-table=dbname.tb2 --ignore-table=dbname.tb3 dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_will_throw_an_exception_when_setting_tables_after_setting_exclude_tables(): void
    {
        $this->expectException(CannotSetDatabaseParameter::class);

        $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->excludeTables('tb1 tb2 tb3')
            ->includeTables('tb4 tb5 tb6');
    }

    /** @test */
    public function it_can_generate_the_contents_of_a_credentials_file(): void
    {
        $credentialsFileContent = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setHost('hostname')
            ->setSocket(1234)
            ->getDbCredentials();

        $expected = '[client]'.PHP_EOL."user = 'username'".PHP_EOL."password = 'password'".PHP_EOL."host = 'hostname'".PHP_EOL."port = '3306'";

        static::assertSame($expected, $credentialsFileContent);
    }

    /** @test */
    public function it_can_get_the_name_of_the_db(): void
    {
        $dbDumper = $this->dumper->setDbName($dbName = 'testName');

        static::assertSame($dbName, $dbDumper->getDbName());
    }

    /** @test */
    public function it_can_add_extra_options(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('--extra-option')
            ->addExtraOption('--another-extra-option="value"')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --extra-option --another-extra-option="value" dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_get_the_host(): void
    {
        $dumper = $this->dumper->setHost('myHost');

        static::assertSame('myHost', $dumper->getHost());
    }

    /** @test */
    public function it_can_set_db_name_as_an_extra_options(): void
    {
        $actual = $this->dumper
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('--extra-option')
            ->addExtraOption('--another-extra-option="value"')
            ->addExtraOption('--databases dbname')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --extra-option --another-extra-option="value" --databases dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_get_the_name_of_the_db_when_dbname_was_set_as_an_extra_option(): void
    {
        $dbName = 'testName';
        $dbDumper = $this->dumper->addExtraOption("--databases {$dbName}");

        static::assertSame($dbName, $dbDumper->getDbName());
    }

    /** @test */
    public function it_can_get_the_name_of_the_db_when_dbname_was_overridden_as_an_extra_option(): void
    {
        $dbDumper = $this->dumper
            ->setDbName('testName')
            ->addExtraOption("--databases otherName");

        static::assertSame('otherName', $dbDumper->getDbName());
    }

    /** @test */
    public function it_can_get_the_name_of_the_db_when_all_databases_was_set_as_an_extra_option(): void
    {
        $actual = $this->dumper
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('--all-databases')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --all-databases > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_array_when_dbname_was_set_as_an_extra_option(): void
    {
        $actual = $this->dumper
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('--databases dbname')
            ->excludeTables(['tb1', 'tb2', 'tb3'])
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --ignore-table=dbname.tb1 --ignore-table=dbname.tb2 --ignore-table=dbname.tb3 --databases dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_excluding_tables_as_string_when_dbname_was_set_as_an_extra_option(): void
    {
        $actual = $this->dumper
            ->setUserName('username')
            ->setPassword('password')
            ->addExtraOption('--databases dbname')
            ->excludeTables('tb1, tb2, tb3')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --ignore-table=dbname.tb1 --ignore-table=dbname.tb2 --ignore-table=dbname.tb3 --databases dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_set_gtid_purged(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->setGtidPurged('OFF')
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --skip-comments --extended-insert --set-gtid-purged=OFF dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }

    /** @test */
    public function it_can_generate_a_dump_command_with_no_create_info(): void
    {
        $actual = $this->dumper
            ->setDbName('dbname')
            ->setUserName('username')
            ->setPassword('password')
            ->doNotCreateTables()
            ->getDumpCommand('dump.sql', 'credentials.txt');

        $expected = '\'mysqldump\' --defaults-extra-file="credentials.txt" --no-create-info --skip-comments --extended-insert dbname > "dump.sql"';

        static::assertSameCommand($expected, $actual);
    }
}
