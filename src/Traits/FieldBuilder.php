<?php

namespace Sentgine\Crudwizard\Traits;

use Illuminate\Support\Str;

trait FieldBuilder
{
    /**
     * Build the fillable fields string content.
     *
     * @param array $fields An array of fields containing field_name values.
     * @param bool $withCloseOpenBracket Whether to include the closing/opening bracket in the generated string.
     * 
     * @return string The generated string containing field names.
     */
    protected function buildFillableFields(array $fields, bool $withCloseOpenBracket = true): string
    {
        $string = '';

        // Initialize the string with an opening bracket.
        $string .= $withCloseOpenBracket ? '[' : '';

        // Iterate over each field in the $fields array.
        foreach ($fields as $value) {
            // Append the field_name enclosed in single quotes and followed by a comma.
            $string .= "'" . $value['field_name'] . "',";
        }

        // Close the string with a closing bracket.
        $string .= $withCloseOpenBracket ? ']' : '';

        // Return the generated string.
        return $string;
    }

    /**
     * Build migration fields based on the provided array.
     *
     * @param array $fields The array of fields to generate migration fields from.
     * @return string The generated migration fields as a string.
     */
    protected function buildMigrationFields(array $fields): string
    {
        $indentation = "\n\t\t\t";
        $string = '';
        $fieldType = '';
        $attribute = '';

        // Iterate over each field in the $fields array.
        foreach ($fields as $value) {
            $string .= $indentation;

            switch ($value['field_type']) {
                case 'email':
                    $fieldType = 'string';
                    $attribute = '->unique()';
                    break;
                case 'integer':
                    $fieldType = 'bigInteger';
                    $attribute = '';
                    break;
                default:
                    $fieldType = $value['field_type'];
                    $attribute = '';
                    break;
            }

            // Append the field_name enclosed in single quotes and followed by a comma.
            $string .= '$table->' . $fieldType . "('" . $value['field_name'] . "')";
            $string .= $attribute . '->nullable()';
            $string .= ';';
        }

        // Return the generated string.
        return $string;
    }

    /**
     * Builds a string of factory fields based on the provided array.
     *
     * @param array $fields An array containing the field information.
     * @return string The generated string of factory fields.
     */
    protected function buildFactoryFields(array $fields): string
    {
        $indentation = "\n\t\t\t";
        $string = '';

        // Iterate over each field in the $fields array.
        foreach ($fields as $value) {
            $string .= $indentation;

            // Append the field_name enclosed in single quotes and followed by a comma.
            switch ($value['field_type']) {
                case 'email':
                    $string .= "'" . $value['field_name'] . "' => fake()->safeEmail(),"; // Field type is email, generate a safe email.
                    break;
                case 'date':
                    $string .= "'" . $value['field_name'] . "' => fake()->date(),"; // Field type is date, generate a random date.
                    break;
                case 'dateTime':
                    $string .= "'" . $value['field_name'] . "' => fake()->dateTime(),"; // Field type is dateTime, generate a random date and time.
                    break;
                default:
                    $string .= "'" . $value['field_name'] . "' => fake()->text(),"; // Field type is unknown, generate random text.
                    break;
            }
        }

        // Return the generated string.
        return $string;
    }

    /**
     * Builds a string of request fields based on the provided array.
     *
     * @param array $fields An array containing the field information.
     * @return string The generated string of request fields.
     */
    protected function buildRequestFields(array $fields): string
    {
        $indentation = "\n\t\t\t";
        $string = '';

        // Iterate over each field in the $fields array.
        foreach ($fields as $value) {
            $string .= $indentation;
            $string .= "'" . $value['field_name'] . "' => ['nullable'],"; // Field name with 'nullable' rule.
        }

        // Return the generated string.
        return $string;
    }

    /**
     * Build the header fields string for a table.
     *
     * @param array $fields The array of fields.
     * @return string The generated string of header fields.
     */
    protected function buildHeaderFields(array $fields): string
    {
        $indentation = "\n\t\t\t\t\t";
        $string = '';

        // Iterate over each field in the $fields array.
        foreach ($fields as $value) {
            $string .= $indentation;
            $fieldName = Str::title(Str::replace('_', ' ', $value['field_name']));
            $string .= '<th class="crudwizard-th">' . $fieldName . '</th>'; // Field name with 'nullable' rule.
        }

        // Return the generated string.
        return $string;
    }

    /**
     * Build the table data fields string for a table row.
     *
     * @param array $fields The array of fields.
     * @param string $resourceName The name of the resource.
     * @return string The generated string of table data fields.
     */
    protected function buildTableDataFields(array $fields, string $resourceName): string
    {
        $indentation = "\n\t\t\t\t\t";
        $string = '';

        // Iterate over each field in the $fields array.
        foreach ($fields as $value) {
            $string .= $indentation;
            $string .= '<td class="crudwizard-td">{{ $' . $resourceName . '->' . $value['field_name'] . ' }}</td>'; // Field name with 'nullable' rule.
        }

        // Return the generated string.
        return $string;
    }
}
