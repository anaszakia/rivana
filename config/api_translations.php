<?php

/**
 * API Value Translation Mapping
 * 
 * Maps Indonesian values from Python API to translation keys
 * This allows flexible bilingual support without modifying the Python backend
 */

return [
    
    // Status Pasokan Air (dengan deskripsi lengkap)
    'status_pasokan' => [
        'Surplus' => 'api.water_supply.surplus',
        'Defisit' => 'api.water_supply.deficit',
        'Seimbang' => 'api.water_supply.balanced',
        // Status dengan deskripsi lengkap
        'Defisit - Pasokan kurang dari kebutuhan' => 'api.water_supply.deficit_insufficient',
        'Surplus - Pasokan berlebih' => 'api.water_supply.surplus_excess',
        'Seimbang - Pasokan cukup' => 'api.water_supply.balanced_sufficient',
    ],
    
    // Kategori Risiko (dengan deskripsi lengkap)
    'kategori_risiko' => [
        'Rendah' => 'api.risk_category.low',
        'Sedang' => 'api.risk_category.medium',
        'Tinggi' => 'api.risk_category.high',
        'Sangat Tinggi' => 'api.risk_category.very_high',
        // Status dengan deskripsi lengkap
        'Rendah - Aman' => 'api.risk_category.low_safe',
        'Sedang - Perlu monitoring' => 'api.risk_category.medium_needs_monitoring',
        'Tinggi - Perlu tindakan' => 'api.risk_category.high_needs_action',
        'Sangat Tinggi - Darurat' => 'api.risk_category.very_high_emergency',
    ],
    
    // Status Keandalan Sistem (dengan deskripsi lengkap)
    'status_keandalan' => [
        'Sangat Baik' => 'api.reliability_status.excellent',
        'Baik' => 'api.reliability_status.good',
        'Cukup' => 'api.reliability_status.fair',
        'Kurang' => 'api.reliability_status.poor',
        'Buruk' => 'api.reliability_status.bad',
        // Status dengan deskripsi lengkap
        'Kurang - Perlu intervensi segera' => 'api.reliability_status.poor_needs_intervention',
        'Cukup - Perlu monitoring' => 'api.reliability_status.fair_needs_monitoring',
        'Baik - Sistem stabil' => 'api.reliability_status.good_stable',
        'Sangat Baik - Optimal' => 'api.reliability_status.excellent_optimal',
    ],
    
    // Status Water Quality Index (WQI)
    'status_wqi' => [
        'Sangat Baik' => 'api.wqi_status.excellent',
        'Baik' => 'api.wqi_status.good',
        'Sedang' => 'api.wqi_status.moderate',
        'Buruk' => 'api.wqi_status.poor',
        'Sangat Buruk' => 'api.wqi_status.very_poor',
    ],
    
    // Status Ekosistem
    'status_ekosistem' => [
        'Sehat' => 'api.ecosystem_status.healthy',
        'Baik' => 'api.ecosystem_status.good',
        'Tertekan' => 'api.ecosystem_status.stressed',
        'Kritis' => 'api.ecosystem_status.critical',
    ],
    
    // Status Water Balance
    'status_balance' => [
        'Seimbang dengan baik' => 'api.balance_status.well_balanced',
        'Seimbang' => 'api.balance_status.balanced',
        'Perlu perhatian' => 'api.balance_status.needs_attention',
        'Tidak seimbang' => 'api.balance_status.unbalanced',
    ],
    
    // Kondisi Hari (Success/Overflow)
    'kondisi_hari' => [
        'Berhasil' => 'api.day_condition.success',
        'Overflow' => 'api.day_condition.overflow',
        'Gagal' => 'api.day_condition.failed',
    ],
    
    // Status Umum
    'status_umum' => [
        'Aktif' => 'api.general_status.active',
        'Nonaktif' => 'api.general_status.inactive',
        'Normal' => 'api.general_status.normal',
        'Peringatan' => 'api.general_status.warning',
        'Bahaya' => 'api.general_status.danger',
        'Aman' => 'api.general_status.safe',
    ],
    
    // Prioritas Sektor
    'prioritas_sektor' => [
        'Sangat Tinggi' => 'api.priority.very_high',
        'Tinggi' => 'api.priority.high',
        'Sedang' => 'api.priority.medium',
        'Rendah' => 'api.priority.low',
        'Sangat Rendah' => 'api.priority.very_low',
    ],
    
    // Tipe Risiko
    'tipe_risiko' => [
        'Banjir' => 'api.risk_type.flood',
        'Kekeringan' => 'api.risk_type.drought',
        'Erosi' => 'api.risk_type.erosion',
        'Sedimentasi' => 'api.risk_type.sedimentation',
        'Pencemaran' => 'api.risk_type.pollution',
    ],
    
    // Kondisi Tanah
    'kondisi_tanah' => [
        'Kering' => 'api.soil_condition.dry',
        'Lembab' => 'api.soil_condition.moist',
        'Basah' => 'api.soil_condition.wet',
        'Jenuh' => 'api.soil_condition.saturated',
    ],
    
    // Kualitas Air Parameter
    'kualitas_parameter' => [
        'Baik Sekali' => 'api.quality_parameter.excellent',
        'Baik' => 'api.quality_parameter.good',
        'Cukup' => 'api.quality_parameter.fair',
        'Kurang' => 'api.quality_parameter.poor',
        'Buruk' => 'api.quality_parameter.bad',
    ],
    
    // Sektor Pengguna Air
    'sector' => [
        'Domestik' => 'domestic',
        'Pertanian' => 'agriculture',
        'Industri' => 'industry',
        'Komersial' => 'commercial',
        'Publik' => 'public',
    ],
    
    // Sumber Air
    'source' => [
        'Sungai' => 'river',
        'Diversi' => 'diversion',
        'Air Tanah' => 'groundwater',
        'Danau' => 'lake',
        'Embung' => 'retention_pond',
        'Bendungan' => 'dam',
    ],
    
    // Kategori Saran Perbaikan
    'category' => [
        'Kapasitas Kolam Retensi' => 'retention_pond_capacity',
        'Keandalan Infrastruktur' => 'infrastructure_reliability',
        'Kualitas Air' => 'water_quality',
        'Efisiensi Distribusi' => 'distribution_efficiency',
    ],
    
    // Prioritas
    'priority' => [
        'TINGGI' => 'high_priority',
        'Tinggi' => 'high_priority',
        'SEDANG' => 'medium_priority',
        'Sedang' => 'medium_priority',
        'RENDAH' => 'low_priority',
        'Rendah' => 'low_priority',
        'NORMAL' => 'normal_priority',
        'Normal' => 'normal_priority',
    ],
    
    // Rekomendasi text patterns (for API recommendation texts)
    'recommendation_texts' => [
        'Kolam Retensi kritis' => 'critical_retention_pond',
        'Terapkan rationing air segera' => 'implement_water_rationing_immediately',
        'Keandalan sistem' => 'system_reliability',
        'Audit infrastruktur' => 'audit_infrastructure',
        'kurangi kebocoran' => 'reduce_leakage',
    ],
    
];
