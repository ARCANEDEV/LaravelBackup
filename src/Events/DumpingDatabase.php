<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Events;

use Arcanedev\LaravelBackup\Database\Dumpers\AbstractDumper;

/**
 * Class     DumpingDatabase
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class DumpingDatabase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelBackup\Database\Dumpers\AbstractDumper */
    public $dumper;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * DumpingDatabase constructor.
     *
     * @param  \Arcanedev\LaravelBackup\Database\Dumpers\AbstractDumper  $dumper
     */
    public function __construct(AbstractDumper $dumper)
    {
        $this->dumper = $dumper;
    }
}
