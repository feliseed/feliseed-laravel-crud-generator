<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands;

class Column
{
    public function __construct(
        public string $name,
        public string $type,
        public bool $nullable
    ) {}

}

