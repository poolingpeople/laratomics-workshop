<?php

namespace Laratomics\Console\Commands;

use Illuminate\Console\Command;
use Laratomics\Services\ConfigurationService;

class ReconfigureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workshop:reconfig';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconfigure the workshop after altering config values.';

    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * Create a new command instance.
     *
     * @param ConfigurationService $configurationService
     */
    public function __construct(ConfigurationService $configurationService)
    {
        parent::__construct();
        $this->configurationService = $configurationService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->configurationService->registerViewResources()) {
            $path = config('workshop.basePath');
            $this->comment("Reset extra view path to {$path}.");
            return 0;
        }

        return -1;
    }
}
