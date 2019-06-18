<?php

use Grafite\FormMaker\Fields\Text;
use Grafite\FormMaker\Fields\Number;
use Grafite\FormMaker\Fields\Select;
use Grafite\FormMaker\Fields\Checkbox;
use Illuminate\Database\Eloquent\Model;
use Grafite\FormMaker\Services\FieldMaker;

class User extends Model
{
    public $fillable = [
        'name',
        'email',
        'password',
    ];

    public function jobs()
    {
        return $this->hasOne('Job');
    }

    public function ideas()
    {
        return $this->belongsToMany('Idea');
    }

    public function getNameAttribute()
    {
        return 'joe';
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
        $config = Text::make('name');
        $field = $this->fieldMaker->make('name', $config['name']);

        $this->assertEquals('<div class="form-group"><label class="" for="Name">Name</label><input  class="form-control" id="Name" name="name" type="text" value=""></div>', $field);
    }

    public function testMakeTextWithObject()
    {
        $config = Text::make('name');
        $field = $this->fieldMaker->make('name', $config['name'], $this->user);

        $this->assertEquals('<div class="form-group"><label class="" for="Name">Name</label><input  class="form-control" id="Name" name="name" type="text" value="joe"></div>', $field);
    }

    public function testCreateCheckboxArray()
    {
        $object = (object) ['gender[male]' => 'on'];

        $config = Checkbox::make('gender[male]');
        $field = $this->fieldMaker->make('gender[male]', $config['gender[male]'], $object);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><div class="form-check"><input  class="form-check-input" id="Gender[male]" type="checkbox" name="gender[male]" checked><label class="form-check-label"></label></div></div>', $field);
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
        ]);
        $field = $this->fieldMaker->make('countries', $config['countries'], $object);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="" for="Countries">Countries</label><select  class="form-control" id="Countries" multiple name="countries[]"><option value="Canada" >Canada</option><option value="America" >America</option><option value="UK" >UK</option><option value="Ireland" >Ireland</option></select></div>', $field);
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
        ]);

        $field = $this->fieldMaker->make('meta[user[id]]', $config['meta[user[id]]'], $entry);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="" for="MetaId">Meta Id</label><input  class="form-control" id="MetaId" name="meta[user[id]]" type="number" value="1"></div>', $field);
    }

    public function testCreateSingleNestedString()
    {
        $entry = app(Entry::class)->create([
            'name' => 'test entry',
            'details' => 'this entry is written in',
        ]);

        $config = Text::make('meta[created_at]');

        $field = $this->fieldMaker->make('meta[created_at]', $config['meta[created_at]'], $entry);

        $this->assertTrue(is_string($field));
        $this->assertEquals('<div class="form-group"><label class="" for="Meta[created_at]">Meta[created_at]</label><input  class="form-control" id="Meta[created At]" name="meta[created_at]" type="text" value="1999-01-01"></div>', $field);
    }

    public function testCreateSpecialString()
    {
    //     $entry = app(Entry::class)->create([
    //         'name' => 'test entry',
    //         'details' => 'this entry is written in [markdown](http://markdown.com)',
    //     ]);

    //     $field = $this->fieldMaker->make('details', [
    //         'type' => 'text',
    //     ], $entry);

    //     $this->assertTrue(is_string($field));
    //     $this->assertEquals($field, '<textarea  id="Details" class="form-control" name="details" placeholder="Details">this entry is written in [markdown](http://markdown.com)</textarea>');
    }

    public function testCreateRelationshipWithoutObject()
    {
    //     $user = app(User::class)->create([
    //         'name' => 'Joe',
    //         'email' => 'joe@haltandcatchfire.com',
    //         'password' => 'password',
    //     ]);

    //     $job = app(Job::class)->create([
    //         'name' => 'Worker',
    //         'user_id' => 1,
    //     ]);

    //     $user->job_id = $job->id;
    //     $user->save();

    //     app(Job::class)->create([
    //         'name' => 'BlackSmith',
    //     ]);
    //     app(Job::class)->create([
    //         'name' => 'Police',
    //     ]);
    //     app(Job::class)->create([
    //         'name' => 'Brogrammer',
    //     ]);

    //     $field = $this->fieldMaker->make('jobs[]', [
    //         'type' => 'relationship',
    //         'model' => 'Job',
    //         'label' => 'name',
    //         'options' => app(Job::class)->all()->pluck('id', 'name'),
    //         'value' => 'id',
    //         'custom' => 'multiple',
    //     ]);

    //     $this->assertTrue(is_string($field));
    //     $this->assertEquals($field, '<select multiple id="Jobs" class="form-control" name="jobs[]"><option value="1" >Worker</option><option value="2" >BlackSmith</option><option value="3" >Police</option><option value="4" >Brogrammer</option></select>');
    }

    public function testCreateRelationshipDefault()
    {
    //     $user = app(User::class)->create([
    //         'name' => 'Joe',
    //         'email' => 'joe@haltandcatchfire.com',
    //         'password' => 'password',
    //     ]);

    //     $job = app(Job::class)->create([
    //         'name' => 'Worker',
    //         'user_id' => 1,
    //     ]);

    //     $user->job_id = $job->id;
    //     $user->save();

    //     app(Job::class)->create([
    //         'name' => 'BlackSmith',
    //     ]);
    //     app(Job::class)->create([
    //         'name' => 'Police',
    //     ]);
    //     app(Job::class)->create([
    //         'name' => 'Brogrammer',
    //     ]);

    //     $field = $this->fieldMaker->make('jobs[]', [
    //         'type' => 'relationship',
    //         'model' => 'Job',
    //         'label' => 'name',
    //         'value' => 'id',
    //         'custom' => 'multiple',
    //     ], $user);

    //     $this->assertTrue(is_string($field));
    //     $this->assertEquals($field, '<select multiple id="Jobs" class="form-control" name="jobs[]"><option value="1" selected>Worker</option><option value="2" >BlackSmith</option><option value="3" >Police</option><option value="4" >Brogrammer</option></select>');
    }

    public function testCreateRelationshipCustom()
    {
    //     $user = app(User::class)->create([
    //         'name' => 'Joe',
    //         'email' => 'joe@haltandcatchfire.com',
    //         'password' => 'password',
    //     ]);

    //     $job = app(Job::class)->create([
    //         'name' => 'Worker',
    //         'user_id' => 1,
    //     ]);

    //     $user->job_id = $job->id;
    //     $user->save();

    //     app(Job::class)->create([
    //         'name' => 'BlackSmith',
    //     ]);
    //     app(Job::class)->create([
    //         'name' => 'Police',
    //     ]);
    //     app(Job::class)->create([
    //         'name' => 'Brogrammer',
    //     ]);

    //     $field = $this->fieldMaker->make('jobs', [
    //         'options' => [],
    //         'type' => 'relationship',
    //         'model' => 'Job',
    //         'method' => 'custom',
    //         'params' => ['Bro'],
    //         'label' => 'name',
    //         'value' => 'id',
    //     ], $user, 'form-control', false, true);

    //     $this->assertTrue(is_string($field));
    //     $this->assertEquals($field, '<select  id="Jobs" class="form-control" name="jobs"><option value="4" >Brogrammer</option></select>');
    }

    public function testCreateRelationshipCustomMultiple()
    {
    //     $user = app(User::class)->create([
    //         'name' => 'Joe',
    //         'email' => 'joe@haltandcatchfire.com',
    //         'password' => 'password',
    //     ]);

    //     $idea1 = app(Idea::class)->create([
    //         'name' => 'Thing',
    //     ]);

    //     $idea2 = app(Idea::class)->create([
    //         'name' => 'Foo',
    //     ]);

    //     app(Idea::class)->create([
    //         'name' => 'Bar',
    //     ]);
    //     app(Idea::class)->create([
    //         'name' => 'Drink',
    //     ]);

    //     $user->ideas()->attach([$idea1->id, $idea2->id]);

    //     $field = $this->fieldMaker->make('ideas', [
    //         'selected' => app(User::class)->where('name', 'Joe')->first()->ideas()->pluck('id'),
    //         'type' => 'relationship',
    //         'model' => 'Idea',
    //         'label' => 'name',
    //         'value' => 'id',
    //         'multiple' => true,
    //     ], $user, 'form-control', false, true);

    //     $this->assertTrue(is_string($field));
    //     $this->assertEquals($field, '<select multiple id="Ideas" class="form-control" name="ideas[]"><option value="1" selected>Thing</option><option value="2" selected>Foo</option><option value="3" >Bar</option><option value="4" >Drink</option></select>');
    }
}
