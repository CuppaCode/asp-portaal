<?php

if (!function_exists('dot_to_comma')) {
    /**
     * Convert a dot-separated value to a comma-separated value.
     *
     * @param string|null $value
     * @return string|null
     */
    function dot_to_comma($value): ?string
    {
        if (is_null($value) || $value === '') {
            return $value;
        }

        return str_replace('.', ',', $value);
    }
}