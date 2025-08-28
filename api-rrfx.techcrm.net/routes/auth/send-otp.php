<?php
loadModel('Verihubs');
$verihub = new Verihubs();

$data = $helperClass->getSafeInput($_POST);
if(empty($data['phone'])) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor HP diperlukan",
        'response' => []
    ], 400);
}

if(empty($data['phone_code'])) {
    ApiResponse([
        'status' => false,
        'message' => "Kode Negara diperlukan",
        'response' => []
    ], 400);
}

$phone_code = $data['phone_code'] ?? "62";
$phone_code = preg_replace("/[^0-9]/", "", $phone_code);
$otp        = random_int(1000, 9999);
$phone      = $data['phone'] ?? 0;

if(substr($phone, 0, 1) == '0') {
    $phone = substr($phone, 1);
}

if(empty($phone) ?? strlen($phone) < 10) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor telepon tidak valid",
        'response' => []
    ], 400);
}

/** Validate Phone */    
$final_phone = $phone_code . $phone;
$SQL_PHONE = mysqli_query($db,"SELECT MBR_ID FROM tb_member WHERE MBR_PHONE = '{$final_phone}' LIMIT 1");
if(mysqli_num_rows($SQL_PHONE) != 0) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor Telepon sudah terdaftar",
        'response' => []
    ], 400);
}

$sendOtp = $verihub->sendOtp_sms([
    'mbrid'     => $final_phone,
    'msisdn'    => $final_phone,
    'otp'       => $otp
]);

if(!$sendOtp['success']) {
    ApiResponse([
        'status' => false,
        'message' => $sendOtp['message'],
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Kode OTP berhasil dikirim",
    'response' => []
]);