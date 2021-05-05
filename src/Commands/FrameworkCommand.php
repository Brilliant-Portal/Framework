<?php

namespace BrilliantPortal\Framework\Commands;

use Illuminate\Console\Command;

class FrameworkCommand extends Command
{
    public $signature = 'framework';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
