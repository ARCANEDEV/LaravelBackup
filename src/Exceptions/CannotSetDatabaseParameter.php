<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Exceptions;

use Exception;

/**
 * Class     CannotSetDatabaseParameter
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CannotSetDatabaseParameter extends Exception
{
    /**
     * @param string $name
     * @param string $conflictName
     *
     * @return static
     */
    public static function conflictingParameters($name, $conflictName): self
    {
        return new static(
            __("Cannot set `:name` because it conflicts with parameter `:conflict_name`", [
                'name'          => $name,
                'conflict_name' => $conflictName,
            ])
        );
    }
}
