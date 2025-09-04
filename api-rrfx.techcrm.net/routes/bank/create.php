<?php

use App\Models\BankList;
use App\Models\Helper;
use App\Models\User;
use Config\Core\Database;

$data = Helper::getSafeInput($_POST);
$required = [
    'name' => "Nama pemilik rekening",
    'bank-name' => "Nama Bank",
    'bank-number' => "Nomor Rekening"
];

foreach($required as $req => $text) {
    if(empty($data[ $req ])) {
        ApiResponse([
            'status' => false,
            'message' => "Kolom {$text} harus diisi",
            'response' => []
        ]);
    }
}

/** Check Max Bank */
$banks = User::myBank($user['MBR_ID']);
if(count($banks) >= 2) {
    ApiResponse([
        'status' => false,
        'message' => "Mencapai limit pembuatan bank",
        'response' => []
    ]);
}

/** check Nomor rekening */
$rekening = Helper::stringTonumber($data['bank-number']);
if(is_numeric($rekening) === FALSE || $rekening <= 0) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor rekening tidak valid",
        'data' => []
    ]);
}

$sqlCheck = $db->query("SELECT ID_MBANK FROM tb_member_bank WHERE MBANK_MBR = ".$user['MBR_ID']." AND MBANK_ACCOUNT = {$rekening} LIMIT 1");
if($sqlCheck->num_rows != 0) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor Rekening sudah terdaftar",
        'response' => []
    ]);
}

/** Check Nama Bank */
$bankName = BankList::findByName($data['bank-name']);
if(!$bankName) {
    ApiResponse([
        'status' => false,
        'message' => "Nama Bank tidak valid",
        'response' => []
    ]);
}

/** validasi bank name */
if(!preg_match('/^[a-zA-Z\s]+$/', $data['name'])) {
    ApiResponse([
        'status' => false,
        'message' => "Nama Pemilik Rekening tidak valid",
        'response' => []
    ]);
}

/** insert */
$insert = Database::insert("tb_member_bank", [
    'MBANK_MBR' => $user['MBR_ID'],
    'MBANK_HOLDER' => $data['name'],
    'MBANK_NAME' => $data['bank-name'],
    'MBANK_ACCOUNT' => $rekening,
    'MBANK_DATETIME' => date("Y-m-d H:i:s")
]);

if(!$insert) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal membuat bank",
        'response' => []
    ]);
}

ApiResponse([
    'status' => true,
    'message' => "Berhasil membuat bank",
    'response' => []
]);