<?php

namespace Tests\Unit;

use Tests\TestCase;
use Grafite\Forms\Fields\Text;
use Grafite\Forms\Fields\TextArea;
use Illuminate\Database\Eloquent\Model;
use Grafite\Forms\Services\FormMaker;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function setUp(): void
    {
        parent::setUp();

        $this->formMaker = app(FormMaker::class);
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
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Name">Name</label><input class="form-control" id="Name" name="name" type="text" value=""></div><div class="form-group"><label class="form-label" for="Details">Details</label><textarea class="form-control" id="Details" name="details"></textarea></div>', $test);
    }

    public function testFromFields()
    {
        $test = $this->formMaker->fromFields([
            Text::make('name'),
            TextArea::make('details'),
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Name">Name</label><input class="form-control" id="Name" name="name" type="text" value=""></div><div class="form-group"><label class="form-label" for="Details">Details</label><textarea class="form-control" id="Details" rows="5" name="details"></textarea></div>', $test);
    }

    public function testFromTableSimulated()
    {
        $entry = app(Entry::class)->create([
            'name' => 'test entry',
            'details' => 'this entry is written in [markdown](http://markdown.com)',
        ]);

        $test = $this->formMaker
            ->setConnection('testbench')
            ->fromObject($entry, $this->formMaker->getTableAsFields('entries'));

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Name">Name</label><input class="form-control" id="Name" name="name" type="text" value="test entry"></div><div class="form-group"><label class="form-label" for="Details">Details</label><textarea class="form-control" id="Details" name="details">this entry is written in [markdown](http://markdown.com)</textarea></div>', $test);
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
                'type' => 'text',
            ],
            'age' => [
                'type' => 'number',
            ],
        ];

        $test = $this->formMaker->fromObject((object) $testObject, $columns);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Name">Name</label><input class="form-control" id="Name" name="name" type="text" value=""></div><div class="form-group"><label class="form-label" for="Age">Age</label><input class="form-control" id="Age" name="age" type="number" value=""></div>', $test);
    }
}
