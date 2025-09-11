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

if(empty($_FILES['bank-image']) || $_FILES['bank-image']['error'] != 0) {
    ApiResponse([
        'status' => false,
        'message' => "Mohon upload foto cover bank",
        'response' => []
    ], 400);
}

$uploadCoverBank = FileUpload::upload_myfile($_FILES['bank-image'], "bank_cover");
if(!is_array($uploadCoverBank) || !array_key_exists("filename", $uploadCoverBank)) {
    ApiResponse([
        'status' => false,
        'message' => $uploadCoverBank ?? "Gagal mengunggah file cover bank",
        'response' => []
    ], 400);
}

/** Update */
$otpCode = random_int(1000, 9999);
$otpExpired = date("Y-m-d H:i:s", strtotime("+30 minute"));
$updateData = [
    'MBANK_HOLDER' => $data['name'],
    'MBANK_NAME' => $data['bank-name'],
    'MBANK_ACCOUNT' => $rekening,
    'MBANK_IMG' => $uploadCoverBank['filename'],
    'MBANK_OTP' => $otpCode,
    'MBANK_OTP_EXPIRED' => $otpExpired,
    'MBANK_STS' => MemberBank::$statusNotVerified
];

$update = Database::update("tb_member_bank", $updateData, ['ID_MBANK' => $bank['ID_MBANK']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal memperbarui bank",
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Bank berhasil diperbarui, silahkan verifikasi otp ulang",
    'response' => []
]);