<?php

namespace BrilliantPortal\Framework\Commands;

use Illuminate\Console\Command;

class PublishBrandingCommand extends Command
{
    public $signature = 'brilliant-portal:publish-branding';

    public $description = 'Publish Jetstream’s branding components to your app';

    public function handle()
    {
        if (! file_exists(resource_path('views/vendor/jetstream/components/'))) {
            mkdir(resource_path('views/vendor/jetstream/components/'), 0755, true);
        }

        copy(base_path('vendor/laravel/jetstream/resources/views/components/application-logo.blade.php'), resource_path('views/vendor/jetstream/components/application-logo.blade.php'));
        copy(base_path('vendor/laravel/jetstream/resources/views/components/authentication-card-logo.blade.php'), resource_path('views/vendor/jetstream/components/authentication-card-logo.blade.php'));
        copy(base_path('vendor/laravel/jetstream/resources/views/components/application-mark.blade.php'), resource_path('views/vendor/jetstream/components/application-mark.blade.php'));

        return self::SUCCESS;
    }
}
