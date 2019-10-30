<?php

namespace Arcanedev\LaravelBackup\Database\Compressors;

use Arcanedev\LaravelBackup\Database\Contracts\Compressor;

/**
 * Class     GzipCompressor
 *
 * @package  Arcanedev\LaravelBackup\Database\Compressors
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
