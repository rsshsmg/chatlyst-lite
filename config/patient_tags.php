<?php

use Carbon\Carbon;

return [

    /*
    |--------------------------------------------------------------------------
    | Tag Demografi
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'usia_0_12',
        'description' => 'Anak-anak (0 - 12 tahun)',
        'rule' => function ($person) {
            $age = Carbon::parse($person->date_of_birth)->age;
            return $age >= 0 && $age <= 12;
        },
    ],
    [
        'name' => 'usia_13_19',
        'description' => 'Remaja (13 - 19 tahun)',
        'rule' => function ($person) {
            $age = Carbon::parse($person->date_of_birth)->age;
            return $age >= 13 && $age <= 19;
        },
    ],
    [
        'name' => 'usia_20_30',
        'description' => 'Dewasa muda (20 - 30 tahun)',
        'rule' => function ($person) {
            $age = Carbon::parse($person->date_of_birth)->age;
            return $age >= 20 && $age <= 30;
        },
    ],
    [
        'name' => 'usia_31_40',
        'description' => 'Dewasa produktif (31 - 40 tahun)',
        'rule' => function ($person) {
            $age = Carbon::parse($person->date_of_birth)->age;
            return $age >= 31 && $age <= 40;
        },
    ],
    [
        'name' => 'usia_41_50',
        'description' => 'Menjelang lansia (41 - 50 tahun)',
        'rule' => function ($person) {
            $age = Carbon::parse($person->date_of_birth)->age;
            return $age >= 41 && $age <= 50;
        },
    ],
    [
        'name' => 'usia_50_plus',
        'description' => 'Lansia (51+ tahun)',
        'rule' => function ($person) {
            $age = Carbon::parse($person->date_of_birth)->age;
            return $age >= 51;
        },
    ],
    [
        'name' => 'pria',
        'description' => 'Pasien pria',
        'rule' => function ($person) {
            return strtolower($person->gender) === 'male';
        },
    ],
    [
        'name' => 'wanita',
        'description' => 'Pasien wanita',
        'rule' => function ($person) {
            return strtolower($person->gender) === 'female';
        },
    ],

    /*
    |--------------------------------------------------------------------------
    | Tag Perilaku
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'pasien_baru',
        'description' => 'Pasien baru (< 1 bulan sejak kunjungan pertama)',
        'rule' => function ($person) {
            return $person->created_at >= now()->subMonth();
        },
    ],
    [
        'name' => 'tidak_aktif_3_bulan',
        'description' => 'Tidak berkunjung > 3 bulan',
        'rule' => function ($person) {
            return !$person->last_visit_date || Carbon::parse($person->last_visit_date)->lt(now()->subMonths(3));
        },
    ],
    [
        'name' => 'tidak_aktif_6_bulan',
        'description' => 'Tidak berkunjung > 6 bulan',
        'rule' => function ($person) {
            return !$person->last_visit_date || Carbon::parse($person->last_visit_date)->lt(now()->subMonths(6));
        },
    ],

    /*
    |--------------------------------------------------------------------------
    | Tag Kesehatan (Contoh awal)
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'hipertensi',
        'description' => 'Riwayat tekanan darah tinggi',
        'rule' => function ($person) {
            return $person->diagnoses && in_array('hipertensi', $person->patient->diagnoses);
        },
    ],
    [
        'name' => 'diabetes',
        'description' => 'Riwayat diabetes',
        'rule' => function ($person) {
            return $person->diagnoses && in_array('diabetes', $person->patient->diagnoses);
        },
    ],
    [
        'name' => 'kolesterol',
        'description' => 'Riwayat kolesterol tinggi',
        'rule' => function ($person) {
            return $person->diagnoses && in_array('kolesterol', $person->patient->diagnoses);
        },
    ],

    /*
    |--------------------------------------------------------------------------
    | Tag Layanan / Minat
    |--------------------------------------------------------------------------
    */
    [
        'name' => 'medical_checkup',
        'description' => 'Pernah menggunakan layanan medical check-up',
        'rule' => function ($person) {
            return $person->services && in_array('medical_checkup', $person->patient->services);
        },
    ],
    [
        'name' => 'gigi',
        'description' => 'Pernah perawatan gigi',
        'rule' => function ($person) {
            return $person->services && in_array('gigi', $person->patient->services);
        },
    ],
    [
        'name' => 'skincare',
        'description' => 'Pernah perawatan kulit',
        'rule' => function ($person) {
            return $person->services && in_array('skincare', $person->patient->services);
        },
    ],
    [
        'name' => 'kesehatan_mental',
        'description' => 'Pernah menggunakan layanan kesehatan mental',
        'rule' => function ($person) {
            return $person->services && in_array('kesehatan_mental', $person->patient->services);
        },
    ],
    [
        'name' => 'kesehatan_reproduksi',
        'description' => 'Pernah menggunakan layanan kesehatan reproduksi',
        'rule' => function ($person) {
            return $person->services && in_array('kesehatan_reproduksi', $person->patient->services);
        },
    ],
];
