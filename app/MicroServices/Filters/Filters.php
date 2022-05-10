<?php

namespace App\MicroServices\Filters;


abstract class Filters
{
    protected $builder;

    protected $filter_methods = [];

    protected $filter_value;

    /**
     * @throws \Exception
     */
    public abstract function apply($builder, $filter, $filter_value);
}
