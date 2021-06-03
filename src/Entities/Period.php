<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Entities;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class     Period
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Period
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var \Carbon\Carbon */
    protected $startDate;

    /** @var \Carbon\Carbon */
    protected $endDate;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Period constructor.
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     */
    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /* -----------------------------------------------------------------
     |  Getters
     | -----------------------------------------------------------------
     */

    /**
     * Get the start date.
     *
     * @return \Carbon\Carbon
     */
    public function startDate(): Carbon
    {
        return $this->startDate->copy();
    }

    /**
     * Get the end date.
     *
     * @return \Carbon\Carbon
     */
    public function endDate(): Carbon
    {
        return $this->endDate->copy();
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make the period ranges.
     *
     * @param  array  $periods
     *
     * @return \Illuminate\Support\Collection
     */
    public static function makeRanges(array $periods): Collection
    {
        $daily = new static(
            Carbon::now()->subDays($periods['all']),
            Carbon::now()->subDays($periods['all'])->subDays($periods['daily'])
        );

        $weekly = new static(
            $daily->endDate(),
            $daily->endDate()->subWeeks($periods['weekly'])
        );

        $monthly = new static(
            $weekly->endDate(),
            $weekly->endDate()->subMonths($periods['monthly'])
        );

        $yearly = new static(
            $monthly->endDate(),
            $monthly->endDate()->subYears($periods['yearly'])
        );

        return Collection::make(compact('daily', 'weekly', 'monthly', 'yearly'));
    }

    /**
     * Get the period format.
     *
     * @param  string  $key
     *
     * @return string
     */
    public static function format(string $key): string
    {
        $formats = [
            'daily'   => 'Ymd',
            'weekly'  => 'YW',
            'monthly' => 'Ym',
            'yearly'  => 'Y',
        ];

        return $formats[$key];
    }
}
