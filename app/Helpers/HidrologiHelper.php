<?php

namespace App\Helpers;

class HidrologiHelper
{
    /**
     * Transform Python API summary keys to Indonesian keys expected by Laravel blade
     * 
     * @param array $summary
     * @return array
     */
    public static function transformSummaryKeys($summary)
    {
        if (!is_array($summary) || empty($summary)) {
            return $summary;
        }

        // Transform statistik_data keys
        if (isset($summary['statistik_data'])) {
            // Translate curah_rainfall -> curah_hujan
            if (isset($summary['statistik_data']['curah_rainfall'])) {
                $summary['statistik_data']['curah_hujan'] = $summary['statistik_data']['curah_rainfall'];
                unset($summary['statistik_data']['curah_rainfall']);
            }

            // Translate volume_reservoir -> volume_kolam_retensi
            if (isset($summary['statistik_data']['volume_reservoir'])) {
                $summary['statistik_data']['volume_kolam_retensi'] = $summary['statistik_data']['volume_reservoir'];
                unset($summary['statistik_data']['volume_reservoir']);
            }

            // Translate reliability_sistem -> keandalan_sistem
            if (isset($summary['statistik_data']['reliability_sistem'])) {
                $summary['statistik_data']['keandalan_sistem'] = $summary['statistik_data']['reliability_sistem'];
                unset($summary['statistik_data']['reliability_sistem']);
            }
        }

        // Transform hasil_analisis keys
        if (isset($summary['hasil_analisis'])) {
            // Translate supply_air -> pasokan_air
            if (isset($summary['hasil_analisis']['supply_air'])) {
                $summary['hasil_analisis']['pasokan_air'] = $summary['hasil_analisis']['supply_air'];
                unset($summary['hasil_analisis']['supply_air']);
            }

            // Translate water_supply_per_sector -> pasokan_air_per_sektor
            if (isset($summary['hasil_analisis']['water_supply_per_sector'])) {
                $summary['hasil_analisis']['pasokan_air_per_sektor'] = $summary['hasil_analisis']['water_supply_per_sector'];
                unset($summary['hasil_analisis']['water_supply_per_sector']);
            }

            // Translate water_sources -> sumber_air
            if (isset($summary['hasil_analisis']['water_sources'])) {
                $summary['hasil_analisis']['sumber_air'] = $summary['hasil_analisis']['water_sources'];
                unset($summary['hasil_analisis']['water_sources']);
            }

            // Translate economics -> ekonomi
            if (isset($summary['hasil_analisis']['economics'])) {
                $summary['hasil_analisis']['ekonomi'] = $summary['hasil_analisis']['economics'];
                unset($summary['hasil_analisis']['economics']);
            }

            // Translate water_quality -> kualitas_air
            if (isset($summary['hasil_analisis']['water_quality'])) {
                $summary['hasil_analisis']['kualitas_air'] = $summary['hasil_analisis']['water_quality'];
                unset($summary['hasil_analisis']['water_quality']);
            }

            // Translate ecosystem_health -> kesehatan_ekosistem
            if (isset($summary['hasil_analisis']['ecosystem_health'])) {
                $summary['hasil_analisis']['kesehatan_ekosistem'] = $summary['hasil_analisis']['ecosystem_health'];
                unset($summary['hasil_analisis']['ecosystem_health']);
            }
        }

        // Transform input_parameters -> input_parameterers (match typo in Python API)
        if (isset($summary['input_parameters'])) {
            $summary['input_parameterers'] = $summary['input_parameters'];
            unset($summary['input_parameters']);
        }

        // Transform nested keys in curah_hujan
        if (isset($summary['statistik_data']['curah_hujan'])) {
            self::transformNestedKeys($summary['statistik_data']['curah_hujan'], [
                'rata_rata' => 'rata_rata',
                'maximum' => 'maksimum',
                'minimum' => 'minimum',
                'total' => 'total'
            ]);
        }

        // Transform nested keys in volume_kolam_retensi
        if (isset($summary['statistik_data']['volume_kolam_retensi'])) {
            self::transformNestedKeys($summary['statistik_data']['volume_kolam_retensi'], [
                'rata_rata' => 'rata_rata',
                'maximum' => 'maksimum',
                'minimum' => 'minimum',
                'akhir_periode' => 'akhir_periode'
            ]);
        }

        // Transform pasokan_air nested keys
        if (isset($summary['hasil_analisis']['pasokan_air'])) {
            self::transformNestedKeys($summary['hasil_analisis']['pasokan_air'], [
                'total_supply' => 'total_supply',
                'total_demand' => 'total_demand',
                'defisit' => 'defisit',
                'status_supply' => 'status_pasokan'
            ]);
        }

        return $summary;
    }

    /**
     * Transform nested array keys
     * 
     * @param array &$array
     * @param array $keyMap
     * @return void
     */
    private static function transformNestedKeys(&$array, $keyMap)
    {
        foreach ($keyMap as $oldKey => $newKey) {
            if ($oldKey !== $newKey && isset($array[$oldKey])) {
                $array[$newKey] = $array[$oldKey];
                if ($oldKey !== $newKey) {
                    unset($array[$oldKey]);
                }
            }
        }
    }
}
