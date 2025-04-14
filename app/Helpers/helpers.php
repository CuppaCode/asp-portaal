<?php

if (!function_exists('dot_to_comma')) {
    /**
     * Convert a dot-separated value to a comma-separated value.
     *
     * @param string $value
     * @return string
     */
    function dot_to_comma(string $value): string
    {
        return str_replace('.', ',', $value);
    }
}