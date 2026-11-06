<?php

use Illuminate\Support\Str;

/**
 * Laravel's Str::limit() won't preserve words. Use this function in such cases.
 *
 * @param string $string
 * @return string
 */
function voco_slug($string, $max_length = 60)
{
    // https://stackoverflow.com/a/7729790
    /*
     * $re_explained = '/(?#! splitCamelCase Rev:20140412)
     * # Split camelCase "words". Two global alternatives. Either g1of2:
     *   (?<=[a-z])      # Position is after a lowercase,
     *   (?=[A-Z])       # and before an uppercase letter.
     * | (?<=[A-Z])      # Or g2of2; Position is after uppercase,
     *   (?=[A-Z][a-z])  # and before upper-then-lower case.
     * /x';
     */

    // Characters to be used for splitting string
    $split_chars = '`~!@#$%^&*()_-+={}[]|<>,.?/;:"\'\\';

    // Replace each of the special characters with single space
    $string = str_replace(str_split($split_chars), ' ', $string);

    // Split "VocoERPWeb" to ['Voco', 'ERP', 'Web']
    $optimal_words = preg_split('/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/x', $string);

    // wordwrap separated by '@'
    $wrapped_text = wordwrap(Str::slug(implode(' ', $optimal_words), ' '), $max_length, '@', true);

    return str_replace(' ', '-', current(explode('@', $wrapped_text)));
}
