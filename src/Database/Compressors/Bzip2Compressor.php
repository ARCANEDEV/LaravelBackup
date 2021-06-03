<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database\Compressors;

use Arcanedev\LaravelBackup\Database\Contracts\Compressor;

/**
 * Class     Bzip2Compressor
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Bzip2Compressor implements Compressor
{
    /* -----------------------------------------------------------------
     |  Getters
     | -----------------------------------------------------------------
     */

    /**
     * Get the command name.
     *
     * @return string
     */
    public function useCommand(): string
    {
        return 'bzip2';
    }

    /**
     * Get the extension.
     *
     * @return string
     */
    public function usedExtension(): string
    {
        return 'bz2';
    }
}
