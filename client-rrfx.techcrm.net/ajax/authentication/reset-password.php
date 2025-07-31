<?php

use Config\Core\Database;
use App\Models\Helper;
use App\Models\User;

$data = Helper::getSafeInput($_POST);
$required = [
    'code' => "Code",
    'password' => "New Password",
    'password_confirm' => "Password Confirmation"
];

foreach($required as $req => $text) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success' => false,
            'message' => "$text field is required",
            'data' => []
        ]);
    }
}

/** Validasi Code */
$isValidCode = User::verifyResetCode($data['code']);
if(!$isValidCode) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Code",
        'data' => []
    ]);
}

/** Validasi password baru */
$isValidPassword = User::validation_password($data['password']);
if($isValidPassword !== TRUE) {
    JsonResponse([
        'success' => false,
        'message' => $isValidPassword,
        'data' => []
    ]);
}

/** Validasi Confirm password */
if(base64_encode($data['password']) != base64_encode($data['password_confirm'])) {
    JsonResponse([
        'success' => false,
        'message' => "Wrong Password Confirmation",
        'data' => []
    ]);
}

/** Update Password */
$passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
$update = Database::update("tb_member", ['MBR_PASS' => $passwordHash, 'MBR_RESET_EXPIRED' => date("Y-m-d H:i:s")], ['MBR_ID' => $isValidCode]);
if(!$update) {
    JsonResponse([
        'success' => false,
        'message' => "Failed update update password",
        'data' => []
    ]);
}

JsonResponse([
    'success' => true,
    'message' => "Reset password successfull",
    'data' => []
]);