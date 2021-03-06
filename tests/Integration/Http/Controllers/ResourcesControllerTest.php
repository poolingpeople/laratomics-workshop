<?php

namespace Integration\Http\Controllers;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Tests\BaseTestCase;
use Tests\Traits\TestStubs;

class ResourcesControllerTest extends BaseTestCase
{
    use TestStubs;

    /**
     * @test
     * @covers \Oloid\Http\Controllers\ResourcesController
     */
    public function it_should_return_empty_global_dependencies()
    {
        // arrange

        /** @var TestResponse $response */
        $response = $this->getJson("workshop/api/v1/resources");

        // assert
        $response->assertSuccessful();
        $expectedJson = [
            'data' => [
                'head' => '',
                'body' => '',
            ]
        ];
        $response->assertJson($expectedJson);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\ResourcesController
     */
    public function it_should_return_global_dependencies()
    {
        // arrange
        $this->prepareResourcesStub();

        /** @var TestResponse $response */
        $response = $this->getJson("workshop/api/v1/resources");

        // assert
        $response->assertSuccessful();
        $expectedJson = [
            'data' => [
                'head' => '<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha256-KsRuvuRtUVvobe66OFtOQfjP8WA2SzYsmm4VPfMnxms=" crossorigin="anonymous"></script>',
                'body' => ''
            ]
        ];
        $response->assertJson($expectedJson);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\ResourcesController
     */
    public function it_should_add_a_global_dependency()
    {
        $dependencyPath = "{$this->tempDir}/resources.json";
        $this->assertFalse(File::exists($dependencyPath));

        // act
        $response = $this->postJson('workshop/api/v1/resources', [
            'head' => '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,600">',
            'body' => ''
        ]);

        // assert
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertTrue(File::exists($dependencyPath));

        $dependencies = File::get($dependencyPath);
        $expectedDependencies = [
            'head' => '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,600">',
            'body' => '',
        ];
        $this->assertEquals($expectedDependencies, json_decode($dependencies, true));
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\ResourcesController
     */
    public function it_should_throw_validation_exception_on_missing_fields()
    {
        // arrange

        // act
        $response = $this->postJson('workshop/api/v1/resources', [
            'head' => 'https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css'
        ]);

        // assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
