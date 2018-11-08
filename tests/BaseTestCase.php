<?php


namespace Laratomics\Tests;

use Faker\Provider\File;
use Illuminate\Filesystem\Filesystem;
use Laratomics\Providers\PatternServiceProvider;
use Laratomics\WorkshopServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class BaseTestCase extends TestCase
{
    /**
     * Temporary testing folder.
     * @var string
     */
    protected $tempDir = __DIR__ . '/tmp';

    /**
     * Setup before testing.
     */
    protected function setUp()
    {
        $this->deleteTempDirectory();
        mkdir($this->tempDir);
        parent::setUp();
    }

    /**
     * Cleanup after testing.
     */
    protected function tearDown()
    {
        $this->deleteTempDirectory();
        parent::tearDown();
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            WorkshopServiceProvider::class,
            PatternServiceProvider::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app->get('config');
        $config->set('app.name', 'testApp');
        $config->set('workshop.uri', 'workshop');
        $config->set('workshop.basePath', $this->tempDir);
        $config->set('workshop.patternPath', $this->tempDir . '/patterns');
    }

    /**
     * Delete the temp testing directory if it exists.
     */
    protected function deleteTempDirectory(): void
    {
        $fs = new Filesystem();
        if (is_dir($this->tempDir)) {
            $fs->deleteDirectory($this->tempDir);
        }
    }
}