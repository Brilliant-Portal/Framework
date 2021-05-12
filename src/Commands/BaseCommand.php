<?php

namespace BrilliantPortal\Framework\Commands;

use Illuminate\Console\Command;

class BaseCommand extends Command
{
    protected $changedVendorFiles = [];

    /**
     * Display warnings for modified vendor files.
     *
     * @since 1.2.0
     *
     * @return void
     */
    public function maybeDisplayVendorErrors()
    {
        if ($this->changedVendorFiles) {
            $this->warn('Some of the vendor files overridden by BrilliantPortal Framework have been modified. Please open an issue and assign to the appropriate person.');
            $this->warn('Click the link below to start an issue, then copy-and-paste the table into the issue dsecription.');
            $this->newLine();
            $this->line('https://git.luminfire.net/products/brilliantportal/brilliantportal-framework/-/issues/new?issue[title]=Modified vendor files&issue[description]=These vendor files have been modified:');
            $this->table(
                ['Modified Files'],
                array_map(function ($file) {
                    return [$file];
                }, $this->changedVendorFiles)
            );
        }
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Check vendor file against known hash.
     *
     * @since 1.1.0
     *
     * @param string $path
     * @param string $expectedHash
     *
     * @return void
     */
    protected function checkFileHash($path, $expectedHash)
    {
        $actualHash = hash('sha256', file_get_contents(base_path($path)));

        if ($actualHash !== $expectedHash) {
            $this->changedVendorFiles[] = $path;
        }
    }
}
