<?php

/** Required fullname */

use App\Models\Country;
use Config\Core\Database;
use App\Models\Helper;

if($user['MBR_STS'] != 2) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Status",
        'response' => []
    ]);
}

$data = Helper::getSafeInput($_POST);
if(empty($user['MBR_NAME'])) {
    if(empty($data['fullname'])) {
        ApiResponse([
            'status' => false,
            'message' => "Fullname field is required",
            'response' => []
        ]);
    }
}

/** Required phone */
if(empty($user['MBR_PHONE'])) {
    if(empty($data['phone'])) {
        ApiResponse([
            'status' => false,
            'message' => "Phone field is required",
            'response' => []
        ]);
    }
}

$fullname = $data['fullname'] ?? $user['MBR_NAME'];
$phone = $data['phone'] ?? $user['MBR_PHONE'];
$gender = $data['gender'] ?? $user['MBR_JENIS_KELAMIN'];
$country = $data['country'] ?? $user['MBR_COUNTRY'];
$address = $data['address'] ?? $user['MBR_ADDRESS'];

/** validasi nama lengkap */
if(!preg_match('/^[a-zA-Z\s]+$/', $fullname)) {
    ApiResponse([
        'status' => false,
        'message' => "Nama Lengkap tidak valid",
        'response' => []
    ]);
}

/** check Country */
$d_country = Country::getByName("Indonesia");
if(!$d_country) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Country Name",
        'response' => []
    ]);
}

/** Update */
$updateData = [
    'MBR_NAME'  => $fullname,
    'MBR_PHONE' => $phone,
    'MBR_JENIS_KELAMIN' => strtoupper($gender ?? ""),
    'MBR_COUNTRY' => $d_country['COUNTRY_NAME'],
    'MBR_ADDRESS' => $address,
    'MBR_VERIF' => -1,
    'MBR_STS' => -1
];

$update = Database::update("tb_member", $updateData, ['MBR_ID' => $user['MBR_ID']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Failed to update data",
        'response' => []
    ]);
}

ApiResponse([
    'status' => true,
    'message' => "Verification successfull",
    'response' => []
]);