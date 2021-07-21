<?php

namespace BrilliantPortal\Framework\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class BaseCommand extends Command
{
    protected Filesystem $filesystem;
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
     * Get the filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    private function getFilesystem(): Filesystem
    {
        if (! isset($this->filesystem)) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $path
     * @param  string  $content
     * @return void
     */
    protected function appendToFile($path, $content)
    {
        $this->getFilesystem()->append($path, $content);
    }

    /**
     * Append content to .env and .env.example.
     *
     * @param string[] $content
     *
     * @return void
     */
    protected function appendToEnv(...$content): void
    {
        $existingEnvContent = $this->getFilesystem()->get('.env');
        $existingExampleContent = $this->getFilesystem()->get('.env');

        $envContent = collect($content)
            ->filter(function ($string) use ($existingEnvContent) {
                return ! Str::contains($existingEnvContent, $string);
            })
            ->join(PHP_EOL);

        $exampleContent = collect($content)
            ->filter(function ($string) use ($existingExampleContent) {
                return ! Str::contains($existingExampleContent, $string);
            })
            ->join(PHP_EOL);

        if (! empty($envContent)) {
            $this->appendToFile(base_path('.env'), PHP_EOL.$envContent.PHP_EOL);
        }
        if (! empty($exampleContent)) {
            $this->appendToFile(base_path('.env.example'), PHP_EOL.$exampleContent.PHP_EOL);
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
        $this->getFilesystem()->replaceInFile($search, $replace, $path);
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
