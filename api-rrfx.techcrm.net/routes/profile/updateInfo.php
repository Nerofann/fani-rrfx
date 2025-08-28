<?php

use App\Models\Database;

$data = $helperClass->getSafeInput($_POST);
$required = [
    'fullname' => "Nama Lengkap",
    'gender' => "Jenis Kelamin",
    'date_of_birth' => "Tanggal Lahir",
    'place_of_birth' => "Tempat Lahir",
    'address' => "Alamat Lengkap",
    'country' => "Negara Asal",
    'zipcode' => "Kode Pos"
];

foreach($required as $key => $req) {
    if(empty($data[ $key ])) {
        ApiResponse([
            'status' => false,
            'message' => "{$req} wajib diisi",
            'response' => [] 
        ], 400);
    }
}

/** Check Gender */
if(!in_array(strtolower($data['gender']), ['laki-laki', 'perempuan'])) {
    ApiResponse([
        'status' => false,
        'message' => "Format jenis kelamin tidak valid",
        'response' => [] 
    ], 400);
}

/** Check Country */
$country = strtolower($data['country']);
$sqlCheckCountry = $db->query("SELECT * FROM tb_country WHERE LOWER(COUNTRY_NAME) = '{$country}' LIMIT 1");
$fetch_country = $sqlCheckCountry->fetch_assoc();
if($sqlCheckCountry->num_rows != 1) {
    ApiResponse([
        'status' => false,
        'message' => "Nama Negara tidak valid",
        'response' => [] 
    ], 400);
}

/** Check Kode pos */
$kodepos = $data['zipcode'];
$sqlGetKodePos = $db->query("SELECT KDP_POS FROM tb_kodepos WHERE KDP_POS = {$kodepos} LIMIT 1");
$fetch_kode = $sqlGetKodePos->fetch_assoc();
if($sqlGetKodePos->num_rows != 1) {
    ApiResponse([
        'status' => false,
        'message' => "Kodepos tidak valid",
        'response' => [] 
    ], 400);
}

/** Update */
$updateData = [
    'MBR_NAME' => $data['fullname'],
    'MBR_JENIS_KELAMIN' => ucwords($data['gender']),
    'MBR_TGLLAHIR' => $data['date_of_birth'],
    'MBR_TMPTLAHIR' => $data['place_of_birth'],
    'MBR_ADDRESS' => $data['address'],
    'MBR_COUNTRY' => $fetch_country['COUNTRY_NAME'],
    'MBR_ZIP' => $fetch_kode['KDP_POS']
];

$update = Database::updateWithArray("tb_member", $updateData, ['MBR_ID' => $userData['MBR_ID']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal memperbarui profile",
        'response' => [] 
    ], 400);
}

newInsertLog([
    'mbrid' => $userData['MBR_ID'],
    'module' => "profile",
    'message' => "Memperbarui Profile",
    'device' => "mobile",
    'data'  => $data
]);

ApiResponse([
    'status' => true,
    'message' => "Profile berhasil diperbarui",
    'response' => [] 
]);