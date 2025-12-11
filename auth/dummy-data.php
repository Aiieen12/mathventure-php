<?php
// auth/dummy-data.php
// Data dummy untuk projek inovasi Mathventure (boleh diganti DB sebenar kemudian).

$kelasUtama = '4 Dinamik';

// Senarai pelajar contoh
$students = [
    'anis21' => [
        'id'             => 'anis21',
        'nama'           => 'Anis',
        'kelas'          => '4 Dinamik',
        'hadir_hari_ini' => true,
        'jumlah_hadir'   => 18,
        'jumlah_hari'    => 20,
        'markah_ujian1'  => 95,
        'markah_ujian2'  => 88,
        'purata_level'   => 4.5,
        'jumlah_badge'   => 3,
        'badges_minggu'  => 2,
    ],
    'amir14' => [
        'id'             => 'amir14',
        'nama'           => 'Amirul',
        'kelas'          => '4 Dinamik',
        'hadir_hari_ini' => true,
        'jumlah_hadir'   => 17,
        'jumlah_hari'    => 20,
        'markah_ujian1'  => 82,
        'markah_ujian2'  => 79,
        'purata_level'   => 3.2,
        'jumlah_badge'   => 2,
        'badges_minggu'  => 1,
    ],
    'balqis10' => [
        'id'             => 'balqis10',
        'nama'           => 'Balqis',
        'kelas'          => '4 Dinamik',
        'hadir_hari_ini' => false,
        'jumlah_hadir'   => 15,
        'jumlah_hari'    => 20,
        'markah_ujian1'  => 76,
        'markah_ujian2'  => 70,
        'purata_level'   => 2.4,
        'jumlah_badge'   => 1,
        'badges_minggu'  => 0,
    ],
];

// Fungsi ringkas untuk dapatkan pelajar yang dipilih
function getStudentById(string $id, array $students)
{
    if (isset($students[$id])) {
        return $students[$id];
    }

    // fallback: pelajar pertama dalam senarai
    $first = reset($students);
    return $first;
}
