<?php

use App\Models\Helper;
use App\Models\User;
use Config\Core\EmailSender;

$email = Helper::form_input($_POST['email'] ?? "");
if(empty($email)) {
    ApiResponse([
        'status' => false,
        'message' => 'Kolom email diperlukan',
        'response' => []
    ], 400);
}

/** Check Email */
$sqlGet = $db->query("SELECT MBR_ID, MBR_EMAIL, MBR_NAME, MBR_RESET_CODE, MBR_RESET_EXPIRED FROM tb_member WHERE LOWER(MBR_EMAIL) = LOWER('{$email}') LIMIT 1");
if($sqlGet->num_rows != 1) {
    ApiResponse([
        'status' => false,
        'message' => "Email tidak terdaftar",
        'response' => []
    ]);
}

$userData = $sqlGet->fetch_assoc();
if(time() <= strtotime($userData['MBR_RESET_EXPIRED'] ?? time())) {
    ApiResponse([
        'status' => false,
        'message' => "Anda dapat mengirim lagi jika kode sebelumnya telah kedaluwarsa",
        'response' => []
    ]);
}

$resetCode = md5(md5(uniqid() . $userData['MBR_ID']));
$setCode = User::setResetPasswordCode($userData['MBR_ID'], $resetCode);
if(!$setCode) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal",
        'response' => []
    ]);
}

/** Send Mail */
$emailData = [
    'subject' => "Reset Password",
    'code' => $resetCode
];

$emailSender = EmailSender::init(['email' => $userData['MBR_EMAIL'], 'name' => $userData['MBR_NAME']]);
$emailSender->useFile("reset-password", $emailData)->send();

ApiResponse([
    'status' => true,
    'message' => 'Link reset password telah dikirimkan ke email '.$email,
    'response' => []
]);