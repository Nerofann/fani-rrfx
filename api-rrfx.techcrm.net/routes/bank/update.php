<?php

use App\Models\BankList;
use App\Models\Helper;
use App\Models\User;
use Config\Core\Database;

$data = Helper::getSafeInput($_POST);
$required = [
    'id' => "ID bank",
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

/** check id */
$bank = User::myBank($user['MBR_ID'], $data['id']);
if(!$bank) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid ID",
        'response' => []
    ]);
}

/** check Nomor rekening */
$rekening = Helper::stringTonumber($data['bank-number']);
if(is_numeric($rekening) === FALSE || $rekening <= 0) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor rekening tidak valid",
        'response' => []
    ]);
}

$sqlCheck = $db->query("SELECT ID_MBANK FROM tb_member_bank WHERE MBANK_MBR = ".$user['MBR_ID']." AND MBANK_ACCOUNT = {$rekening} AND ID_MBANK != ".$bank['ID_MBANK']." LIMIT 1");
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

/** Update */
$updateData = [
    'MBANK_HOLDER' => $data['name'],
    'MBANK_NAME' => $data['bank-name'],
    'MBANK_ACCOUNT' => $rekening,
];

$update = Database::update("tb_member_bank", $updateData, ['ID_MBANK' => $bank['ID_MBANK']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal memperbarui bank",
        'response' => []
    ]);
}

ApiResponse([
    'status' => true,
    'message' => "Bank berhasil diperbarui",
    'response' => []
]);