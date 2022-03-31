<?php

namespace Sparors\Ussd\Commands;

use Illuminate\Support\Facades\File;

/** @since v2.0.0 */
class ActionCommand extends GenerateCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ussd:action {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new ussd action';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $namespace = config('ussd.action_namespace', 'App\Http\Ussd\Actions');
        $name = $this->argument('name');

        if (! File::exists($this->pathFromNamespace($namespace, $name))) {
            $content = preg_replace_array(
                ['/\[namespace\]/', '/\[class\]/'],
                [$this->classNamespace($namespace, $name), $this->className($name)],
                file_get_contents(__DIR__.'/action.stub')
            );

            $this->ensureDirectoryExists($namespace, $name);
            File::put($this->pathFromNamespace($namespace, $name), $content);

            $this->info($this->className($name).' action created successfully');
        } else {
            $this->error('File already exists !');
        }
    }
}
