<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Grafite\FormMaker\Services\FormMaker;

class Entry extends Model
{
    use SoftDeletes;

    public $fillable = [
        'name',
        'details',
    ];

    public function getMetaAttribute()
    {
        return (object) [
            'user' => (object) [
                'id' => 1,
            ],
            'created_at' => \Carbon\Carbon::create(1999, 1, 1, 6, 15, 0),
            'updated_at' => \Carbon\Carbon::create(1999, 1, 1, 6, 15, 0),
            'deleted_at' => null
        ];
    }
}

class FormMakerTest extends TestCase
{
    protected $app;
    protected $formMaker;

    public function setUp()
    {
        parent::setUp();
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
        $test = $this->formMaker->setConnection('testbench')->fromTable('entries');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group "><label class="control-label" for="Name">Name</label><input  id="Name" class="form-control" type="text" name="name"    placeholder="Name"></div><div class="form-group "><label class="control-label" for="Details">Text</label><textarea  id="Details" class="form-control" name="details" placeholder="Text"></textarea></div>', $test);
    }

    public function testFromTableSimulated()
    {
        $entry = app(Entry::class)->create([
            'name' => 'test entry',
            'details' => 'this entry is written in [markdown](http://markdown.com)',
        ]);

        $test = $this->formMaker->setConnection('testbench')->fromObject($entry, $this->formMaker->getTableColumns('entries'));

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group "><label class="control-label" for="Name">Name</label><input  id="Name" class="form-control" type="text" name="name"   value="test entry" placeholder="Name"></div><div class="form-group "><label class="control-label" for="Details">Details</label><textarea  id="Details" class="form-control" name="details" placeholder="Details">this entry is written in [markdown](http://markdown.com)</textarea></div>', $test);
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
            ],
        ];

        $test = $this->formMaker->fromObject((object) $testObject, $columns);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group "><label class="control-label" for="Name">Name</label><input  id="Name" class="form-control" type="text" name="name"   value="" placeholder="Name"></div><div class="form-group "><label class="control-label" for="Age">Age</label><input  id="Age" class="form-control" type="number" name="age"   value="" placeholder="Age"></div>', $test);
    }
}
