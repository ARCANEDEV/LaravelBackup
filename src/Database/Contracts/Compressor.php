<?php

namespace Arcanedev\LaravelBackup\Database\Contracts;

/**
 * Interface     Compressor
 *
 * @package  Arcanedev\LaravelBackup\Database\Contracts
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface Compressor
{
    public function useCommand(): string;

    /**
     * Get the used extension.
     *
     * @return string
     */
    public function usedExtension(): string;
}
