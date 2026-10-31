<?php

/*
 * SIMDESA - Smart Desa AI
 * Aplikasi ini dikembangkan berdasarkan OpenSID dan berlisensi GPL v3.
 */

namespace Modules\SmartDesaAi\Providers;

use Illuminate\Support\ServiceProvider;

class SmartDesaAiServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'SmartDesaAi';

    protected string $moduleNameLower = 'smartdesaai';

    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', $this->moduleNameLower);

        $this->loadViewsFrom(
            FCPATH . 'Modules' . DIRECTORY_SEPARATOR . $this->moduleName . DIRECTORY_SEPARATOR . 'Views',
            $this->moduleNameLower
        );
    }

    public function register(): void
    {
    }
}
