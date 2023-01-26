<?php

namespace Grafite\Forms\Forms\Concerns;

trait HasIndex
{
    /**
     * The items loaded by the index
     *
     * @var mixed
     */
    public $items;

    /**
    * The headers with sort for the model index
    *
    * @return string
    */
    public function indexHeaders()
    {
        $headers = '';

        foreach ($this->parseVisibleFields($this->parseFields($this->fields())) as $header => $data) {
            $header = $data['label'] ?? $header;
            $header = ucfirst($header);
            $order = 'desc';

            if ($data['sortable']) {
                if (request('order') === 'desc') {
                    $order = 'asc';
                }

                if (request('order') === 'asc') {
                    $order = 'desc';
                }

                $sortLink = request()->url() . '?' . http_build_query(array_merge(
                    request()->all(),
                    [
                        'sort_by' => strtolower($header),
                        'order' => $order,
                    ]
                ));
                $icon = config('forms.html.sortable-icon', '&#8597;');

                if (request('order') && strtolower($header) === request('sort_by')) {
                    $direction = (request('order') === 'asc') ? 'up' : 'down';
                    $icon = config("forms.html.sortable-icon-{$direction}", '&#8597;');
                }

                $header = "<a href=\"{$sortLink}\">{$header} {$icon}</a>";
            }

            $class = '';

            if (! is_null($data['table_class'])) {
                $class = " class=\"{$data['table_class']}\"";
            }

            $headers .= "<th{$class}>{$header}</th>";
        }

        $headers .= config('forms.html.table-actions-header', '<th class="text-end">Actions</th>');

        return $headers;
    }

    /**
     * The index body for the model
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return string
     */
    public function indexBody($query = null)
    {
        $fields = $this->parseVisibleFields($this->parseFields($this->fields()));
        $sortBy = array_keys($fields)[0];
        $query = $query;

        if (is_null($query)) {
            $query = app($this->model);
        }

        if (! is_null($this->paginate)) {
            $this->items = $query
                ->with($this->with)
                ->orderBy(request('sort_by', $sortBy), request('order', 'asc'))
                ->paginate($this->paginate);
        } else {
            $this->items = $query
                ->with($this->with)
                ->orderBy(request('sort_by', $sortBy), request('order', 'asc'))
                ->get();
        }

        $rows = '';

        foreach ($this->items as $item) {
            $deleteButton = $this->delete($item);
            $editButton = $this->editButton($item);

            $rows .= '<tr>';

            foreach ($fields as $field => $data) {
                $class = '';

                if (! is_null($data['table_class'])) {
                    $class = " class=\"{$data['table_class']}\"";
                }

                $rows .= "<td{$class}>{$item->$field}</td>";
            }

            $rows .= '<td>';
            $rows .= ' <div class="btn-toolbar justify-content-end">';
            $rows .= $editButton;
            $rows .= $deleteButton;
            $rows .= '</div>';
            $rows .= '</td>';
            $rows .= '</tr>';
        }

        return $rows;
    }

    /**
     * The index method for the model
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return string
     */
    public function index($query = null)
    {
        $indexHeaders = $this->indexHeaders();
        $indexBody = $this->indexBody($query);
        $paginated = '';

        if (! is_null($this->paginate)) {
            $paginated = $this->paginated();
        }

        $spacing = config('forms.html.pagination', 'd-flex justify-content-center mt-4 mb-0');
        $tableClass = config('forms.html.table', 'table table-borderless m-0 p-0');
        $tableHeadClass = config('forms.html.table-head', 'thead');

        $this->html = <<<EOT
<table class="{$tableClass}">
    <thead class="{$tableHeadClass}">
        <tr>
            {$indexHeaders}
        </tr>
    </thead>
    <tbody>
        {$indexBody}
    </tbody>
</table>

<div class="{$spacing}">{$paginated}</div>
EOT;

        return $this;
    }

    /**
         * Convert the items from the index to JSON
         *
         * @return string
         */
    public function toJson()
    {
        return $this->items->toJson();
    }
}
