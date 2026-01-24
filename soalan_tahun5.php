<?php
// auth/soalan_tahun6.php
//
// Set soalan Matematik Tahun 6 untuk permainan Mathventure.
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

    // ===== LEVEL 1 TAHUN 6 =====
    1 => [
        // Soalan 1 (MCQ)
        [
            'type'    => 'mcq',
            'text'    => 'Hitung: 40 x (2,500 + 750) ÷ 100 = ?',
            'options' => [
                'A. 1,300',
                'B. 1,030',
                'C. 3,250',
                'D. 1,003',
            ],
            'correct' => 0, // A
        ],

        // Soalan 2 — 2.08 juta dalam perkataan
        [
            'type'   => 'text',
            'text'   => "Tulis '2.08 juta' dalam perkataan penuh.",
            'answer' => [
                'Dua perpuluhan sifar lapan juta',
                'dua perpuluhan sifar lapan juta',
            ],
        ],

        // Soalan 3 — nombor perdana (visual)
        [
            'type'   => 'visual',
            'text'   => 'Rajah di bawah menunjukkan senarai beberapa nombor. Senaraikan semua nombor perdana yang terdapat dalam rajah di atas.',
            'image'  => 'qimages/T6/T6L1.png', // tukar jika nama fail lain
            'answer' => [
                '53, 59',
                '53 , 59',
                '53 dan 59',
                '53 dan59',
            ],
        ],
    ],

    // ===== LEVEL 2 TAHUN 6 =====
    2 => [
        // Soalan 1 (MCQ) — 4/5 kepada peratus
        [
            'type'    => 'mcq',
            'text'    => 'Tukarkan 4/5 kepada peratus.',
            'options' => [
                'A. 20%',
                'B. 45%',
                'C. 80%',
                'D. 40%',
            ],
            'correct' => 2, // C
        ],

        // Soalan 2 — 3.2 kepada peratus (perkataan)
        [
            'type'   => 'text',
            'text'   => 'Tukarkan 3.2 kepada peratus. Tulis jawapan anda dalam perkataan penuh.',
            'answer' => [
                'Tiga ratus dua puluh peratus',
                'tiga ratus dua puluh peratus',
            ],
        ],

        // Soalan 3 — harga kasut selepas diskaun (visual)
        [
            'type'   => 'visual',
            'text'   => 'Rajah di bawah menunjukkan harga asal dan peratus diskaun bagi sepasang kasut. Berapakah harga kasut itu selepas diskaun?',
            'image'  => 'qimages/T6/T6L2.png', // tukar jika nama fail lain
            'answer' => [
                'RM120',
                'RM 120',
                '120',
                '120.00',
                'rm120',
            ],
        ],
    ],

    // ===== LEVEL 3 TAHUN 6 =====
    3 => [
        // Soalan 1 (MCQ) — untung telefon
        [
            'type'    => 'mcq',
            'text'    => 'Harga kos sebuah telefon ialah RM800. Peniaga itu menjualnya dengan harga RM920. Berapakah keuntungan yang diperoleh?',
            'options' => [
                'A. RM1,720',
                'B. RM120',
                'C. RM1,020',
                'D. RM80',
            ],
            'correct' => 1, // B
        ],

        // Soalan 2 — istilah "Aset"
        [
            'type'   => 'text',
            'text'   => 'Sesuatu yang dibeli dan mempunyai nilai (seperti rumah atau simpanan bank) yang boleh digunakan untuk membayar hutang dikenali sebagai _______.',
            'answer' => [
                'Aset',
                'aset',
            ],
        ],

        // Soalan 3 — bil utiliti (visual)
        [
            'type'   => 'visual',
            'text'   => 'Rajah di bawah menunjukkan sebahagian daripada bil utiliti yang perlu dibayar oleh Encik Kamal. Berapakah jumlah liabiliti (hutang) semasa yang perlu dibayar berdasarkan bil di atas?',
            'image'  => 'qimages/T6/T6L3.png', // tukar jika nama fail lain
            'answer' => [
                'RM235.95',
                'RM 235.95',
                '235.95',
            ],
        ],
    ],

    // ===== LEVEL 4 TAHUN 6 =====
    4 => [
        // Soalan 1 (MCQ) — poligon sekata 6 sisi
        [
            'type'    => 'mcq',
            'text'    => 'Sebuah poligon sekata mempunyai 6 sisi yang sama panjang dan 6 sudut pedalaman yang sama saiz. Apakah nama poligon ini?',
            'options' => [
                'A. Pentagon',
                'B. Heksagon',
                'C. Heptagon',
                'D. Oktagon',
            ],
            'correct' => 1, // B
        ],

        // Soalan 2 — bentuk 5 sisi, 5 bucu
        [
            'type'   => 'text',
            'text'   => 'Apakah nama bagi bentuk dua dimensi yang mempunyai 5 sisi lurus dan 5 bucu? Tulis jawapan dengan ejaan penuh.',
            'answer' => [
                'Pentagon',
                'pentagon',
            ],
        ],

        // Soalan 3 — diameter bulatan (visual)
        [
            'type'   => 'visual',
            'text'   => 'Rajah di bawah menunjukkan sebuah bulatan dengan pusat O. Garis manakah (P atau Q) yang mewakili diameter?',
            'image'  => 'qimages/T6/T6L4.png', // tukar jika nama fail lain
            'answer' => [
                'Q',
                'q',
                'Garis Q',
                'garis Q',
            ],
        ],
    ],

    // ===== LEVEL 5 TAHUN 6 =====
    5 => [
        // Soalan 1 (MCQ) — median
        [
            'type'    => 'mcq',
            'text'    => 'Satu set data menunjukkan: 10, 12, 8, 10, 15. Apakah median bagi set data tersebut?',
            'options' => [
                'A. 10',
                'B. 11',
                'C. 12',
                'D. 8',
            ],
            'correct' => 0, // A
        ],

        // Soalan 2 — peristiwa mustahil
        [
            'type'   => 'text',
            'text'   => "'Seekor ayam melahirkan anak.' Nyatakan sama ada peristiwa ini mustahil atau pasti berlaku. Tulis jawapan anda dengan ejaan penuh.",
            'answer' => [
                'Mustahil',
                'mustahil',
            ],
        ],

        // Soalan 3 — min bilangan buku (visual)
        [
            'type'   => 'visual',
            'text'   => 'Carta palang di bawah menunjukkan bilangan buku yang dibaca oleh 4 orang murid dalam sebulan. Hitung min (purata) bilangan buku yang dibaca oleh seorang murid.',
            // Dalam soalan asal tertulis T6L6; di sini guna T6L5.png.
            // Tukar ikut nama sebenar fail carta palang.
            'image'  => 'qimages/T6/T6L5.png',
            'answer' => [
                '7.5',
                '7.50',
                '7.5 buku',
                '7.5 buah buku',
            ],
        ],
    ],
];
