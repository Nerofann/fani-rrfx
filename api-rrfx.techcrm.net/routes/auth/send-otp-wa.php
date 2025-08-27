<?php

use App\Models\ApiWatsap;

$data = $helperClass->getSafeInput($_POST);
if(empty($data['phone'])) {
    ApiResponse([
        'status' => false,
        'message' => "Phone number is required",
        'response' => []
    ], 400);
}

if(empty($data['phone_code'])) {
    ApiResponse([
        'status' => false,
        'message' => "Country code is required",
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
        'message' => "Invalid phone number",
        'response' => []
    ], 400);
}

/** Validate Phone */    
$final_phone = $phone_code . $phone;
// $SQL_PHONE = mysqli_query($db,"SELECT MBR_ID FROM tb_member WHERE MBR_PHONE = '{$final_phone}' LIMIT 1");
// if(mysqli_num_rows($SQL_PHONE) != 0) {
//     ApiResponse([
//         'status' => false,
//         'message' => "Nomor Telepon sudah terdaftar",
//         'response' => []
//     ], 400);
// }

$sendOtp = ApiWatsap::sendMessage([
    'phone' => $final_phone,
    'otp' => $otp,
    'mbrid' => $final_phone
]);

if(!$sendOtp['status']) {
    ApiResponse([
        'status' => false,
        'message' => $sendOtp['message'],
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "OTP code has been sent",
    'response' => []
]);