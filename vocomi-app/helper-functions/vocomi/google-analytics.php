<?php

/**
 *
 * @return \Illuminate\Support\HtmlString
 */
function voco_ga($code = null)
{
    if (null === $code) {
        $code = config('vocomi.settings.gacode');
    }

    return new \Illuminate\Support\HtmlString(
            '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $code . '"></script>'
            . '<script>'
            . 'window.dataLayer=window.dataLayer||[];'
            . 'function gtag(){dataLayer.push(arguments);}'
            . 'gtag(\'js\',new Date());'
            . 'gtag(\'config\',\'' . $code . '\');'
            . '</script>'
    );
}
