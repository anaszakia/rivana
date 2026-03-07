<?php

if (!function_exists('trans_api')) {
    /**
     * Translate API response strings (from Python/Indonesian) to current app locale.
     *
     * Usage in Blade:
     *   {{ trans_api($value, 'status_keandalan') }}
     *   {{ trans_api($value, 'priority') }}
     *
     * @param  string|null  $value   The raw value from API (usually Indonesian)
     * @param  string       $group   Translation group key
     * @return string
     */
    function trans_api(?string $value, string $group = 'general'): string
    {
        if ($value === null || $value === '' || $value === 'N/A') {
            return $value ?? 'N/A';
        }

        $lang = app()->getLocale(); // 'en' or 'id'

        // If Indonesian locale, return as-is
        if ($lang === 'id') {
            return $value;
        }

        // ==========================================
        // TRANSLATION MAPS  (ID → EN)
        // ==========================================

        $maps = [

            // ------------------------------------------
            // status_keandalan  (system reliability)
            // ------------------------------------------
            'status_keandalan' => [
                'Sangat Andal (≥90%)'    => 'Excellent Reliability (≥90%)',
                'Andal (75-90%)'         => 'Good Reliability (75-90%)',
                'Cukup Andal (60-75%)'   => 'Fair Reliability (60-75%)',
                'Kurang Andal (<60%)'    => 'Poor Reliability (<60%)',
                'Sangat Baik'            => 'Excellent',
                'Baik'                   => 'Good',
                'Cukup Baik'             => 'Fair',
                'Perlu Perhatian'        => 'Needs Attention',
                'SANGAT BAIK'            => 'EXCELLENT',
                'BAIK'                   => 'GOOD',
                'CUKUP BAIK'             => 'FAIR',
                'PERLU PERHATIAN'        => 'NEEDS ATTENTION',
            ],

            // ------------------------------------------
            // status_balance  (water balance)
            // ------------------------------------------
            'status_balance' => [
                'Sangat Baik (Error <5%)'   => 'Excellent (Error <5%)',
                'Baik (Error 5-10%)'        => 'Good (Error 5-10%)',
                'Cukup (Error 10-20%)'      => 'Fair (Error 10-20%)',
                'Buruk (Error >20%)'        => 'Poor (Error >20%)',
                'SANGAT BAIK'               => 'EXCELLENT',
                'BAIK'                      => 'GOOD',
                'CUKUP'                     => 'FAIR',
                'BURUK'                     => 'POOR',
                // English pass-through (API may already return English)
                'Excellent (Error <5%)'     => 'Excellent (Error <5%)',
                'Good (Error 5-10%)'        => 'Good (Error 5-10%)',
                'Fair (Error 10-20%)'       => 'Fair (Error 10-20%)',
                'Poor (Error >20%)'         => 'Poor (Error >20%)',
            ],

            // ------------------------------------------
            // status_ekosistem  (ecosystem health)
            // ------------------------------------------
            'status_ekosistem' => [
                'Sangat Sehat (≥80%)'    => 'Very Healthy (≥80%)',
                'Sehat (60-80%)'         => 'Healthy (60-80%)',
                'Cukup Sehat (40-60%)'   => 'Fair Health (40-60%)',
                'Kurang Sehat (<40%)'    => 'Poor Health (<40%)',
                'SANGAT SEHAT'           => 'VERY HEALTHY',
                'SEHAT'                  => 'HEALTHY',
                'CUKUP SEHAT'            => 'FAIR HEALTH',
                'KURANG SEHAT'           => 'POOR HEALTH',
                'Sangat Baik'            => 'Excellent',
                'Baik'                   => 'Good',
                'Cukup'                  => 'Fair',
                'Kurang'                 => 'Poor',
                // English pass-through
                'Very Healthy (≥80%)'    => 'Very Healthy (≥80%)',
                'Healthy (60-80%)'       => 'Healthy (60-80%)',
                'Fair Health (40-60%)'   => 'Fair Health (40-60%)',
                'Poor Health (<40%)'     => 'Poor Health (<40%)',
            ],

            // ------------------------------------------
            // priority  (recommendation priority)
            // ------------------------------------------
            'priority' => [
                'Tinggi'                    => 'High',
                'TINGGI'                    => 'HIGH',
                'Prioritas Tinggi'          => 'High Priority',
                'Sedang'                    => 'Medium',
                'SEDANG'                    => 'MEDIUM',
                'Prioritas Sedang'          => 'Medium Priority',
                'Normal'                    => 'Normal',
                'NORMAL'                    => 'NORMAL',
                'Prioritas Normal'          => 'Normal Priority',
                'Rendah'                    => 'Low',
                'RENDAH'                    => 'LOW',
                // English pass-through
                'High'                      => 'High',
                'HIGH'                      => 'HIGH',
                'High Priority'             => 'High Priority',
                'Medium'                    => 'Medium',
                'MEDIUM'                    => 'MEDIUM',
                'Medium Priority'           => 'Medium Priority',
                'Low'                       => 'Low',
                'LOW'                       => 'LOW',
            ],

            // ------------------------------------------
            // category  (recommendation/improvement category)
            // ------------------------------------------
            'category' => [
                'Sedimentasi'               => 'Sedimentation',
                'Konservasi Tanah'          => 'Soil Conservation',
                'Kapasitas Kolam Retensi'   => 'Retention Pond Capacity',
                'Mitigasi Banjir'           => 'Flood Mitigation',
                'Mitigasi Kekeringan'       => 'Drought Mitigation',
                'Infrastruktur'             => 'Infrastructure',
                'Pemeliharaan Rutin'        => 'Routine Maintenance',
                'Keandalan Sistem'          => 'System Reliability',
                'Pasokan Air'               => 'Water Supply',
                'Kualitas Air'              => 'Water Quality',
                'Kesehatan Ekosistem'       => 'Ecosystem Health',
                'Keseimbangan Air'          => 'Water Balance',
                // English pass-through
                'Sedimentation'             => 'Sedimentation',
                'Soil Conservation'         => 'Soil Conservation',
                'Retention Pond Capacity'   => 'Retention Pond Capacity',
                'Flood Mitigation'          => 'Flood Mitigation',
                'Drought Mitigation'        => 'Drought Mitigation',
                'Infrastructure'            => 'Infrastructure',
                'Routine Maintenance'       => 'Routine Maintenance',
                'System Reliability'        => 'System Reliability',
                'Water Supply'              => 'Water Supply',
                'Water Quality'             => 'Water Quality',
                'Ecosystem Health'          => 'Ecosystem Health',
            ],

            // ------------------------------------------
            // source  (water source keys from analysis_results)
            // ------------------------------------------
            'source' => [
                'sungai'                    => 'River',
                'river'                     => 'River',
                'Sungai'                    => 'River',
                'River'                     => 'River',
                'pengalihan'                => 'Diversion',
                'diversion'                 => 'Diversion',
                'Pengalihan'                => 'Diversion',
                'Diversion'                 => 'Diversion',
                'air_tanah'                 => 'Groundwater',
                'groundwater'               => 'Groundwater',
                'Air Tanah'                 => 'Groundwater',
                'Groundwater'               => 'Groundwater',
                'hujan'                     => 'Rainfall Harvest',
                'Hujan'                     => 'Rainfall Harvest',
                'rainfall'                  => 'Rainfall Harvest',
            ],

            // ------------------------------------------
            // sector  (water sector keys)
            // ------------------------------------------
            'sector' => [
                'rumah_tangga'              => 'Domestic',
                'domestik'                  => 'Domestic',
                'domestic'                  => 'Domestic',
                'Rumah Tangga'              => 'Domestic',
                'Domestik'                  => 'Domestic',
                'pertanian'                 => 'Agriculture',
                'agriculture'               => 'Agriculture',
                'Pertanian'                 => 'Agriculture',
                'Agriculture'               => 'Agriculture',
                'industri'                  => 'Industry',
                'industry'                  => 'Industry',
                'Industri'                  => 'Industry',
                'Industry'                  => 'Industry',
                'lingkungan'                => 'Environmental',
                'environmental'             => 'Environmental',
                'Lingkungan'                => 'Environmental',
                'Environmental'             => 'Environmental',
            ],

            // ------------------------------------------
            // status_umum  (general job / system status)
            // ------------------------------------------
            'status_umum' => [
                'pending'                   => 'Pending',
                'submitted'                 => 'Submitted',
                'processing'                => 'Processing',
                'completed'                 => 'Completed',
                'completed_with_warning'    => 'Completed (with warnings)',
                'failed'                    => 'Failed',
                'cancelled'                 => 'Cancelled',
                'selesai'                   => 'Completed',
                'Selesai'                   => 'Completed',
                'gagal'                     => 'Failed',
                'Gagal'                     => 'Failed',
                'dibatalkan'                => 'Cancelled',
                'Dibatalkan'                => 'Cancelled',
                // Water balance status
                'Normal'                    => 'Normal',
                'NORMAL'                    => 'NORMAL',
                'Baik'                      => 'Good',
                'BAIK'                      => 'GOOD',
                'Buruk'                     => 'Poor',
                'BURUK'                     => 'POOR',
            ],

            // ------------------------------------------
            // general  (fallback group)
            // ------------------------------------------
            'general' => [
                // Status words
                'Sangat Baik'               => 'Excellent',
                'SANGAT BAIK'               => 'EXCELLENT',
                'Baik'                      => 'Good',
                'BAIK'                      => 'GOOD',
                'Cukup'                     => 'Fair',
                'CUKUP'                     => 'FAIR',
                'Kurang'                    => 'Poor',
                'KURANG'                    => 'POOR',
                'Buruk'                     => 'Poor',
                'BURUK'                     => 'POOR',
                'Normal'                    => 'Normal',
                'NORMAL'                    => 'NORMAL',
                'Optimal'                   => 'Optimal',
                'OPTIMAL'                   => 'OPTIMAL',
                'Rendah'                    => 'Low',
                'RENDAH'                    => 'LOW',
                'Sedang'                    => 'Medium',
                'SEDANG'                    => 'MEDIUM',
                'Tinggi'                    => 'High',
                'TINGGI'                    => 'HIGH',
                // Soil/infiltration
                'Kering (<20 mm)'           => 'Dry (<20 mm)',
                'Optimal (20-40 mm)'        => 'Optimal (20-40 mm)',
                'Jenuh (>40 mm)'            => 'Saturated (>40 mm)',
                'Rendah'                    => 'Low',
                // Priority
                'Tinggi'                    => 'High',
                'Sedang'                    => 'Medium',
                'Normal'                    => 'Normal',
            ],
        ];

        // Look up in specific group first, then fall back to 'general'
        $map = $maps[$group] ?? [];
        if (isset($map[$value])) {
            return $map[$value];
        }

        // Try general as fallback
        if ($group !== 'general' && isset($maps['general'][$value])) {
            return $maps['general'][$value];
        }

        // Return original value unchanged (already English, or untranslatable)
        return $value;
    }
}


if (!function_exists('format_idr')) {
    /**
     * Format number as Indonesian Rupiah shorthand.
     * e.g. 1500000 -> "Rp 1,5 juta"
     */
    function format_idr(float $amount): string
    {
        if ($amount >= 1_000_000_000) {
            return 'Rp ' . number_format($amount / 1_000_000_000, 1) . ' miliar';
        }
        if ($amount >= 1_000_000) {
            return 'Rp ' . number_format($amount / 1_000_000, 1) . ' juta';
        }
        if ($amount >= 1_000) {
            return 'Rp ' . number_format($amount / 1_000, 1) . ' ribu';
        }
        return 'Rp ' . number_format($amount, 0);
    }
}


if (!function_exists('hydro_status_class')) {
    /**
     * Return Tailwind CSS classes for a status badge.
     * Usage: <span class="{{ hydro_status_class($status) }}">{{ $status }}</span>
     */
    function hydro_status_class(string $status): string
    {
        $map = [
            // Green — good
            'completed'             => 'bg-green-100 text-green-800',
            'Sangat Baik'           => 'bg-green-100 text-green-800',
            'SANGAT BAIK'           => 'bg-green-100 text-green-800',
            'Excellent'             => 'bg-green-100 text-green-800',
            'EXCELLENT'             => 'bg-green-100 text-green-800',
            'Very Healthy'          => 'bg-green-100 text-green-800',
            'Optimal'               => 'bg-green-100 text-green-800',
            'OPTIMAL'               => 'bg-green-100 text-green-800',
            'Surplus'               => 'bg-green-100 text-green-800',
            'SURPLUS'               => 'bg-green-100 text-green-800',

            // Blue — normal/good
            'Baik'                  => 'bg-blue-100 text-blue-800',
            'BAIK'                  => 'bg-blue-100 text-blue-800',
            'Good'                  => 'bg-blue-100 text-blue-800',
            'GOOD'                  => 'bg-blue-100 text-blue-800',
            'Normal'                => 'bg-blue-100 text-blue-800',
            'NORMAL'                => 'bg-blue-100 text-blue-800',
            'Seimbang'              => 'bg-blue-100 text-blue-800',
            'Balanced'              => 'bg-blue-100 text-blue-800',

            // Yellow — fair/warning
            'Cukup'                 => 'bg-yellow-100 text-yellow-800',
            'CUKUP'                 => 'bg-yellow-100 text-yellow-800',
            'Fair'                  => 'bg-yellow-100 text-yellow-800',
            'FAIR'                  => 'bg-yellow-100 text-yellow-800',
            'completed_with_warning'=> 'bg-yellow-100 text-yellow-800',
            'Sedang'                => 'bg-yellow-100 text-yellow-800',
            'SEDANG'                => 'bg-yellow-100 text-yellow-800',

            // Orange — needs attention
            'Perlu Perhatian'       => 'bg-orange-100 text-orange-800',
            'Needs Attention'       => 'bg-orange-100 text-orange-800',
            'Rendah'                => 'bg-orange-100 text-orange-800',
            'Low'                   => 'bg-orange-100 text-orange-800',

            // Red — poor/critical
            'failed'                => 'bg-red-100 text-red-800',
            'Buruk'                 => 'bg-red-100 text-red-800',
            'BURUK'                 => 'bg-red-100 text-red-800',
            'Poor'                  => 'bg-red-100 text-red-800',
            'POOR'                  => 'bg-red-100 text-red-800',
            'Kurang'                => 'bg-red-100 text-red-800',
            'KURANG'                => 'bg-red-100 text-red-800',
            'Defisit'               => 'bg-red-100 text-red-800',
            'Deficit'               => 'bg-red-100 text-red-800',

            // Gray — neutral
            'pending'               => 'bg-gray-100 text-gray-800',
            'cancelled'             => 'bg-gray-100 text-gray-800',
        ];

        return $map[$status] ?? 'bg-gray-100 text-gray-700';
    }
}