<?php

namespace Tests\Unit;

use Grafite\Forms\Fields\Text;
use Grafite\Forms\Fields\TextArea;
use Grafite\Forms\Services\FormMaker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;

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
            'deleted_at' => null,
        ];
    }
}

class FormMakerTest extends TestCase
{
    protected $app;

    protected $formMaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formMaker = app(FormMaker::class);
    }

    public function test_set_connection()
    {
        $test = $this->formMaker->setConnection('alternate');

        $this->assertTrue(is_string($test->connection));
        $this->assertEquals('alternate', $test->connection);
    }

    public function test_from_table()
    {
        $test = $this->formMaker->setConnection('testbench')->fromTable('entries');

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group"><label class="control-label" for="Name">Name</label><input class="form-control" id="Name" name="name" type="text" value=""></div><div class="form-group"><label class="control-label" for="Details">Details</label><textarea class="form-control" id="Details" name="details"></textarea></div>', $test);
    }

    public function test_from_fields()
    {
        $test = $this->formMaker->fromFields([
            Text::make('name'),
            TextArea::make('details'),
        ]);

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group"><label class="control-label" for="Name">Name</label><input class="form-control" id="Name" name="name" type="text" value=""></div><div class="form-group"><label class="control-label" for="Details">Details</label><textarea class="form-control" id="Details" rows="5" name="details"></textarea></div>', $test);
    }

    public function test_from_table_simulated()
    {
        $entry = app(Entry::class)->create([
            'name' => 'test entry',
            'details' => 'this entry is written in [markdown](http://markdown.com)',
        ]);

        $test = $this->formMaker
            ->setConnection('testbench')
            ->fromObject($entry, $this->formMaker->getTableAsFields('entries'));

        $this->assertTrue(is_string($test));
        $this->assertEquals('<div class="form-group"><label class="control-label" for="Name">Name</label><input class="form-control" id="Name" name="name" type="text" value="test entry"></div><div class="form-group"><label class="control-label" for="Details">Details</label><textarea class="form-control" id="Details" name="details">this entry is written in [markdown](http://markdown.com)</textarea></div>', $test);
    }

    public function test_from_object()
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
        $this->assertEquals('<div class="form-group"><label class="control-label" for="Name">Name</label><input class="form-control" id="Name" name="name" type="text" value=""></div><div class="form-group"><label class="control-label" for="Age">Age</label><input class="form-control" id="Age" name="age" type="number" value=""></div>', $test);
    }
}
