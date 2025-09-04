<?php

use App\Models\Helper;
use App\Models\Logger;
use App\Models\Token;
use App\Models\User;
use Config\Core\Database;
use Config\Core\EmailSender;

/** check token */
$userToken = $_SERVER['HTTP_AUTHORIZATION'] ?? "";
$userToken = str_replace("Bearer ", "", $userToken);
$isValid = Token::verifyToken($userToken);
if(!$isValid || !is_array($isValid)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Token",
        'response' => []
    ], 300);
}

$user = User::findByMemberId($isValid['user_id']);
$userId = md5(md5($isValid['user_id']));
if(empty($user)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid User",
        'response' => []
    ], 400);
}

if($user['MBR_STS'] != 0) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Status",
        'response' => []
    ]);
}

/** check otp */
if(!empty($user['MBR_OTP_EXPIRED']) && strtotime($user['MBR_OTP_EXPIRED']) >= strtotime("now")) {
    ApiResponse([
        'status' => false,
        'message' => "Anda harus menunggu beberapa menit sebelum mengirim ulang",
        'response' => []
    ]);
}

/** update OTP */
$otpCode = random_int(1000, 9999);
$otpExpired = date("Y-m-d H:i:s", strtotime("+5 minute"));
$update = Database::update("tb_member", ['MBR_OTP' => $otpCode, 'MBR_OTP_EXPIRED' => $otpExpired], ['MBR_ID' => $user['MBR_ID']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Failed to send OTP",
        'response' => []
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
    'message' => "Resend OTP code " . $user['MBR_EMAIL'],
    'device' => implode(", ", array_values($_POST['device'] ?? [])),
    'data' => array_merge(($emailData ?? []))
]);

ApiResponse([
    'status' => true,
    'message' => "Kode OTP berhasil dikirimkan",
    'response' => []
]);