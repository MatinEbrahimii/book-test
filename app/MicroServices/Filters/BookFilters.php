<?php

namespace App\MicroServices\Filters;

use Exception;

class BookFilters extends Filters
{
    protected $filter_methods = [
        'title' => 'title',
        'authors' => 'authors'
    ];

    public function apply($builder, $filter, $filter_value)
    {
        $this->builder = $builder;
        $this->filter_value = $filter_value;

        if (is_null($filter)) {
            return $this->builder;
        }

        if (!method_exists($this, $method = $this->filter_methods[$filter])) {
            throw new Exception('This filter doesnt exist');
        }

        $this->$method();

        return $this->builder;
    }

    protected function title()
    {
        return $this->builder->where('title', 'like', '%' . $this->filter_value . '%');
    }

    protected function authors()
    {
        $authors_ids = explode(',', $this->filter_value);

        return $this->builder->whereHas('authors', function ($query) use ($authors_ids) {
            $query->whereIn('id', $authors_ids);
        });
    }
}
