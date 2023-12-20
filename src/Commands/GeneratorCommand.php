<?php

namespace Sparors\Ussd\Commands;

use Illuminate\Console\GeneratorCommand as BaseCommand;

abstract class GeneratorCommand extends BaseCommand
{
    protected function extendNamespace(string $extension): string
    {
        $namespace = trim(config('ussd.namespace', 'App\Ussd'), '\\');

        return "{$namespace}\\{$extension}";
    }
}
