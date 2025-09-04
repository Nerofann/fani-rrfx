<?php

use App\Models\Helper;
use App\Models\Logger;
use App\Models\User;
use Config\Core\Database;

$data = Helper::getSafeInput($_POST);
$required = [
    'current_pass' => "Password Saat ini",
    'new_pass' => "Password Baru",
    'confirm_new_pass' => "Konfirmasi Password Baru"
];

foreach($required as $key => $text) {
    if(empty($data[ $key ])) {
        ApiResponse([
            'status' => false,
            'message' => "Kolom {$text} wajib diisi",
            'response' => []
        ]);
    }
}

/** Check password saat ini */
if(!password_verify($data['current_pass'], $user['MBR_PASS'])) {
    ApiResponse([
        'status' => false,
        'message' => "Password Salah",
        'response' => []
    ]);
}

/** validasi password */
$isValidate = User::validation_password($data['new_pass']);
if($isValidate !== TRUE) {
    ApiResponse([
        'status' => false,
        'message' => $isValidate ?? "Format password salah",
        'response' => []
    ]);
}

/** check password konfirmasi */
if(base64_encode($data['new_pass']) != base64_encode($data['confirm_new_pass'])) {
    ApiResponse([
        'status' => false,
        'message' => "Password konfirmasi salah",
        'response' => []
    ]);
}

/** Update Password */
$passwordHash = password_hash($data['new_pass'], PASSWORD_BCRYPT);
$update = Database::update("tb_member", ['MBR_PASS' => $passwordHash], ['MBR_ID' => $user['MBR_ID']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal memperbarui password",
        'response' => []
    ]);
}

Logger::client_log([
    'mbrid' => $user['MBR_ID'],
    'module' => "security/update-password",
    'data' => $data,
    'device' => implode(", ", array_values(json_decode($_POST['device'] ?? "{}", true))),
    'message' => "Update password"
]);

ApiResponse([
    'status' => true,
    'message' => "Berhasil",
    'response' => []
]);