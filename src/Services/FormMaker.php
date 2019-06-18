<?php

namespace Grafite\FormMaker\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Grafite\FormMaker\Services\FieldMaker;

/**
 * FormMaker helper to make table and object form mapping easy.
 */
class FormMaker
{
    protected $columns = 1;

    protected $orientation;

    protected $fieldMaker;

    public $connection;

    public function __construct()
    {
        $this->fieldMaker = app(FieldMaker::class);
        $this->connection = config('database.default');

        if (is_null($this->orientation)) {
            $this->orientation = config('form-maker.form.orientation', 'vertical');
        }
    }

    /**
     * Set the form maker connection.
     *
     * @param string $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Set the columns of the form
     *
     * @param int $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Set the columns of the form
     *
     * @param int $columns
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * Generate a form from a table.
     *
     * @param string $table Table name
     * @param array  $fields Field configs
     *
     * @return string
     */
    public function fromTable($table, $fields = [])
    {
        $fieldCollection = [];

        if (empty($fields)) {
            $tableColumns = $this->getTableColumns($table, true);
            foreach ($tableColumns as $column => $value) {
                $fields[$column] = $this->getNormalizedType($value['type']);
            }
        }

        $fields = $this->cleanupIdAndTimeStamps($fields);

        foreach ($fields as $column => $columnConfig) {
            if (is_numeric($column)) {
                $column = $columnConfig;
            }

            $fieldCollection[] = $this->fieldMaker->make($column, $columnConfig);
        }

        return $this->buildUsingColumns($fieldCollection);
    }

    /**
     * Build the form from an object.
     *
     * @param object $object An object to base the form off
     * @param array  $fields Field configs
     *
     * @return string
     */
    public function fromObject($object, $fields = [])
    {
        $fieldCollection = [];

        if (empty($fields)) {
            $fields = is_array($object['attributes']) ? array_keys($object['attributes']) : [];
        }

        foreach ($fields as $column => $columnConfig) {
            if (is_numeric($column)) {
                $column = $columnConfig;
            }

            if ($column === 'id') {
                $columnConfig = [
                    'type' => 'hidden'
                ];
            }

            $fieldCollection[] = $this->fieldMaker->make($column, $columnConfig, $object);
        }

        return $this->buildUsingColumns($fieldCollection);
    }

    /**
     * Cleanup the ID and TimeStamp columns.
     *
     * @param array $columns
     *
     * @return array
     */
    public function cleanupIdAndTimeStamps($columns)
    {
        unset($columns['id']);
        unset($columns['created_at']);
        unset($columns['updated_at']);
        unset($columns['deleted_at']);

        return $columns;
    }

    /**
     * Build based on the columns wanted
     *
     * @param array  $formBuild
     * @param string $columns
     *
     * @return string
     */
    private function buildUsingColumns($formBuild)
    {
        switch ($this->columns) {
            case 1:
                return implode("", $formBuild);
            case 2:
                return $this->buildBootstrapColumnForm($formBuild, 2);
            case 3:
                return $this->buildBootstrapColumnForm($formBuild, 3);
            case 4:
                return $this->buildBootstrapColumnForm($formBuild, 4);
            case 6:
                return $this->buildBootstrapColumnForm($formBuild, 6);
            default:
                return implode("", $formBuild);
        }
    }

    /**
     * Build a two column form using standard bootstrap classes
     *
     * @param  array $formBuild
     * @return string
     */
    private function buildBootstrapColumnForm($formBuild, $columns)
    {
        $newFormBuild = [];
        $formChunks = array_chunk($formBuild, $columns);
        $class = 'col-md-'.(12 / $columns);

        foreach ($formChunks as $chunk) {
            $newFormBuild[] = '<div class="row">';
            foreach ($chunk as $element) {
                $newFormBuild[] = '<div class="'.$class.'">';
                $newFormBuild[] = $element;
                $newFormBuild[] = '</div>';
            }
            $newFormBuild[] = '</div>';
        }

        return implode("", $newFormBuild);
    }

    /**
     * Get Table Columns.
     *
     * @param string $table Table name
     *
     * @return array
     */
    public function getTableColumns($table, $allColumns = false)
    {
        $tableColumns = Schema::connection($this->connection)->getColumnListing($table);

        $tableTypeColumns = [];
        $badColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];

        if ($allColumns) {
            $badColumns = [];
        }

        foreach ($tableColumns as $column) {
            if (!in_array($column, $badColumns)) {
                $type = DB::connection($this->connection)
                    ->getDoctrineColumn(DB::connection($this->connection)->getTablePrefix().$table, $column)
                    ->getType()->getName();
                $tableTypeColumns[$column]['type'] = $type;
            }
        }

        return $tableTypeColumns;
    }

    public function getNormalizedType($type)
    {
        $columnTypes = [
            'number' => 'number',
            'smallint' => 'number',
            'integer' => 'number',
            'bigint' => 'number',
            'float' => 'decimal',
            'decimal' => 'decimal',
            'boolean' => 'number',
            'string' => 'text',
            'guid' => 'text',
            'text' => 'text',
            'date' => 'date',
            'datetime' => 'datetime',
            'datetimetz' => 'datetime-local',
            'time' => 'time',
        ];

        return $columnTypes[$type];
    }
}
