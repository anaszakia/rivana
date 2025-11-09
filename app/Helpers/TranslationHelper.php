<?php

if (!function_exists('trans_api')) {
    /**
     * Translate API values from Indonesian to current locale
     * 
     * @param string|null $value The Indonesian value from API
     * @param string|null $context The category context (e.g., 'status_pasokan', 'kategori_risiko')
     * @return string Translated value or original if not found
     */
    function trans_api($value, $context = null)
    {
        // Return original if value is null or empty
        if (empty($value)) {
            return $value;
        }

        // Trim whitespace
        $value = trim($value);

        // Load API translations config
        $apiTranslations = config('api_translations', []);

        // If context is provided, search in that specific category
        if ($context && isset($apiTranslations[$context])) {
            // Try exact match first
            $translationKey = $apiTranslations[$context][$value] ?? null;
            
            if ($translationKey) {
                return __("messages.{$translationKey}");
            }
            
            // Try partial match (for values with descriptions)
            foreach ($apiTranslations[$context] as $apiValue => $key) {
                if (stripos($value, $apiValue) !== false || stripos($apiValue, $value) !== false) {
                    return __("messages.{$key}");
                }
            }
        }

        // If no context or not found, search across all categories
        foreach ($apiTranslations as $category => $mappings) {
            // Try exact match
            if (isset($mappings[$value])) {
                $translationKey = $mappings[$value];
                return __("messages.{$translationKey}");
            }
            
            // Try partial match
            foreach ($mappings as $apiValue => $key) {
                if (stripos($value, $apiValue) !== false || stripos($apiValue, $value) !== false) {
                    return __("messages.{$key}");
                }
            }
        }

        // If no mapping found, return original value
        return $value;
    }
}
