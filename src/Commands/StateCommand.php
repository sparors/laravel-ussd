<?php

namespace Sparors\Ussd\Commands;

use Illuminate\Support\Facades\File;

class StateCommand extends GenerateCommand
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $namespace = config('ussd.state_namespace', 'App\Http\Ussd\States');
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
            $this->error('File already exists !');
        }
    }
}
