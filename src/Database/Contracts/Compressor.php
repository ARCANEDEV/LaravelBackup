<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database\Contracts;

/**
 * Interface  Compressor
 *
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface Compressor
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the compressor command.
     *
     * @return string
     */
    public function useCommand(): string;

    /**
     * Get the used extension.
     *
     * @return string
     */
    public function usedExtension(): string;
}
