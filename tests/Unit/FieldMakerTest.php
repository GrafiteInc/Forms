<?php

namespace Tests\Unit;

use Tests\TestCase;
use Grafite\Forms\Fields\Text;
use Grafite\Forms\Fields\HasOne;
use Grafite\Forms\Fields\Number;
use Grafite\Forms\Fields\Select;
use Grafite\Forms\Fields\HasMany;
use Grafite\Forms\Fields\Checkbox;
use Grafite\Forms\Fields\TextArea;
use Grafite\Forms\Services\FieldMaker;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $fillable = [
        'name',
        'email',
        'password',
    ];

    public function jobs()
    {
        return $this->hasOne(\Tests\Unit\Job::class);
    }

    public function ideas()
    {
        return $this->belongsToMany(\Tests\Unit\Idea::class);
    }

    public function getNameAttribute()
    {
        return 'joe';
    }

    public function getIdAttribute()
    {
        return 3;
    }
}

class Idea extends Model
{
    public $fillable = [
        'name',
    ];

    public function user()
    {
        return $this->hasOne('User');
    }
}

class Job extends Model
{
    public $fillable = [
        'name',
        'user_id',
    ];

    public function user()
    {
        return $this->hasOne('User');
    }

    public function custom($params)
    {
        return $this->where('name', 'LIKE', "%$params[0]%")->get();
    }
}

class FieldMakerTest extends TestCase
{
    protected $app;
    protected $fieldMaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = app(User::class);
        $this->fieldMaker = app(FieldMaker::class);
    }

    public function testMakeText()
    {
        $config = Text::make('name')->toArray();
        $field = $this->fieldMaker->make('name', $config);

        $this->assertEquals('<div class="form-group"><label class="form-label" for="Name">Name</label><input class="form-control" id="Name" name="name" type="text" value=""></div>', $field);
    }

    public function testMakeTextWithObject()
    {
        $config = Text::make('name')->toArray();
        $field = $this->fieldMaker->make('name', $config, $this->user);

        $this->assertEquals('<div class="form-group"><label class="form-label" for="Name">Name</label><input class="form-control" id="Name" name="name" type="text" value="joe"></div>', $field);
    }

    public function testCreateCheckboxArray()
    {
        $object = (object) ['gender[male]' => 'on'];

        $config = Checkbox::make('gender[male]')->toArray();
        $field = $this->fieldMaker->make('gender[male]', $config, $object);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><div class="form-check"><input class="form-check-input" id="Gender[male]" type="checkbox" name="gender[male]" checked><label class="form-check-label" for="Gender[male]">Male</label></div></div>', $field);
    }

    public function testCreateMultipleSelect()
    {
        $object = (object) ['countries' => json_encode(['Canada', 'America'])];
        $config = Select::make('countries', [
            'multiple' => true,
            'options' => [
                'Canada' => 'Canada',
                'America' => 'America',
                'UK' => 'UK',
                'Ireland' => 'Ireland',
            ],
        ])->toArray();
        $field = $this->fieldMaker->make('countries', $config, $object);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Countries">Countries</label><select class="form-control" id="Countries" multiple name="countries[]"><option value="Canada">Canada</option><option value="America">America</option><option value="UK">UK</option><option value="Ireland">Ireland</option></select></div>', $field);
    }

    public function testCreateMultipleNestedString()
    {
        $entry = app(Entry::class)->create([
            'name' => 'test entry',
            'details' => 'this entry is written in',
        ]);

        $config = Number::make('meta[user[id]]', [
            'label' => 'Meta Id',
            'id' => 'MetaId',
        ])->toArray();

        $field = $this->fieldMaker->make('meta[user[id]]', $config, $entry);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="MetaId">Meta Id</label><input class="form-control" id="MetaId" name="meta[user[id]]" type="number" value="1"></div>', $field);
    }

    public function testCreateSingleNestedString()
    {
        $entry = app(Entry::class)->create([
            'name' => 'test entry',
            'details' => 'this entry is written in',
        ]);

        $config = Text::make('meta[created_at]')->toArray();

        $field = $this->fieldMaker->make('meta[created_at]', $config, $entry);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Meta[created_at]">Created At</label><input class="form-control" id="Meta[created_at]" name="meta[created_at]" type="text" value="1999-01-01"></div>', $field);
    }

    public function testCreateSpecialString()
    {
        $entry = app(Entry::class)->create([
            'name' => 'test entry',
            'details' => 'this entry is written in [markdown](http://markdown.com)',
        ]);

        $config = TextArea::make('details')->toArray();

        $field = $this->fieldMaker->make('details', $config, $entry);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Details">Details</label><textarea class="form-control" id="Details" rows="5" name="details">this entry is written in [markdown](http://markdown.com)</textarea></div>', $field);
    }

    public function testCreateRelationshipWithoutObject()
    {
        $user = app(User::class)->create([
            'name' => 'Joe',
            'email' => 'joe@haltandcatchfire.com',
            'password' => 'password',
        ]);

        $job = app(\Tests\Unit\Job::class)->create([
            'name' => 'Worker',
            'user_id' => 1,
        ]);

        $user->job_id = $job->id;
        $user->save();

        app(\Tests\Unit\Job::class)->create([
            'name' => 'BlackSmith',
        ]);
        app(\Tests\Unit\Job::class)->create([
            'name' => 'Police',
        ]);
        app(\Tests\Unit\Job::class)->create([
            'name' => 'Brogrammer',
        ]);

        $config = HasMany::make('jobs', [
            'model' => \Tests\Unit\Job::class,
            'model_options' => [
                'label' => 'name',
                'value' => 'id',
            ]
        ])->toArray();

        $field = $this->fieldMaker->make('jobs', $config);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Jobs">Jobs</label><select class="form-control" id="Jobs" multiple name="jobs[]"><option value="1">Worker</option><option value="2">BlackSmith</option><option value="3">Police</option><option value="4">Brogrammer</option></select></div>', $field);
    }

    public function testCreateRelationshipWithoutObjectWithForcedOptions()
    {
        $user = app(User::class)->create([
            'name' => 'Joe',
            'email' => 'joe@haltandcatchfire.com',
            'password' => 'password',
        ]);

        $job = app(Job::class)->create([
            'name' => 'Worker',
            'user_id' => 1,
        ]);

        $user->job_id = $job->id;
        $user->save();

        app(Job::class)->create([
            'name' => 'BlackSmith',
        ]);
        app(Job::class)->create([
            'name' => 'Police',
        ]);
        app(Job::class)->create([
            'name' => 'Brogrammer',
        ]);

        $config = HasMany::make('jobs', [
            'model' => Job::class,
            'options' => app(Job::class)->all()->pluck('id', 'name')->toArray(),
        ])->toArray();

        $field = $this->fieldMaker->make('jobs', $config);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Jobs">Jobs</label><select class="form-control" id="Jobs" multiple name="jobs[]"><option value="1">Worker</option><option value="2">BlackSmith</option><option value="3">Police</option><option value="4">Brogrammer</option></select></div>', $field);
    }

    public function testCreateRelationshipHasOne()
    {
        $user = app(User::class)->create([
            'name' => 'Joe',
            'email' => 'joe@haltandcatchfire.com',
            'password' => 'password',
        ]);

        $job = app(Job::class)->create([
            'name' => 'Worker',
            'user_id' => 1,
        ]);

        $user->job_id = $job->id;
        $user->save();

        app(Job::class)->create([
            'name' => 'BlackSmith',
        ]);
        app(Job::class)->create([
            'name' => 'Police',
        ]);
        app(Job::class)->create([
            'name' => 'Brogrammer',
        ]);

        $config = HasOne::make('jobs', [
            'model' => Job::class,
            'model_options' => [
                'label' => 'name',
                'value' => 'id',
            ]
        ])->toArray();

        $field = $this->fieldMaker->make('jobs', $config, $user);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Jobs">Jobs</label><select class="form-control" id="Jobs" name="jobs"><option value="1">Worker</option><option value="2">BlackSmith</option><option value="3">Police</option><option value="4">Brogrammer</option></select></div>', $field);
    }

    public function testCreateRelationshipCustom()
    {
        $user = app(User::class)->create([
            'name' => 'Joe',
            'email' => 'joe@haltandcatchfire.com',
            'password' => 'password',
        ]);

        $job = app(Job::class)->create([
            'name' => 'Worker',
            'user_id' => 1,
        ]);

        $user->job_id = $job->id;
        $user->save();

        app(Job::class)->create([
            'name' => 'BlackSmith',
        ]);
        app(Job::class)->create([
            'name' => 'Police',
        ]);
        app(Job::class)->create([
            'name' => 'Brogrammer',
        ]);

        $config = HasOne::make('jobs', [
            'model' => Job::class,
            'model_options' => [
                'method' => 'custom',
                'params' => [
                    'Bro'
                ],
                'label' => 'name',
                'value' => 'id',
            ],
        ])->toArray();

        $field = $this->fieldMaker->make('jobs', $config, $user);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Jobs">Jobs</label><select class="form-control" id="Jobs" name="jobs"><option value="4">Brogrammer</option></select></div>', $field);
    }

    public function testCreateRelationshipCustomMultiple()
    {
        $user = app(User::class)->create([
            'name' => 'Joe',
            'email' => 'joe@haltandcatchfire.com',
            'password' => 'password',
        ]);

        $idea1 = app(\Tests\Unit\Idea::class)->create([
            'name' => 'Thing',
        ]);
        $idea2 = app(\Tests\Unit\Idea::class)->create([
            'name' => 'Foo',
        ]);

        app(\Tests\Unit\Idea::class)->create([
            'name' => 'Bar',
        ]);
        app(\Tests\Unit\Idea::class)->create([
            'name' => 'Drink',
        ]);

        $user->ideas()->attach([$idea1->id, $idea2->id]);

        $config = HasMany::make('ideas', [
            'model' => \Tests\Unit\Idea::class,
            'model_options' => [
                'label' => 'name',
                'value' => 'id',
            ],
        ])->toArray();

        $field = $this->fieldMaker->make('ideas', $config, $user);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="form-label" for="Ideas">Ideas</label><select class="form-control" id="Ideas" multiple name="ideas[]"><option value="1" selected>Thing</option><option value="2" selected>Foo</option><option value="3">Bar</option><option value="4">Drink</option></select></div>', $field);
    }
}
