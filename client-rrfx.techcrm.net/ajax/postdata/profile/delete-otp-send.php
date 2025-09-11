<?php

use App\Models\Logger;
use Config\Core\Database;
use Config\Core\EmailSender;

$otpCode = random_int(1000, 9999);
$otpExpired = date("Y-m-d H:i:s", strtotime("+5 minute"));
$update = Database::update("tb_member", 
[
    'MBR_OTP' => $otpCode,
    'MBR_OTP_EXPIRED' => $otpExpired
], [
    'MBR_ID' => $user['MBR_ID']
]);
if(!$update) {
    JsonResponse([
        'success' => false,
        'message' => "Gagal memperbarui data",
        'data' => []
    ]);
}

/** Email OTP */
$emailData = [
    'subject' => "Delete account OTP",
    'otp'  => $otpCode,
];

$emailSender = EmailSender::init(['email' => $user['MBR_EMAIL'], 'name' => $user['MBR_NAME']]);
$emailSender->useFile("otp-delete", $emailData);
$send = $emailSender->send();

/** Log */
Logger::client_log([
    'mbrid' => $user['MBR_ID'],
    'module' => "send-otp-delete",
    'data' => array_merge($_POST, $emailData),
    'message' => "Send OTP delete code " . $user['MBR_EMAIL']
]);

JsonResponse([
    'success' => true,
    'message' => "Check OTP di email anda",
    'data' => []
]);