<?php

/**
 *
 * @return \Illuminate\Support\HtmlString
 */
function top_ajax_results()
{
    return new \Illuminate\Support\HtmlString(
            '<div class="card-search-results"></div>'
    );
}

/**
 *
 * @return \Illuminate\Support\HtmlString
 */
function fa_icon($class, $extra_class = '')
{
    return new \Illuminate\Support\HtmlString(
            '<i class="fa fa-' . $class . ' ' . $extra_class . '" aria-hidden="true"></i>'
    );
}
