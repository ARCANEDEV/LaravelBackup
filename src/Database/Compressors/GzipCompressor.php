<?php declare(strict_types=1);

namespace Arcanedev\LaravelBackup\Database\Compressors;

use Arcanedev\LaravelBackup\Database\Contracts\Compressor;

/**
 * Class     GzipCompressor
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class GzipCompressor implements Compressor
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
        return 'gzip';
    }

    /**
     * Get the extension.
     *
     * @return string
     */
    public function usedExtension(): string
    {
        return 'gz';
    }
}
