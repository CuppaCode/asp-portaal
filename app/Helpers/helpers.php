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

if (!function_exists('format_license_plate')) {
    /**
     * Format a Dutch license plate according to RDW standards.
     * Supports various Dutch license plate formats (sidecode 1-14).
     *
     * @param string|null $plate
     * @return string|null
     */
    function format_license_plate($plate): ?string
    {
        if (is_null($plate) || $plate === '') {
            return $plate;
        }

        // Remove all non-alphanumeric characters and convert to uppercase
        $cleaned = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $plate));

        if (strlen($cleaned) < 4 || strlen($cleaned) > 8) {
            return $cleaned; // Return as-is if length doesn't match known formats
        }

        // Dutch license plate patterns (sidecodes)
        $patterns = [
            // Sidecode 1: XX-99-99 (2 letters, 2 numbers, 2 numbers)
            '/^([A-Z]{2})(\d{2})(\d{2})$/' => '$1-$2-$3',
            // Sidecode 2: 99-99-XX (2 numbers, 2 numbers, 2 letters)
            '/^(\d{2})(\d{2})([A-Z]{2})$/' => '$1-$2-$3',
            // Sidecode 3: 99-XX-99 (2 numbers, 2 letters, 2 numbers)
            '/^(\d{2})([A-Z]{2})(\d{2})$/' => '$1-$2-$3',
            // Sidecode 4: XX-99-XX (2 letters, 2 numbers, 2 letters)
            '/^([A-Z]{2})(\d{2})([A-Z]{2})$/' => '$1-$2-$3',
            // Sidecode 5: XX-XX-99 (2 letters, 2 letters, 2 numbers)
            '/^([A-Z]{2})([A-Z]{2})(\d{2})$/' => '$1-$2-$3',
            // Sidecode 6: 99-XX-XX (2 numbers, 2 letters, 2 letters)
            '/^(\d{2})([A-Z]{2})([A-Z]{2})$/' => '$1-$2-$3',
            // Sidecode 7: 99-XXX-9 (2 numbers, 3 letters, 1 number)
            '/^(\d{2})([A-Z]{3})(\d{1})$/' => '$1-$2-$3',
            // Sidecode 8: 9-XXX-99 (1 number, 3 letters, 2 numbers)
            '/^(\d{1})([A-Z]{3})(\d{2})$/' => '$1-$2-$3',
            // Sidecode 9: XX-999-X (2 letters, 3 numbers, 1 letter)
            '/^([A-Z]{2})(\d{3})([A-Z]{1})$/' => '$1-$2-$3',
            // Sidecode 10: X-999-XX (1 letter, 3 numbers, 2 letters)
            '/^([A-Z]{1})(\d{3})([A-Z]{2})$/' => '$1-$2-$3',
            // Sidecode 11: XXX-99-X (3 letters, 2 numbers, 1 letter)
            '/^([A-Z]{3})(\d{2})([A-Z]{1})$/' => '$1-$2-$3',
            // Sidecode 12: X-99-XXX (1 letter, 2 numbers, 3 letters)
            '/^([A-Z]{1})(\d{2})([A-Z]{3})$/' => '$1-$2-$3',
            // Sidecode 13: 9-XX-999 (1 number, 2 letters, 3 numbers)
            '/^(\d{1})([A-Z]{2})(\d{3})$/' => '$1-$2-$3',
            // Sidecode 14: 999-XX-9 (3 numbers, 2 letters, 1 number)
            '/^(\d{3})([A-Z]{2})(\d{1})$/' => '$1-$2-$3',
        ];

        foreach ($patterns as $pattern => $replacement) {
            if (preg_match($pattern, $cleaned)) {
                return preg_replace($pattern, $replacement, $cleaned);
            }
        }

        // If no pattern matches, return cleaned version
        return $cleaned;
    }
}