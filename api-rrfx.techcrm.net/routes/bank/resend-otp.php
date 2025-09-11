<?php

use App\Models\Helper;
use App\Models\MemberBank;
use Config\Core\Database;
use Config\Core\EmailSender;

$idBank = Helper::form_input($_POST['id'] ?? "");
$bank = MemberBank::findByIdHash($idBank);
if(!$bank) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid ID",
        'response' => []
    ], 400);
}

/** validasi status */
if($bank['MBANK_STS'] != MemberBank::$statusNotVerified) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Status",
        'response' => []
    ], 400);
}

/** validasi otp sebelumnya, expired / belum */
if(strtotime($bank['MBANK_OTP_EXPIRED']) > time()) {
    ApiResponse([
        'status' => false,
        'message' => "Anda harus menunggu beberapa menit untuk mengirim kembali",
        'response' => []
    ], 400);
}

/** update otp */
$otpCode = random_int(1000, 9999);
$otpExpired = date("Y-m-d H:i:s", strtotime("+30 minute"));
$update = Database::update("tb_member_bank", ['MBANK_OTP' => $otpCode, 'MBANK_OTP_EXPIRED' => $otpExpired], ['ID_MBANK' => $bank['ID_MBANK']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal",
        'response' => []
    ], 400);
}

/** Email OTP */
$emailData = [
    'subject' => "Bank OTP Verification",
    'otp'  => $otpCode,
];

$emailSender = EmailSender::init(['email' => $user['MBR_EMAIL'], 'name' => $user['MBR_NAME']]);
$emailSender->useFile("otp", $emailData);
$send = $emailSender->send();

ApiResponse([
    'status' => true,
    'message' => "Kode OTP berhasil dikirim",
    'response' => []
]);