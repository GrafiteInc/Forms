<?php

namespace Tests\Feature;

use Tests\TestCase;

class DummyTest extends TestCase
{
    public function testIndex()
    {
        $response = $this->get(route('DummyRoutePrefix.index'));

        $response->assertOk();
    }

    public function testCreate()
    {
        $response = $this->get(route('DummyRoutePrefix.create'));

        $response->assertOk();
    }

    public function testEdit()
    {
        $response = $this->get(route('DummyRoutePrefix.edit'));

        $response->assertOk();
    }

    public function testStore()
    {
        $model = factory(DummyModel::class)
            ->make()
            ->toArray();

        $response = $this->post(route('DummyRouteCreate'), $model);

        $response->assertOk();
    }

    public function testUpdate()
    {
        $model = factory(DummyModel::class)
            ->create();

        $response = $this->put(route('DummyRouteUpdate', $model->id), $model);

        $response->assertOk();
    }

    public function testDelete()
    {
        $model = factory(DummyModel::class)
            ->create();

        $response = $this->delete(route('DummyRouteDelete', $model->id));

        $response->assertOk();
    }
}
