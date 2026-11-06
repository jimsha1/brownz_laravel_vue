<?php

namespace VocomiApp\Http\Traits;

trait ConstantExporter
{
    protected function exportConstants($prefix = null)
    {
        $constants = (new \ReflectionClass(__CLASS__))->getConstants();

        if ($prefix === null) {
            return $constants;
        }

        return array_filter($constants, function($key) use($prefix) {
            return false !== strpos($key, $prefix);
        }, ARRAY_FILTER_USE_KEY);
    }
}
