<?php

namespace Laratomics\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laratomics:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all the Laratomics-workshop resoureces';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment('Publishing Laratomics Workshop Assets...');
        $this->callSilent('vendor:publish', ['--tag' => 'workshop-assets']);

        $this->comment('Publishing Laratomics Workshop Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'workshop-config']);

        $this->comment('Publishing Laratomics Workshop Views...');
        $this->callSilent('vendor:publish', ['--tag' => 'workshop-views']);

        $this->info('Laratomics Workshop installed successfully.');

        return 0;
    }
}