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
        JsonResponse([
            'success' => false,
            'message' => "Kolom {$text} harus diisi",
            'data' => []
        ]);
    }
}

/** Check Max Bank */
$banks = User::myBank($user['MBR_ID']);
if(count($banks) >= 2) {
    JsonResponse([
        'success' => false,
        'message' => "Mencapai limit pembuatan bank",
        'data' => []
    ]);
}

/** check Nomor rekening */
$rekening = Helper::stringTonumber($data['bank-number']);
if(is_numeric($rekening) === FALSE || $rekening <= 0) {
    JsonResponse([
        'success' => false,
        'message' => "Nomor rekening tidak valid",
        'data' => []
    ]);
}

$sqlCheck = $db->query("SELECT ID_MBANK FROM tb_member_bank WHERE MBANK_MBR = ".$user['MBR_ID']." AND MBANK_ACCOUNT = {$rekening} LIMIT 1");
if($sqlCheck->num_rows != 0) {
    JsonResponse([
        'success' => false,
        'message' => "Nomor Rekening sudah terdaftar",
        'data' => []
    ]);
}

/** Check Nama Bank */
$bankName = BankList::findByName($data['bank-name']);
if(!$bankName) {
    JsonResponse([
        'success' => false,
        'message' => "Nama Bank tidak valid",
        'data' => []
    ]);
}

/** validasi bank name */
if(!preg_match('/^[a-zA-Z\s]+$/', $data['name'])) {
    JsonResponse([
        'success' => false,
        'message' => "Nama Pemilik Rekening tidak valid",
        'data' => []
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
    JsonResponse([
        'success' => false,
        'message' => "Gagal membuat bank",
        'data' => []
    ]);
}

JsonResponse([
    'success' => true,
    'message' => "Berhasil membuat bank",
    'data' => []
]);