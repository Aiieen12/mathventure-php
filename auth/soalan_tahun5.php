<?php
// auth/soalan_tahun5.php
//
// Set soalan Matematik Tahun 5 untuk permainan Mathventure.
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

    // ===== LEVEL 1 TAHUN 5 =====
    1 => [
        // Soalan 1 (MCQ) — nombor perdana
        [
            'type'    => 'mcq',
            'text'    => 'Antara nombor berikut, yang manakah merupakan nombor perdana?',
            'options' => [
                'A. 9',
                'B. 15',
                'C. 21',
                'D. 23',
            ],
            'correct' => 3, // D
        ],

        // Soalan 2 — bundar 318509 kepada ribu terdekat, jawapan dalam perkataan
        [
            'type'   => 'text',
            'text'   => 'Bundarkan 318509 kepada ribu terdekat. Tulis jawapan anda dalam perkataan.',
            'answer' => [
                'Tiga ratus sembilan belas ribu',
                'tiga ratus sembilan belas ribu',
            ],
        ],

        // Soalan 3 — graf pelitup muka, kilang paling sedikit
        [
            'type'   => 'visual',
            'text'   => 'Rajah di bawah menunjukkan bilangan pelitup muka yang dihasilkan oleh empat buah kilang. Kilang manakah yang menghasilkan bilangan pelitup muka yang paling sedikit?',
            'image'  => 'qimages/T5/T5L1.png',
            'answer' => [
                'Kilang D',
                'kilang d',
                'D',
            ],
        ],
    ],

    // ===== LEVEL 2 TAHUN 5 =====
    2 => [
        // Soalan 1 (MCQ) — 3/8 daripada 40
        [
            'type'    => 'mcq',
            'text'    => 'Terdapat 40 orang murid di dalam Kelas 5 Cemerlang. 3/8 daripada mereka ialah murid lelaki. Berapakah bilangan murid lelaki di dalam kelas itu?',
            'options' => [
                'A. 12',
                'B. 15',
                'C. 20',
                'D. 25',
            ],
            'correct' => 1, // B
        ],

        // Soalan 2 — 1.45 kepada peratus, dalam perkataan
        [
            'type'   => 'text',
            'text'   => 'Tukarkan 1.45 kepada peratus. Tulis jawapan anda dalam perkataan penuh.',
            'answer' => [
                'Seratus empat puluh lima peratus',
                'seratus empat puluh lima peratus',
            ],
        ],

        // Soalan 3 — jisim gula-gula, 3 bekas
        [
            'type'   => 'visual',
            'text'   => 'Rajah di bawah menunjukkan jisim sebungkus gula-gula. Berapakah jumlah jisim, dalam kg, bagi 3 bekas gula-gula yang sama?',
            'image'  => 'qimages/T5/T5L2.png',
            'answer' => [
                '1.05 kg',
                '1.05kg',
                '1.05',
            ],
        ],
    ],

    // ===== LEVEL 3 TAHUN 5 =====
    3 => [
        // Soalan 1 (MCQ) — baki simpanan ayah Ali
        [
            'type'    => 'mcq',
            'text'    => 'Ayah Ali mempunyai simpanan sebanyak RM12,500. Dia menggunakan RM1,899 untuk membeli komputer riba dan RM750 untuk membeli telefon bimbit. Berapakah baki simpanannya?',
            'options' => [
                'A. RM9,851',
                'B. RM10,601',
                'C. RM10,750',
                'D. RM11,750',
            ],
            'correct' => 0, // A
        ],

        // Soalan 2 — istilah "Faedah"
        [
            'type'   => 'text',
            'text'   => 'Ganjaran wang yang dibayar oleh pihak bank kepada penyimpan wang dikenali sebagai __________. (Isi tempat kosong dengan satu perkataan yang betul ejaannya).',
            'answer' => [
                'faedah',
                'Faedah',
            ],
        ],

        // Soalan 3 — beza harga tunai & ansuran basikal
        [
            'type'   => 'visual',
            'text'   => 'Rajah di bawah menunjukkan iklan jualan sebuah basikal. Hitung beza harga antara bayaran tunai dan bayaran ansuran.',
            'image'  => 'qimages/T5/T5L3.png',
            'answer' => [
                'RM120',
                'RM 120',
                '120',
            ],
        ],
    ],

    // ===== LEVEL 4 TAHUN 5 =====
    4 => [
        // Soalan 1 (MCQ) — 2 abad 7 dekad kepada tahun
        [
            'type'    => 'mcq',
            'text'    => "Tukarkan '2 abad 7 dekad' kepada tahun.",
            'options' => [
                'A. 27 tahun',
                'B. 90 tahun',
                'C. 207 tahun',
                'D. 270 tahun',
            ],
            'correct' => 3, // D
        ],

        // Soalan 2 — 5 hari 10 jam kepada jam, dalam perkataan
        [
            'type'   => 'text',
            'text'   => "Tukarkan '5 hari 10 jam' kepada jam sahaja. Tulis jawapan anda dalam perkataan penuh.",
            'answer' => [
                'Seratus tiga puluh jam',
                'seratus tiga puluh jam',
            ],
        ],

        // Soalan 3 — sela masa antara Filem A & B
        [
            'type'   => 'visual',
            'text'   => 'Jadual di bawah menunjukkan waktu tayangan tiga filem di sebuah pawagam. Berapakah tempoh sela masa, dalam jam dan minit, di antara waktu tayangan Filem A dan Filem B?',
            'image'  => 'qimages/T5/T5L4.png',
            'answer' => [
                '2 jam 45 minit',
                '2j 45 minit',
                '2 jam 45',
                '2:45',
            ],
        ],
    ],

    // ===== LEVEL 5 TAHUN 5 =====
    5 => [
        // Soalan 1 (MCQ) — 1.08 tan metrik kepada kg
        [
            'type'    => 'mcq',
            'text'    => 'Sebuah lori membawa muatan 1.08 tan metrik beras. Berapakah jisim beras itu dalam kilogram (kg)?',
            'options' => [
                'A. 108 kg',
                'B. 1,008 kg',
                'C. 1,080 kg',
                'D. 10,800 kg',
            ],
            'correct' => 2, // C
        ],

        // Soalan 2 — 4,025 ml kepada liter & mililiter (perkataan)
        [
            'type'   => 'text',
            'text'   => 'Tukarkan 4,025 ml kepada unit liter dan mililiter. Tulis jawapan anda dalam perkataan penuh.',
            'answer' => [
                'Empat liter dua puluh lima mililiter',
                'empat liter dua puluh lima mililiter',
            ],
        ],

        // Soalan 3 — isi padu air dibahagi 5 cawan
        [
            'type'   => 'visual',
            'text'   => 'Rajah di bawah menunjukkan isi padu air di dalam sebuah bikar. Aina menuang kesemua air itu secara sama rata ke dalam 5 biji cawan. Berapakah isi padu air, dalam ml, bagi setiap cawan?',
            'image'  => 'qimages/T5/T5L5.png',
            'answer' => [
                '160 ml',
                '160ml',
                '160',
            ],
        ],
    ],
];
