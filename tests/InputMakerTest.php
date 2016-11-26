<?php

use Illuminate\Database\Eloquent\Model;
use Yab\FormMaker\Services\InputMaker;

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

class InputMakerTest extends TestCase
{
    protected $app;
    protected $inputMaker;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__.'/migrations'),
        ]);

        $this->testUser = app(User::class);
        $this->inputMaker = app(InputMaker::class);
    }

    public function testCreateString()
    {
        $object = (object) ['name' => 'test'];
        $test = $this->inputMaker->create('name', [], $object, 'form-control', false, true);

        $this->assertTrue(is_string($test));
        $this->assertEquals($test, '<input  id="Name" class="form-control" type="text" name="name"   value="test" placeholder="Name">');
    }

    public function testCreateCheckboxArray()
    {
        $object = (object) [ 'gender[male]' => 'on' ];
        $test = $this->inputMaker->create('gender[male]', [
            'type' => 'checkbox'
        ], $object);

        $this->assertTrue(is_string($test));
        $this->assertEquals($test, '<input  id="Gender[male]" checked type="checkbox" name="gender[male]">');
    }

    public function testCreateMultipleSelect()
    {
        $object = (object) [ 'countries' => json_encode(["Canada", "America"]) ];
        $test = $this->inputMaker->create('countries', [
            'type' => 'select',
            'custom' => 'multiple',
            'options' => [
                "Canada" => "Canada",
                "America" => "America",
                "UK" => "UK",
                "Ireland" => "Ireland",
            ]
        ], $object);

        $this->assertTrue(is_string($test));
        $this->assertEquals($test, '<select multiple id="Countries" class="form-control" name="countries"><option value="Canada" selected>Canada</option><option value="America" selected>America</option><option value="UK" >UK</option><option value="Ireland" >Ireland</option></select>');
    }

    public function testCreateMultipleNestedString()
    {
        $entry = app(Entry::class)->create([
            'name' => 'test entry',
            'details' => 'this entry is written in'
        ]);

        $test = $this->inputMaker->create('meta[user[id]]', [
            'type' => 'number',
        ], $entry);

        $this->assertTrue(is_string($test));
        $this->assertEquals($test, '<input  id="Meta[user[id]]" class="form-control" type="number" name="meta[user[id]]"   value="1" placeholder="Meta User Id">');
    }

    public function testCreateSingleNestedString()
    {
        $entry = app(Entry::class)->create([
            'name' => 'test entry',
            'details' => 'this entry is written in'
        ]);

        $test = $this->inputMaker->create('meta[created_at]', [
            'type' => 'string',
        ], $entry);

        $this->assertTrue(is_string($test));
        $this->assertEquals($test, '<input  id="Meta[created_at]" class="form-control" type="text" name="meta[created_at]"   value="1999-01-01 06:15:00" placeholder="Meta Created At">');
    }

    public function testCreateSpecialString()
    {
        $entry = app(Entry::class)->create([
            'name' => 'test entry',
            'details' => 'this entry is written in [markdown](http://markdown.com)'
        ]);

        $test = $this->inputMaker->create('details', [
            'type' => 'text',
        ], $entry);

        $this->assertTrue(is_string($test));
        $this->assertEquals($test, '<textarea  id="Details" class="form-control" name="details" placeholder="Details">this entry is written in [markdown](http://markdown.com)</textarea>');
    }

    public function testCreateRelationshipDefault()
    {
        $user = app(User::class)->create([
            'name' => 'Joe',
            'email' => 'joe@haltandcatchfire.com',
            'password' => 'password'
        ]);

        $job = app(Job::class)->create([
            'name' => 'Worker',
            'user_id' => 1
        ]);

        $user->job_id = $job->id;
        $user->save();

        app(Job::class)->create([
            'name' => 'BlackSmith'
        ]);
        app(Job::class)->create([
            'name' => 'Police'
        ]);
        app(Job::class)->create([
            'name' => 'Brogrammer'
        ]);

        $test = $this->inputMaker->create('jobs', [
            'options' => [],
            'type' => 'relationship',
            'model' => 'Job',
            'method' => 'all',
            'label' => 'name',
            'value' => 'id'
        ], $user, 'form-control', false, true);

        $this->assertTrue(is_string($test));
        $this->assertEquals($test, '<select  id="Jobs" class="form-control" name="jobs"><option value="1" selected>Worker</option><option value="2" >BlackSmith</option><option value="3" >Police</option><option value="4" >Brogrammer</option></select>');
    }

    public function testCreateRelationshipCustom()
    {
        $user = app(User::class)->create([
            'name' => 'Joe',
            'email' => 'joe@haltandcatchfire.com',
            'password' => 'password'
        ]);

        $job = app(Job::class)->create([
            'name' => 'Worker',
            'user_id' => 1
        ]);

        $user->job_id = $job->id;
        $user->save();

        app(Job::class)->create([
            'name' => 'BlackSmith'
        ]);
        app(Job::class)->create([
            'name' => 'Police'
        ]);
        app(Job::class)->create([
            'name' => 'Brogrammer'
        ]);

        $test = $this->inputMaker->create('jobs', [
            'options' => [],
            'type' => 'relationship',
            'model' => 'Job',
            'method' => 'custom',
            'params' => ['Bro'],
            'label' => 'name',
            'value' => 'id'
        ], $user, 'form-control', false, true);

        $this->assertTrue(is_string($test));
        $this->assertEquals($test, '<select  id="Jobs" class="form-control" name="jobs"><option value="4" >Brogrammer</option></select>');
    }
}
