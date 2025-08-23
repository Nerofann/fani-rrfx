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
        JsonResponse([
            'success' => false,
            'message' => "Kolom {$text} harus diisi",
            'data' => []
        ]);
    }
}

/** check id */
$bank = User::myBank($user['MBR_ID'], $data['id']);
if(!$bank) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid ID",
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

$sqlCheck = $db->query("SELECT ID_MBANK FROM tb_member_bank WHERE MBANK_MBR = ".$user['MBR_ID']." AND MBANK_ACCOUNT = {$rekening} AND ID_MBANK != ".$bank['ID_MBANK']." LIMIT 1");
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

/** Update */
$updateData = [
    'MBANK_HOLDER' => $data['name'],
    'MBANK_NAME' => $data['bank-name'],
    'MBANK_ACCOUNT' => $rekening,
];

$update = Database::update("tb_member_bank", $updateData, ['ID_MBANK' => $bank['ID_MBANK']]);
if(!$update) {
    JsonResponse([
        'success' => false,
        'message' => "Gagal memperbarui bank",
        'data' => []
    ]);
}

JsonResponse([
    'success' => true,
    'message' => "Bank berhasil diperbarui",
    'data' => []
]);