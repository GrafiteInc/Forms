<?php

namespace Grafite\Forms\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Grafite\Forms\Traits\HasErrorBag;
use Grafite\Forms\Traits\HasLivewire;
use Grafite\Forms\Services\FieldMaker;
use Grafite\Forms\Services\FormAssets;
use Illuminate\Support\Facades\Schema;

/**
 * FormMaker helper to make table and object form mapping easy.
 */
class FormMaker
{
    use HasLivewire;
    use HasErrorBag;

    protected $columns = 1;

    protected $sections = [];

    protected $orientation;

    protected $withJsValidation = false;

    protected $fieldMaker;

    public $connection;

    public $errorBag;

    public $withLivewire = false;

    public $livewireOnKeydown = false;

    public function __construct()
    {
        $this->fieldMaker = app(FieldMaker::class);
        $this->formAssets = app(FormAssets::class);
        $this->connection = config('database.default');

        if (is_null($this->orientation)) {
            $this->orientation = config('forms.form.orientation', 'vertical');
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
     * Set if the form uses js validation
     *
     * @param bool $withJsValidation
     */
    public function setJsValidation($withJsValidation)
    {
        $this->withJsValidation = $withJsValidation;

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

            $fieldCollection[$column] = $this->fieldMaker
                ->setErrorBag($this->errorBag)
                ->setLivewire($this->withLivewire)
                ->setLivewireOnKeydown($this->livewireOnKeydown)
                ->make($column, $columnConfig);
        }

        $this->defaultJs();

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

            $fieldCollection[$column] = $this->fieldMaker
                ->setErrorBag($this->errorBag)
                ->setLivewire($this->withLivewire)
                ->setLivewireOnKeydown($this->livewireOnKeydown)
                ->make($column, $columnConfig);
        }

        $this->defaultJs();

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
                    'type' => 'hidden',
                ];
            }

            $this->setAssets($columnConfig);

            $fieldCollection[$column] = $this->fieldMaker
                ->setErrorBag($this->errorBag)
                ->setLivewire($this->withLivewire)
                ->setLivewireOnKeydown($this->livewireOnKeydown)
                ->make($column, $columnConfig, $object);
        }

        $this->defaultJs();

        return $this->buildUsingColumns($fieldCollection);
    }

    /**
     * In cases where data is unknown
     *
     * @param array $fields
     * @param array|object $data
     * @return string
     */
    public function fromFieldsOrObject($fields, $data)
    {
        if (! is_null($data)) {
            return $this->fromObject($data, $fields);
        }

        return $this->fromFields($fields);
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
        unset($columns['id'], $columns['created_at'], $columns['updated_at'], $columns['deleted_at']);

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
                return implode('', $formBuild);

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

            case 'steps':
                return $this->buildColumnForm($formBuild, null, true);

            default:
                return implode('', $formBuild);
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
     * Set the form JavaScript
     *
     * @param array $scripts
     * @return self
     */
    public function setFormJs($scripts)
    {
        if (! is_null($scripts)) {
            $this->formAssets->addJs($scripts);
        }

        return $this;
    }

    /**
     * Set the form styles
     *
     * @param string $styles
     * @return self
     */
    public function setFormStyles($styles)
    {
        if (! is_null($styles)) {
            $this->formAssets->addStyles($styles);
        }

        return $this;
    }

    /**
     * The default JS for form validation
     *
     * @return string
     */
    public function defaultJs()
    {
        $formValidationClass = config('forms.form.invalid-input-class', 'is-invalid');

        $formValidation = <<<EOT
window.Forms_validation = function () {
    let _fields = document.getElementsByClassName('{$formValidationClass}');

    for (let i = 0; i < _fields.length; i++) {
        _fields[i].addEventListener("keyup", function (e) {
            if (this.value.length > 0) {
                this.classList.remove('{$formValidationClass}');
            }
        });

        _fields[i].addEventListener("onfocusout", function (e) {
            if (this.value.length > 0) {
                this.classList.remove('{$formValidationClass}');
            }
        });
    }
}
window.Forms_validation();
EOT;

        if ($this->withJsValidation) {
            $this->formAssets->addJs($formValidation);
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
                'type' => $this->getNormalizedType($value['type']),
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
    private function buildSection($fields, $columns, $label = null, $isStepped = false, $step = 1)
    {
        $formChunks = [];
        $newFormBuild = [];

        // We move all hidden fields to the bottom to not interfere
        // with the layout of columns.
        $fields = collect($fields)->sortBy(function ($element) {
            if (
                Str::contains($element, 'type="hidden"')
                && ! Str::contains($element, 'label')
            ) {
                return 4;
            }

            return 1;
        })->toArray();

        if (is_null($columns)) {
            $columns = count($fields);
        }

        if (! empty($fields)) {
            $formChunks = array_chunk($fields, $columns);
        }

        $columnBase = config('forms.form.sections.column-base', 'col-md-');
        $rowClass = config('forms.form.sections.row-class', 'row');
        $fullSizeColumn = config('forms.form.sections.full-size-column', 'col-md-12');
        $headerSpacing = config('forms.form.sections.header-spacing', 'mt-2 mb-2');

        if ($isStepped) {
            $newFormBuild[] = '<div data-step="' . $step . '" class="form_step">';
        }

        if (! is_null($label)) {
            $newFormBuild[] = '<div class="' . $rowClass . '">';
            $newFormBuild[] = '<div class="' . $fullSizeColumn . '"><h4 class="' . $headerSpacing . '">' . $label . '</h4><hr></div>';
            $newFormBuild[] = '</div>';
        }

        foreach ($formChunks as $chunk) {
            $newFormBuild[] = '<div class="' . $rowClass . '">';

            foreach ($chunk as $element) {
                if (
                    Str::contains($element, 'type="hidden"')
                    && ! Str::contains($element, 'label')
                ) {
                    $class = '';
                } else {
                    $class = $columnBase . (12 / $columns);
                }

                $newFormBuild[] = '<div class="' . $class . '">';
                $newFormBuild[] = $element;
                $newFormBuild[] = '</div>';
            }
            $newFormBuild[] = '</div>';
        }

        if ($isStepped) {
            $newFormBuild[] = '</div>';
        }

        return implode('', $newFormBuild);
    }

    /**
     * Build a two column form using standard bootstrap classes
     *
     * @param  array $formBuild
     * @param  int $columns
     * @param  bool $isStepped
     * @return string
     */
    private function buildColumnForm($formBuild, $columns, $isStepped = false)
    {
        $formSections = [];
        $step = 0;

        foreach ($this->sections as $section => $fields) {
            $step++;
            $label = null;

            if (is_null($columns)) {
                $columns = count($fields);
            }

            if (is_string($section)) {
                $label = $section;
            }

            $inputs = [];

            foreach ($fields as $field) {
                if (! is_array($field) && isset($formBuild[$field])) {
                    $inputs[] = $formBuild[$field];
                }

                if (is_array($field)) {
                    foreach ($field as $inputField) {
                        $inputs[] = $formBuild[$inputField];
                    }
                }
            }

            $formSections[] = $this->buildSection($inputs, $columns, $label, $isStepped, $step);
        }

        return implode('', $formSections);
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
            if (! in_array($column, $badColumns)) {
                $type = DB::connection($this->connection)
                    ->getDoctrineColumn(DB::connection($this->connection)->getTablePrefix() . $table, $column)
                    ->getType()->getName();
                $tableTypeColumns[$column]['type'] = $type;
            }
        }

        return $tableTypeColumns;
    }

    /**
     * A list of normalized types
     *
     * @param string $type
     * @return string
     */
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
