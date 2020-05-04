<?php

namespace Sparors\Ussd\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ussd:state {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new ussd state';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $namespace = config('ussd.class_namespace', 'App\Http\Ussd');
        $name = $this->argument('name');

        if (! File::exists($this->pathFromNamespace($namespace, $name))) {
            $content = preg_replace_array(
                ['/\[namespace\]/', '/\[class\]/'],
                [$this->classNamespace($namespace, $name), $this->className($name)],
                file_get_contents(__DIR__.'/state.stub')
            );

            $this->ensureDirectoryExists($namespace, $name);
            File::put($this->pathFromNamespace($namespace, $name), $content);

            $this->info($this->className($name).' state created successfully');
        } else {
            $this->error('File Already exists !');
        }
    }

    private function pathFromNamespace($namespace, $relativePath)
    {
        $extended_path = implode('\\', array_map(function ($value) {
            return ucfirst($value); 
        }, explode(DIRECTORY_SEPARATOR, $relativePath)));

        $base_path = Str::replaceFirst(app()->getNamespace(), '', $namespace);
        $path = $base_path.DIRECTORY_SEPARATOR.$extended_path.'.php';

        return app('path').'/'.str_replace('\\', '/', $path);
    }

    private function classNamespace($namespace, $relativePath)
    {
        $path = array_map(function ($value) {
            return ucfirst($value); 
        }, explode(DIRECTORY_SEPARATOR, $relativePath));

        array_pop($path);

        return rtrim($namespace.'\\'.implode('\\', $path), '\\');
    }

    private function className($relativePath)
    {
        $path = explode(DIRECTORY_SEPARATOR, $relativePath);

        return ucfirst(array_pop($path));
    }

    private function ensureDirectoryExists($namespace, $relativePath)
    {
        $path = $this->pathFromNamespace($namespace, $relativePath);

        if (! File::isDirectory(dirname($path))) {
            File::makeDirectory(dirname($path), 0777, $recursive = true, $force = true);
        }
    }
}
