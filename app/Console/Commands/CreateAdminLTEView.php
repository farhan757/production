<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CreateAdminLTEView extends Command
{
    protected $filesystem;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view {name : the name of the view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Arfi Custom view for Create a new view using adminlte default layout';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->filesystem->put(
                $path = resource_path('views/' . $this->viewPath()),
                $this->filesystem->get($this->getStubPath()),
                0
            );
            $this->info('File ' . $path . ' created');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function getStubPath()
    {
        return __DIR__ . '/stubs/view.blade.php';
    }

    protected function viewPath()
    {
        return $this->constructViewBladeName($this->argument('name'));
    }

    protected function constructViewBladeName($name)
    {
        return $this->dottedPathToSlashesPath($name) . '.blade.php';
    }

    protected function dottedPathToSlashesPath($name)
    {
        return str_replace(".", "/", $name);
    }
}
