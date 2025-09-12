<?php

use App\Models\BankList;
use App\Models\FileUpload;
use App\Models\Helper;
use App\Models\MemberBank;
use App\Models\User;
use Config\Core\Database;

$data = Helper::getSafeInput($_POST);
$required = [
    'id' => "ID bank",
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

/** Update Data */
$updateData = [
    'MBANK_HOLDER' => $user['MBR_NAME'],
    'MBANK_NAME' => $data['bank-name'],
    'MBANK_ACCOUNT' => $rekening,
    'MBANK_STS' => MemberBank::$statusPending
];

/** Upload cover bank */
if(empty($_FILES['imagecover']) || $_FILES['imagecover']['error'] != 0) {
    if(empty($bank['MBANK_IMG'])) {
        JsonResponse([
            'success' => false,
            'message' => "Mohon upload cover bank",
            'data' => []
        ]);
    }
    
}else {
    $uploadCoverBank = FileUpload::upload_myfile($_FILES['imagecover'], "bank_cover");
    if(!is_array($uploadCoverBank) || !array_key_exists("filename", $uploadCoverBank)) {
        JsonResponse([
            'success' => false,
            'message' => $uploadCoverBank ?? "Gagal mengunggah cover buku tabungan",
            'data' => []
        ]);
    }

    $updateData['MBANK_IMG'] = $uploadCoverBank['filename'];
}

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