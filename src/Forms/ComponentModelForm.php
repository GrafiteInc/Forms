<?php

namespace Grafite\Forms\Forms;

use Grafite\Forms\Forms\ModelForm;
use Grafite\Forms\Forms\Concerns\AsComponent;

class ComponentModelForm extends ModelForm
{
    use AsComponent;

    public $instance;
    public $action;
    public $modal;
    public $query;
    public $searchPlaceholder;
    public $searchSubmit;
    public $searchMethod;
    public $searchRoute;

    public function __construct(
        $instance = null,
        $action = null,
        $modal = false,
        $query = null,
        $searchPlaceholder = 'Search',
        $searchSubmit = 'Search',
        $searchMethod = 'post',
        $searchRoute = null
    ) {
        $this->instance = $instance;
        $this->action = $action;
        $this->modal = $modal;
        $this->query = $query;
        $this->searchPlaceholder = $searchPlaceholder;
        $this->searchSubmit = $searchSubmit;
        $this->searchMethod = $searchMethod;
        $this->searchRoute = $searchRoute;

        parent::__construct();
    }

    public function render()
    {
        if ($this->action === 'create') {
            $this->create();
        }

        if ($this->action === 'index') {
            $this->index();
        }

        if ($this->action === 'search') {
            $this->html = $this->index()->search(
                $this->searchRoute,
                $this->searchPlaceholder,
                $this->searchSubmit,
                $this->searchMethod
            );
        }

        if ($this->instance && $this->action === 'edit') {
            $this->edit($this->instance);
        }

        if ($this->instance && $this->action === 'delete') {
            $this->delete($this->instance);
        }

        if ($this->modal) {
            $this->html = $this->asModal();
        }

        return $this->html;
    }
}
