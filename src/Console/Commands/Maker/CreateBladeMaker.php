<?php

namespace Feliseed\LaravelCrudGenerator\Console\Commands\Maker;
use Illuminate\Support\Str;
use Feliseed\LaravelCrudGenerator\Console\Commands\DatabaseSchema;
use Feliseed\LaravelCrudGenerator\Console\Commands\Column;

class CreateBladeMaker {
    public function generate(DatabaseSchema $jsonSchema, String $modelName): void {
        
        $indexBlade = file_get_contents(__DIR__ . '/../../../../stubs/create.blade.stub');
        
        // 文字列を置換
        $indexBlade = str_replace('%%ROUTE_NAME%%', Str::kebab(Str::plural($modelName)), $indexBlade);
        $indexBlade = str_replace('%%PAGE_TITLE%%', Str::ucfirst(Str::plural($modelName)), $indexBlade);
        $indexBlade = str_replace('%%VARIABLE_SINGULAR%%', "$".Str::camel(Str::singular($modelName)), $indexBlade);
        $indexBlade = str_replace('%%INPUTS%%', $this->getInputs($jsonSchema, $modelName), $indexBlade);

        // publish
        file_put_contents(
            "resources/views/". Str::kebab(Str::plural($modelName)) ."/create.blade.php",
            $indexBlade
        );
    }

    protected function getInputs(DatabaseSchema $jsonSchema, String $modelName) : String {
        $result = '';

        
        foreach ($jsonSchema->columns as $column) {

            switch ($column->type) {
                case 'id':
                    break;
                case 'boolean':
                    $result .= $this->getLabel($column->name, !$column->nullable);
                    $result .= $this->getCheckbox(Str::camel(Str::singular($modelName)), $column->name);
                    $result .= $this->getValidationErrorMessage($column->name);
                    break;
                case 'text':
                    $result .= $this->getLabel($column->name, !$column->nullable);
                    $result .= $this->getTextarea(Str::camel(Str::singular($modelName)), $column->name, !$column->nullable);
                    $result .= $this->getValidationErrorMessage($column->name);
                    break;
                case 'date':
                    $result .= $this->getLabel($column->name, !$column->nullable);
                    $result .= $this->getDateInput(Str::camel(Str::singular($modelName)), $column->name, !$column->nullable);
                    $result .= $this->getValidationErrorMessage($column->name);
                    break;
                case 'time':
                    $result .= $this->getLabel($column->name, !$column->nullable);
                    $result .= $this->getTimeInput(Str::camel(Str::singular($modelName)), $column->name, !$column->nullable);
                    $result .= $this->getValidationErrorMessage($column->name);
                    break;
                default:
                    $result .= $this->getLabel($column->name, !$column->nullable);
                    $result .= $this->getTextInput(Str::camel(Str::singular($modelName)), $column->name, !$column->nullable);
                    $result .= $this->getValidationErrorMessage($column->name);
                    break;
            }

        }

        return $result;
    }

    protected function getDateInput(?string $model, string $column, bool $required, bool $isCreate = true): string
    {
        $id = "id='{$column}'";
        $name = "name='{$column}'";
        $value = $isCreate
            ? "value='{{ old(\"{$column}\") }}'"
            : "value='{{ old(\"{$column}\", \Carbon\Carbon::parse(\${$model}->{$column})->format('Y-m-d')) }}'";
        $required = $required ? "required" : "";
        $class= "class='block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6'";

        return "<input type='date' {$id} {$name} {$value} {$required} {$class} />";
    }

    protected function getTimeInput(?string $model, string $column, bool $required, bool $isCreate = true): string
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

    protected function getTextarea(?string $model, string $column, bool $required, bool $isCreate = true): string
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

    protected function getValidationErrorMessage(string $column): string
    {
        $message = "\$errors->first('{$column}')";
        $class = "class='mt-2 text-sm text-red-600'";
        
        return "@if ({$message})<p {$class}>{{ {$message} }}</p>@endif";
    }

    protected function getCheckbox(?string $model, string $column, bool $isCreate = true): string
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

    protected function getLabel(string $column, bool $required): string
    {
        $for = "for='{$column}'";
        $required = $required ? '<span class="text-red-500">*</span>' : '';
        $class = "class='block text-sm font-medium leading-6 text-gray-900'";

        return "<label for='text' {$for} {$class}>{$column}</label>";
    }

    protected function getTextInput(?string $model, string $column, bool $required, bool $isCreate = true): string
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
