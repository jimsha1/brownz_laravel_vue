<?php

/**
 *
 * @param array $data_array
 * @param string $key_column
 * @param string $value_column
 * @param string|boolean $default
 * @return \Illuminate\Support\HtmlString
 */
function voco_array_dropdown(array $data_array, $key_column, $value_column, $default = false)
{
    $html = '';
    foreach ($data_array as $row) {
        $html .= '<option value="' . $row[$key_column] . '"'
                . ($default == $row[$key_column] ? ' selected' : '')
                . '>' . $row[$value_column] . '</option>';
    }

    return new \Illuminate\Support\HtmlString($html);
}

/**
 *
 * @param array $data_array
 * @param string|boolean $default
 * @return \Illuminate\Support\HtmlString
 */
function voco_nvp_dropdown(array $data_array, $default = false)
{
    $html = '';
    foreach ($data_array as $name => $value) {
        $html .= '<option value="' . $name . '"'
                . ($default == $name ? ' selected' : '')
                . '>' . $value . '</option>';
    }

    return new \Illuminate\Support\HtmlString($html);
}

/**
 *
 * @param array $data_array
 * @param string|boolean $default
 * @return \Illuminate\Support\HtmlString
 */
function voco_scalar_dropdown(array $data_array, $default = false)
{
    $html = '';
    foreach ($data_array as $value) {
        $html .= '<option value="' . $value . '"'
                . ($default == $value ? ' selected' : '')
                . '>' . ucwords(str_replace(['_', '-'], ' ', $value)) . '</option>';
    }

    return new \Illuminate\Support\HtmlString($html);
}
