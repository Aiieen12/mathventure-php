<?php
// auth/soalan_tahun4.php

// Setiap level ada 3 soalan:
// 1 = MCQ
// 2 = isian/esei
// 3 = visual (gambar + jawapan ditaip)

return [

    // ===== Level 1 Tahun 4 =====
    1 => [
        // Soalan 1: MCQ
        [
            'type'    => 'mcq',
            'text'    => '8 + 5 = ?',
            'options' => ['11', '12', '13'],
            'correct' => 2 // index dalam array options (0,1,2)
        ],
        // Soalan 2: isian tempat kosong / esei pendek
        [
            'type'   => 'text',
            'text'   => 'Tuliskan nombor genap yang datang selepas 14.',
            'answer' => '16'
        ],
        // Soalan 3: visual (contoh: gambar rajah pecahan)
        [
            'type'   => 'visual',
            'text'   => 'Berdasarkan gambar rajah, tuliskan pecahan yang diwarnakan.',
            // tukar laluan gambar ikut folder awak
            'image'  => 'images/t4-l1-q3.png',
            // boleh letak lebih dari satu jawapan yang diterima
            'answer' => ['1/4', '1 / 4']
        ],
    ],

    // ===== Level 2 Tahun 4 (contoh ringkas) =====
    2 => [
        [
            'type'    => 'mcq',
            'text'    => '15 − 7 = ?',
            'options' => ['6', '7', '8'],
            'correct' => 2
        ],
        [
            'type'   => 'text',
            'text'   => 'Tuliskan nombor perdana yang paling kecil.',
            'answer' => ['2', 'dua']
        ],
        [
            'type'   => 'visual',
            'text'   => 'Berdasarkan garis nombor dalam gambar, apakah nilai yang ditunjukkan oleh anak panah?',
            'image'  => 'images/t4-l2-q3.png',
            'answer' => '20'
        ],
    ],

    // ===== Level 3 Tahun 4 (boleh ubah kemudian) =====
    3 => [
        [
            'type'    => 'mcq',
            'text'    => '3 × 6 = ?',
            'options' => ['12', '18', '24'],
            'correct' => 1
        ],
        [
            'type'   => 'text',
            'text'   => 'Isi tempat kosong: 100 cm = __ m',
            'answer' => ['1', '1 m']
        ],
        [
            'type'   => 'visual',
            'text'   => 'Lihat rajah waktu pada jam. Tulis masa yang ditunjukkan.',
            'image'  => 'images/t4-l3-q3.png',
            'answer' => ['3:00', '3.00', '3']
        ],
    ],
];
