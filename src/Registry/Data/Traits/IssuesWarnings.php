<?php

declare(strict_types=1);

namespace Upmind\ProvisionBase\Registry\Data\Traits;

use Illuminate\Support\Facades\App;

trait IssuesWarnings
{
    protected function warning(string $text): void
    {
        if (App::runningInConsole()) {
            printf("WARNING: %s\n", $text);
        }
    }
}
