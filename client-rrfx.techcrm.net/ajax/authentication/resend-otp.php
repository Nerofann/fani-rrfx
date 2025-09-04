<?php

use App\Models\Helper;
use App\Models\Logger;
use App\Models\ProfilePerusahaan;
use Config\Core\Database;
use Config\Core\EmailSender;

$uniqueCode = Helper::form_input($_POST['code'] ?? "");
$sqlGet = $db->query("SELECT * FROM tb_member WHERE MD5(MD5(CONCAT(MBR_ID, ID_MBR))) = '$uniqueCode' AND MBR_STS = 0 LIMIT 1");
if($sqlGet->num_rows != 1) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Code",
        'data' => []
    ]);
}

/** check otp */
$user = $sqlGet->fetch_assoc();
if(!empty($user['MBR_OTP_EXPIRED']) && strtotime($user['MBR_OTP_EXPIRED']) >= strtotime("now")) {
    JsonResponse([
        'success' => false,
        'message' => "Anda harus menunggu beberapa menit sebelum mengirim ulang",
        'data' => []
    ]);
}

/** update OTP */
$otpCode = random_int(1000, 9999);
$otpExpired = date("Y-m-d H:i:s", strtotime("+5 minute"));
$update = Database::update("tb_member", ['MBR_OTP' => $otpCode, 'MBR_OTP_EXPIRED' => $otpExpired], ['MBR_ID' => $user['MBR_ID']]);
if(!$update) {
    JsonResponse([
        'success' => false,
        'message' => "Failed to send OTP",
        'data' => []
    ]);
}

/** Email OTP */
$emailData = [
    'subject' => "OTP Verification",
    'otp'  => $otpCode,
];

$emailSender = EmailSender::init(['email' => $user['MBR_EMAIL'], 'name' => $user['MBR_NAME']]);
$emailSender->useFile("otp", $emailData);
$send = $emailSender->send();

/** Log */
Logger::client_log([
    'mbrid' => $user['MBR_ID'],
    'module' => "resend-otp",
    'data' => array_merge($_POST, $emailData),
    'message' => "Resend OTP code " . $user['MBR_EMAIL']
]);

JsonResponse([
    'success' => true,
    'message' => "Kode OTP berhasil dikirimkan",
    'data' => []
]);