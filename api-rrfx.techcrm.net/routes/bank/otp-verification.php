<?php

use App\Models\Helper;
use App\Models\MemberBank;
use Config\Core\Database;

$idBank = Helper::form_input($_POST['id'] ?? "");
$bank = MemberBank::findByIdHash($idBank);
if(!$bank) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid ID",
        'response' => []
    ], 400);
}

/** check expired */
if(strtotime($bank['MBANK_OTP_EXPIRED']) < time()) {
    ApiResponse([
        'status' => false,
        'message' => "Kode OTP kadaluarsa",
        'response' => []
    ], 400);
}

/** check status */
if($bank['MBANK_STS'] != MemberBank::$statusNotVerified) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Status",
        'response' => []
    ], 400);
}

$otp = Helper::form_input($_POST['otp'] ?? '0');
$otp = Helper::stringTonumber($otp);
if($bank['MBANK_OTP'] != $otp) {
    ApiResponse([
        'status' => false,
        'message' => "Kode OTP salah",
        'response' => []
    ], 400);
}

/** update status */
$updateData = [
    'MBANK_STS' => MemberBank::$statusVerified,
    'MBANK_OTP_EXPIRED' => date("Y-m-d H:i:s")
];

$update = Database::update("tb_member_bank", $updateData, ['ID_MBANK' => $bank['ID_MBANK']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal",
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Verifikasi Berhasil",
    'response' => []
]);