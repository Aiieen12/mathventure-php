<?php
// auth/soalan_tahun4.php
//
// Set soalan Matematik Tahun 4 untuk permainan Mathventure.
//
// Setiap level ada 3 soalan:
//   1 = MCQ (aneka pilihan)
//   2 = isian / esei pendek
//   3 = visual (gambar + jawapan ditaip)
//
// Struktur setiap soalan:
//   type    : 'mcq' | 'text' | 'visual'
//   text    : teks soalan
//   options : (MCQ sahaja) senarai pilihan jawapan
//   correct : (MCQ sahaja) index jawapan betul dalam 'options' (0-bermula)
//   image   : (visual sahaja) laluan gambar relatif kepada folder auth/
//   answer  : (text/visual) string atau array string jawapan yang diterima
//
return [

    // ===== LEVEL 1 TAHUN 4 =====
    1 => [
        // Soalan 1 (MCQ)
        // Di sebuah stadium, terdapat 23,489 tempat duduk penyokong pasukan merah
        // dan 23,849 tempat duduk penyokong pasukan biru. Penyataan manakah yang betul?
        // Jawapan: B (Bilangan tempat duduk pasukan biru lebih besar)
        [
            'type'    => 'mcq',
            'text'    => 'Di sebuah stadium, terdapat 23,489 tempat duduk penyokong pasukan merah dan 23,849 tempat duduk penyokong pasukan biru. Penyataan manakah yang betul?',
            'options' => [
                'A. Bilangan tempat duduk pasukan merah lebih besar.',
                'B. Bilangan tempat duduk pasukan biru lebih besar.',
                'C. Bilangan tempat duduk kedua-dua pasukan adalah sama.',
                'D. Bilangan tempat duduk pasukan biru lebih kecil.',
            ],
            'correct' => 1, // index 0=A, 1=B, 2=C, 3=D
        ],

        // Soalan 2 (Isi tempat kosong)
        // Tuliskan '50,107' dalam perkataan.
        // Jawapan utama: "Lima puluh ribu satu ratus tujuh"
        [
            'type'   => 'text',
            'text'   => "Tuliskan '50,107' dalam perkataan.",
            'answer' => [
                'Lima puluh ribu satu ratus tujuh',
                'Lima puluh ribu seratus tujuh', // variasi yang juga munasabah
            ],
        ],

        // Soalan 3 (Berasaskan visual)
        // Rajah memaparkan empat nombor. Susun dalam tertib menurun.
        // Jawapan: 19008, 18900, 18090, 18009
        [
            'type'   => 'visual',
            'text'   => 'Rajah di atas memaparkan empat buah nombor. Susun keempat-empat nombor itu dalam tertib menurun.',
            // Gambar disimpan dalam auth/qimages/T4/T4L1.png
            'image'  => 'qimages/T4/T4L1.png',
            'answer' => [
                '19008, 18900, 18090, 18009',
                '19008 18900 18090 18009',
                '19008,18900,18090,18009',
            ],
        ],
    ],

    // ===== LEVEL 2 TAHUN 4 =====
    2 => [
        // Soalan 1 (MCQ)
        // Aiman ada 7/9 biji kek. Dia memberikan 2/9 biji kek itu kepada adiknya.
        // Berapakah baki kek Aiman?
        // Jawapan: C (5/9)
        [
            'type'    => 'mcq',
            'text'    => 'Aiman ada 7/9 biji kek. Dia memberikan 2/9 biji kek itu kepada adiknya. Berapakah baki kek Aiman?',
            'options' => [
                'A. 5/18',
                'B. 9/9',
                'C. 5/9',
                'D. 5/0',
            ],
            'correct' => 2, // C
        ],

        // Soalan 2 (Tukaran perpuluhan kepada peratus)
        // Tukarkan 0.75 kepada bentuk peratus. Tulis jawapan dalam perkataan.
        // Jawapan: Tujuh puluh lima peratus
        [
            'type'   => 'text',
            'text'   => 'Tukarkan 0.75 kepada bentuk peratus. Tulis jawapan anda dalam perkataan.',
            'answer' => [
                'Tujuh puluh lima peratus',
            ],
        ],

        // Soalan 3 (Visual peratusan berlorek)
        // Rajah segi empat dibahagi kepada bahagian sama besar, 6/10 berlorek.
        // Jawapan: 60%
        [
            'type'   => 'visual',
            'text'   => 'Rajah di bawah menunjukkan sebuah segi empat yang dibahagikan kepada beberapa bahagian yang sama besar. Nyatakan nilai peratusan kawasan yang berlorek.',
            // Gambar disimpan dalam auth/qimages/T4/T4L2.png
            'image'  => 'qimages/T4/T4L2.png',
            'answer' => [
                '60%',
                '60 peratus',
                '60',
            ],
        ],
    ],

    // ===== LEVEL 3 TAHUN 4 =====
    3 => [
        // Soalan 1 (MCQ)
        // Harga sebuah basikal ialah RM450. Harga sebuah topi keledar ialah RM89.90.
        // Berapakah jumlah harga untuk 2 buah basikal dan sebuah topi keledar?
        // Jawapan: B (RM989.90)
        [
            'type'    => 'mcq',
            'text'    => 'Harga sebuah basikal ialah RM450. Harga sebuah topi keledar ialah RM89.90. Berapakah jumlah harga untuk 2 buah basikal dan sebuah topi keledar?',
            'options' => [
                'A. RM539.90',
                'B. RM989.90',
                'C. RM900.00',
                'D. RM1,089.80',
            ],
            'correct' => 1, // B
        ],

        // Soalan 2 (Mata wang)
        // Apakah mata wang rasmi bagi negara India?
        // Jawapan: Rupee
        [
            'type'   => 'text',
            'text'   => 'Apakah mata wang rasmi bagi negara India? Tulis jawapan anda dengan ejaan penuh.',
            'answer' => [
                'rupee',
                'rupee india',
                'indian rupee',
            ],
        ],

        // Soalan 3 (Visual resit pembelian)
        // Resit pembelian Puan Salmah di SmartKid Station.
        // Berapakah harga bagi kasut sekolah?
        // Jawapan: RM42.50
        [
            'type'   => 'visual',
            'text'   => 'Resit ini merupakan resit pembelian Puan Salmah di SmartKid Station. Berapakah harga bagi kasut sekolah yang dibeli oleh Puan Salmah?',
            // Gambar disimpan dalam auth/qimages/T4/T4L3.png
            'image'  => 'qimages/T4/T4L3.png',
            'answer' => [
                'RM42.50',
                'RM 42.50',
                'rm42.50',
                'rm 42.50',
                '42.50',
                '42.5',
            ],
        ],
    ],

    // ===== LEVEL 4 TAHUN 4 =====
    4 => [
        // Soalan 1 (MCQ)
        // Tukarkan 5 abad dan 4 dekad kepada tahun.
        // Jawapan: D (540 tahun)
        [
            'type'    => 'mcq',
            'text'    => 'Tukarkan 5 abad dan 4 dekad kepada tahun.',
            'options' => [
                'A. 54 tahun',
                'B. 90 tahun',
                'C. 504 tahun',
                'D. 540 tahun',
            ],
            'correct' => 3, // D
        ],

        // Soalan 2 (Tukaran masa 24 jam kepada 12 jam)
        // Tuliskan 'Jam 2015' dalam sistem 12 jam.
        // Jawapan utama: 8:15 malam (petang juga boleh diterima)
        [
            'type'   => 'text',
            'text'   => "Tuliskan 'Jam 2015' dalam sistem 12 jam. Pastikan anda menulis 'pagi', 'petang' atau 'malam' dengan ejaan penuh.",
            'answer' => [
                '8:15 malam',
                '8.15 malam',
                '8:15 petang',
                '8.15 petang',
                '8:15 pm',
                '8.15 pm',
                '8:15 p.m.',
                '8.15 p.m.',
            ],
        ],

        // Soalan 3 (Visual muka jam)
        // Muka jam menunjukkan waktu tamat rancangan pada 10:00 malam.
        // Tempoh rancangan 1 jam 30 minit.
        // Pukul berapakah rancangan bermula? Jawapan: 8:30 malam
        [
            'type'   => 'visual',
            'text'   => 'Muka jam di bawah menunjukkan waktu tamat sebuah rancangan televisyen pada waktu malam. Rancangan itu berdurasi 1 jam 30 minit. Pukul berapakah rancangan itu bermula?',
            // Gambar disimpan dalam auth/qimages/T4/T4L4.png
            'image'  => 'qimages/T4/T4L4.png',
            'answer' => [
                '8:30 malam',
                '8.30 malam',
                '8:30 pm',
                '8.30 pm',
                '8:30 p.m.',
                '8.30 p.m.',
            ],
        ],
    ],

    // ===== LEVEL 5 TAHUN 4 =====
    5 => [
        // Soalan 1 (MCQ)
        // Jarak rumah Azri ke sekolah ialah 2 km 50 m. Berapakah jarak dalam meter?
        // Jawapan: C (2,050 m)
        [
            'type'    => 'mcq',
            'text'    => 'Jarak dari rumah Azri ke sekolah ialah 2 km 50 m. Berapakah jarak itu dalam meter (m) sahaja?',
            'options' => [
                'A. 250 m',
                'B. 2,005 m',
                'C. 2,050 m',
                'D. 2,500 m',
            ],
            'correct' => 2, // C
        ],

        // Soalan 2 (Unit ukuran isipadu cecair)
        // Simbol: ml. Nama unit: mililiter.
        [
            'type'   => 'text',
            'text'   => "Tuliskan nama unit ukuran bagi simbol 'ml'. Jawapan mesti dalam perkataan penuh.",
            'answer' => [
                'mililiter',
            ],
        ],

        // Soalan 3 (Visual pembaris dan pensel)
        // Pensel bermula pada 7 cm dan berakhir pada 20 cm.
        // Panjang sebenar: 13 cm.
        [
            'type'   => 'visual',
            'text'   => 'Rajah di bawah menunjukkan panjang sebatang pensel yang diukur menggunakan pembaris. Berapakah panjang sebenar pensel itu dalam cm?',
            // Gambar disimpan dalam auth/qimages/T4/T4L5.png
            'image'  => 'qimages/T4/T4L5.png',
            'answer' => [
                '13 cm',
                '13',
            ],
        ],
    ],
];
