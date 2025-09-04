<?php

use App\Models\Helper;
use App\Models\Logger;
use App\Models\Token;
use App\Models\User;
use Config\Core\Database;
use Config\Core\EmailSender;

$status = [
    '0' => "otp",
    '1' => "suspend",
    '2' => "verification",
    '-1' => "active"
];

$data = Helper::getSafeInput($_POST);
foreach(['email', 'password'] as $req) {
    if(!isset($data[$req])) {
        ApiResponse([
            'status' => false,
            'message' => "kolom {$req} diperlukan",
            'response' => []
        ], 400);
    }
}

$email = $data['email'];
$password = $data['password'];

/** Check email */
$sqlCheckEmail = $db->query("SELECT * FROM tb_member WHERE LOWER(MBR_EMAIL) = LOWER('{$email}') LIMIT 1");
if($sqlCheckEmail->num_rows != 1) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ]);
} 

/** Validasi Password */
$userData = $sqlCheckEmail->fetch_assoc();
if(!password_verify($password, $userData['MBR_PASS']) && User::developerPassword($password) === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ]);
} 

if(!array_key_exists($userData['MBR_STS'], $status)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Status",
        'response' => []
    ]);
}

if($userData['MBR_STS'] == 1) {
    ApiResponse([
        'status' => false,
        'message' => "Akun anda telah diblokir",
        'response' => []
    ]);
}

/** Check Token Active */
$tokenData = User::getAuthData();
$active_refreshToken = $tokenData['refresh_token'];
if(!empty($active_refreshToken)) {
    Token::revokeToken($active_refreshToken);
}

/** Generate Token */
$accessToken = Token::generateAccessToken($userData['MBR_ID']);
$refreshToken = Token::generateRefreshToken($userData['MBR_ID']);

/** Save Token */
$saveToken = Token::saveTokens($userData['MBR_ID'], $accessToken, $refreshToken);
if(!$saveToken) {
    ApiResponse([
        'status'   => false,
        'message'   => "Invalid Status Token",
        'response'      => []
    ]);
}

/** Update last ip */
$update = Database::update("tb_member", ['MBR_IP' => Helper::get_ip_address()], ['MBR_ID' => $userData['MBR_ID']]);

/** Set Auth Data */
$authData = [
    'access_token' => $accessToken, 
    'refresh_token' => $refreshToken
];

if(isset($data['remember'])) {
    $authData['remember_me'] = true;
}

User::setAuthData($authData);
Logger::client_log([
    'mbrid' => $userData['MBR_ID'],
    'module' => "signin",
    'data' => $data,
    'device' => implode(", ", array_values(json_decode($_POST['device'] ?? "", true))),
    'message' => "Login " . $data['email']
]);

ApiResponse([
    'status' => true,
    'message' => 'Berhasil menemukan akun',
    'response' => array(
        "access_token" => $accessToken,
        "refresh_token" => $refreshToken,
        "expires_in" => ACCESS_TOKEN_LIFETIME,
        "status" => $status[ $userData['MBR_STS'] ]
    )
], 200);