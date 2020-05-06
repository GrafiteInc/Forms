<?php

namespace Grafite\FormMaker\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Grafite\FormMaker\Services\FieldMaker;
use Grafite\FormMaker\Services\FormAssets;

/**
 * FormMaker helper to make table and object form mapping easy.
 */
class FormMaker
{
    protected $columns = 1;

    protected $sections = [];

    protected $orientation;

    protected $fieldMaker;

    public $connection;

    public function __construct()
    {
        $this->fieldMaker = app(FieldMaker::class);
        $this->formAssets = app(FormAssets::class);
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
     * Set the sections of the form
     *
     * @param array $sections
     */
    public function setSections($sections)
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * Set the columns of the form
     *
     * @param int $columns
     */
    public function setOrientation($orientation)
    {
        $this->fieldMaker->orientation = $orientation;

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
            $fields = $this->getTableAsFields($table);
        }

        $fields = $this->cleanupIdAndTimeStamps($fields);

        foreach ($fields as $column => $columnConfig) {
            if (is_numeric($column)) {
                $column = $columnConfig;
            }

            $this->setAssets($columnConfig);

            $fieldCollection[$column] = $this->fieldMaker->make($column, $columnConfig);
        }

        return $this->buildUsingColumns($fieldCollection);
    }

    /**
     * Generate a form from just the fields.
     *
     * @param string $table Table name
     * @param array  $fields Field configs
     *
     * @return string
     */
    public function fromFields($fields = [])
    {
        $fieldCollection = [];

        foreach ($fields as $column => $columnConfig) {
            if (is_numeric($column)) {
                $column = array_key_first($columnConfig);
                $columnConfig = $columnConfig[$column];
            }

            $this->setAssets($columnConfig);

            $fieldCollection[$column] = $this->fieldMaker->make($column, $columnConfig);
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

            $this->setAssets($columnConfig);

            $fieldCollection[$column] = $this->fieldMaker->make($column, $columnConfig, $object);
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
                return $this->buildColumnForm($formBuild, 2);
            case 3:
                return $this->buildColumnForm($formBuild, 3);
            case 4:
                return $this->buildColumnForm($formBuild, 4);
            case 6:
                return $this->buildColumnForm($formBuild, 6);
            case 'sections':
                return $this->buildColumnForm($formBuild, null);
            default:
                return implode("", $formBuild);
        }
    }

    /**
     * Set the assets of the form for render
     *
     * @param array $columnConfig
     * @return void
     */
    public function setAssets($columnConfig)
    {
        if (isset($columnConfig['assets'])) {
            $this->formAssets->addJs($columnConfig['assets']['js'] ?? '');
            $this->formAssets->addStyles($columnConfig['assets']['styles'] ?? '');
            $this->formAssets->addScripts($columnConfig['assets']['scripts'] ?? []);
            $this->formAssets->addStylesheets($columnConfig['assets']['stylesheets'] ?? []);
        }
    }

    /**
     * Get table columns as fields
     *
     * @param string $table
     *
     * @return array
     */
    public function getTableAsFields($table)
    {
        $fields = [];

        $tableColumns = $this->getTableColumns($table, true);

        $tableColumns = $this->cleanupIdAndTimeStamps($tableColumns);

        foreach ($tableColumns as $column => $value) {
            $fields[$column] = [
                'type' => $this->getNormalizedType($value['type'])
            ];
        }

        return $fields;
    }

    /**
     * Build a section of fields
     *
     * @param array $fields
     * @param int|null $columns
     * @param string $label
     * @return string
     */
    private function buildSection($fields, $columns, $label = null)
    {
        $newFormBuild = [];

        if (is_null($columns)) {
            $columns = count($fields);
        }

        $formChunks = array_chunk($fields, $columns);

        $columnBase = config('form-maker.sections.column-base', 'col-md-');
        $rowClass = config('form-maker.sections.row-class', 'row');
        $fullSizeColumn = config('form-maker.sections.full-size-column', 'col-md-12');
        $headerSpacing = config('form-maker.sections.header-spacing', 'mt-2 mb-2');

        $class = $columnBase . (12 / $columns);

        if (!is_null($label)) {
            $newFormBuild[] = '<div class="' . $rowClass . '">';
            $newFormBuild[] = '<div class="' . $fullSizeColumn . '"><h4 class="' . $headerSpacing . '">' . $label . '</h4><hr></div>';
            $newFormBuild[] = '</div>';
        }

        foreach ($formChunks as $chunk) {
            $newFormBuild[] = '<div class="' . $rowClass . '">';
            foreach ($chunk as $element) {
                $newFormBuild[] = '<div class="' . $class . '">';
                $newFormBuild[] = $element;
                $newFormBuild[] = '</div>';
            }
            $newFormBuild[] = '</div>';
        }

        return implode("", $newFormBuild);
    }

    /**
     * Build a two column form using standard bootstrap classes
     *
     * @param  array $formBuild
     * @param  int $columns
     * @return string
     */
    private function buildColumnForm($formBuild, $columns)
    {
        $formSections = [];

        foreach ($this->sections as $section => $fields) {
            $label = null;

            if (is_string($section)) {
                $label = $section;
            }

            $inputs = [];

            foreach ($fields as $field) {
                $inputs[] = $formBuild[$field];
            }

            $formSections[] = $this->buildSection($inputs, $columns, $label);
        }

        return implode("", $formSections);
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
                    ->getDoctrineColumn(DB::connection($this->connection)->getTablePrefix() . $table, $column)
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
            'text' => 'textarea',
            'date' => 'date',
            'datetime' => 'datetime-local',
            'datetimetz' => 'datetime-local',
            'time' => 'time',
        ];

        return $columnTypes[$type];
    }
}
