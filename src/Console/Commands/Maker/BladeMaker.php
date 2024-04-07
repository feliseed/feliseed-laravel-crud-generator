<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;
use Exception;

abstract class BladeMaker extends FileMaker {

    protected static function getTimeInput(?string $model, string $column, bool $required, bool $isCreate = true): string
    {
        $id = "id='{$column}'";
        $name = "name='{$column}'";
        $value = $isCreate
            ? "value='{{ old(\"{$column}\") }}'"
            : "value='{{ old(\"{$column}\", \Carbon\Carbon::parse(\${$model}->{$column})->format('H:i')) }}'";
        $required = $required ? "required" : "";
        $class= "class='block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6'";

        return "<input type='time' {$id} {$name} {$value} {$required} {$class} />";
    }

    protected static function getTextarea(?string $model, string $column, bool $required, bool $isCreate = true): string
    {
        $id = "id='{$column}'";
        $name = "name='{$column}'";
        $required = $required ? "required" : "";
        $class= "class='block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6'";
        $value = $isCreate
            ? "{{ old('{$column}') }}"
            : "{{ old('{$column}', \${$model}->{$column}) }}";

        return "<textarea {$id} {$name} {$required} {$class}>{$value}</textarea>";
    }

    protected static function getValidationErrorMessage(string $column): string
    {
        $message = "\$errors->first('{$column}')";
        $class = "class='mt-2 text-sm text-red-600'";
        
        return "@if ({$message})<p {$class}>{{ {$message} }}</p>@endif";
    }

    protected static function getCheckbox(?string $model, string $column, bool $isCreate = true): string
    {
        $id = "id='{$column}'";
        $name = "name='{$column}'";
        $value = "value='1'";
        $checked = $isCreate
            ? "@checked(old('{$column}'))"
            : "@checked(old('{$column}', \${$model}->{$column}))";
        $class= "class='h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-100'";

        return "<input type='hidden' {$name} value='0'>\n"
            . "<input type='checkbox' {$id} {$name} {$value} {$checked} {$class} />";
    }

    protected static function getLabel(string $column, bool $required): string
    {
        $for = "for='{$column}'";
        $required = $required ? '<span class="text-red-500">*</span>' : '';
        $class = "class='block text-sm font-medium leading-6 text-gray-900'";

        return "<label for='text' {$for} {$class}>{$column}</label>";
    }

    protected static function getTextInput(?string $model, string $column, bool $required, bool $isCreate = true): string
    {
        $id = "id='{$column}'";
        $name = "name='{$column}'";
        $value = $isCreate
            ? "value='{{ old(\"{$column}\") }}'"
            : "value='{{ old(\"{$column}\", \${$model}->{$column}) }}'";
        $required = $required ? "required" : "";
        $class= "class='block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6'";

        return "<input type='text' {$id} {$name} {$value} {$required} {$class} />";
    }
}

class UnExpectedColumnTypeException extends Exception {}
