<?php

use function PHPSTORM_META\map;

return [
    'patientid_max_length' => 10,

    'genders' => [
        'm' => 'male',
        'f' => 'female',
    ],

    'blood_types' => [
        1 => 'A+',
        2 => 'A-',
        3 => 'B+',
        4 => 'B-',
        5 => 'AB+',
        6 => 'AB-',
        7 => 'O+',
        8 => 'O-',
    ],

    'religions' => [
        1 => 'Islam',
        2 => 'Kristen',
        3 => 'Protestan',
        4 => 'Hinduism',
        5 => 'Buddhism',
        6 => 'Konghucu',
        7 => 'Other',
    ],

    'marital_status' => [
        1 => 'Single',
        2 => 'Married',
        3 => 'Widowed',
        4 => 'Divorced',
    ],

    'identity_types' => [
        'ktp' => 'KTP',
        'sim' => 'SIM',
        'passport' => 'Passport',
        'lainnya' => 'Other',
    ],

    'address_types' => [
        'domisili' => 'Domisili',
        'ktp' => 'Sesuai KTP',
        'kantor' => 'Kantor',
        'lainnya' => 'Lainnya',
    ],

    'relation_types' => [
        'self' => 'Diri sendiri',
        'parent' => 'Orang Tua',
        'guardian' => 'Wali',
        'relatives' => 'Saudara',
        'friend' => 'Teman',
        'other' => 'Lainnya',
    ],

    'guarantor_types' => [
        'bpjs' => 'BPJS',
        'insurance' => 'Asuransi',
        'corporate' => 'Perusahaan',
        'others' => 'Pribadi',
    ]
];
