<?php

use Illuminate\Container\Container as Container;
use Illuminate\Support\Facades\Facade as Facade;
use Yab\FormMaker\Services\FormMaker;

class FormMakerTest extends TestCase
{
    protected $app;
    protected $formMaker;

    public function setUp()
    {
        $inputTypes = include(__DIR__.'/../config/form-maker.php');

        $this->app = new Container();
        $this->app->singleton('app', 'Illuminate\Container\Container');

        $config = Mockery::mock('config');
        $config->shouldReceive('get')
            ->with('form-maker.form.group-class', 'form-group')
            ->andReturn('form-group');
        $config->shouldReceive('get')
            ->with('form-maker.form.label-class', 'control-label')
            ->andReturn('control-label');
        $config->shouldReceive('get')
            ->with('form-maker.form.error-class', 'has-error')
            ->andReturn('has-error');
        $config->shouldReceive('get')
            ->with('form-maker.inputTypes', $inputTypes['inputTypes'])
            ->andReturn($inputTypes['inputTypes'])
            ->getMock();

        $request = Mockery::mock('request')
            ->shouldReceive('old')
            ->withAnyArgs()
            ->andReturn([])
            ->getMock();

        $database = Mockery::mock('database');
        $database->shouldReceive('connection')
            ->withAnyArgs()
            ->andReturn($database);
        $database->shouldReceive('getDoctrineColumn')
            ->withAnyArgs()
            ->andReturn($database);
        $database->shouldReceive('getType')
            ->withAnyArgs()
            ->andReturn($database);
        $database->shouldReceive('getName')
            ->withAnyArgs()
            ->andReturn('string');
        $database->shouldReceive('getColumnListing')
            ->withAnyArgs()
            ->andReturn([
                'string' => 'title',
                'string' => 'author',
            ]);
        $database->shouldReceive('getSchemaBuilder')
            ->withAnyArgs()
            ->andReturn($database)
            ->getMock();

        $session = Mockery::mock('session');
        $session->shouldReceive('isStarted')->withAnyArgs()->andReturn(true);
        $session->shouldReceive('get')->withAnyArgs()->andReturn(collect([]));

        $this->app->instance('config', $config);
        $this->app->instance('session', $session);
        $this->app->instance('request', $request);
        $this->app->instance('db', $database);

        Facade::setFacadeApplication($this->app);

        $this->formMaker = new FormMaker();
    }

    public function testSetConnection()
    {
        $test = $this->formMaker->setConnection('alternate');

        $this->assertTrue(is_string($test->connection));
        $this->assertEquals('alternate', $test->connection);
    }

    public function testFromTable()
    {
        $test = $this->formMaker->fromTable('books');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group "><label class="control-label" for="Author">Author</label><input  id="Author" class="form-control" type="text" name="author"    placeholder="Author"></div>', $test);
    }

    public function testFromArray()
    {
        $testArray = [
            'name' => 'string',
            'age' => 'number',
        ];

        $test = $this->formMaker->fromArray($testArray);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group "><label class="control-label" for="Name">Name</label><input  id="Name" class="form-control" type="text" name="name"   value="" placeholder="Name"></div><div class="form-group "><label class="control-label" for="Age">Number</label><input  id="Age" class="form-control" type="number" name="age"   value="" placeholder="Number"></div>', $test);
    }

    public function testFromArrayWithColumns()
    {
        $testArray = [
            'name' => 'string',
            'age' => 'number',
        ];

        $test = $this->formMaker->fromArray($testArray, ['name']);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group "><label class="control-label" for="Name">Name</label><input  id="Name" class="form-control" type="text" name="name"   value="" placeholder="Name"></div>', $test);
    }

    public function testFromObject()
    {
        $testObject = [
            'attributes' => [
                'name' => 'Joe',
                'age' => 18,
            ],
        ];
        $columns = [
            'name' => [
                'type' => 'string',
            ],
            'age' => [
                'type' => 'number',
            ]
        ];

        $test = $this->formMaker->fromObject((object) $testObject, $columns);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group "><label class="control-label" for="Name">Name</label><input  id="Name" class="form-control" type="text" name="name"   value="" placeholder="Name"></div><div class="form-group "><label class="control-label" for="Age">Age</label><input  id="Age" class="form-control" type="number" name="age"   value="" placeholder="Age"></div>', $test);
    }
}