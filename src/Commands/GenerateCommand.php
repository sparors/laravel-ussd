<?php

namespace Sparors\Ussd\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateCommand extends Command
{
    protected function pathFromNamespace($namespace, $relativePath)
    {
        $extended_path = implode('\\', array_map(function ($value) {
            return ucfirst($value);
        }, explode(DIRECTORY_SEPARATOR, $relativePath)));

        $base_path = Str::replaceFirst(app()->getNamespace(), '', $namespace);
        $path = $base_path.DIRECTORY_SEPARATOR.$extended_path.'.php';

        return app('path').'/'.str_replace('\\', '/', $path);
    }

    protected function classNamespace($namespace, $relativePath)
    {
        $path = array_map(function ($value) {
            return ucfirst($value);
        }, explode(DIRECTORY_SEPARATOR, $relativePath));

        array_pop($path);

        return rtrim($namespace.'\\'.implode('\\', $path), '\\');
    }

    protected function className($relativePath)
    {
        $path = explode(DIRECTORY_SEPARATOR, $relativePath);

        return ucfirst(array_pop($path));
    }

    protected function ensureDirectoryExists($namespace, $relativePath)
    {
        $path = $this->pathFromNamespace($namespace, $relativePath);

        if (! File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0777, $recursive = true, $force = true);
        }
    }
}
