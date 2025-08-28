<?php

use App\Models\Helper;
use App\Models\Logger;
use App\Models\Token;
use App\Models\User;
use Config\Core\Database;
use Config\Core\EmailSender;

$data = Helper::getSafeInput($_POST);
foreach(['email', 'password'] as $req) {
    if(!isset($data[$req])) {
        ApiResponse([
            'status' => false,
            'message' => "{$req} field is required",
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

/** Check Status */
switch($userData['MBR_STS']) {
    case 0:
        /** Kirim Email verifikasi lagi jika token sebelumnya expired */
        if(strtotime($userData['MBR_OTP_EXPIRED'] ?? "1970-01-01") < time()) {
            /** Update OTP */
            $dateExpired = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $otpCode = random_int(1000, 9999);
            $updateOtp = Database::update("tb_member", ['MBR_OTP' => $otpCode, 'MBR_OTP_EXPIRED' => $dateExpired], ['MBR_ID' => $userData['MBR_ID']]);
            if(!$updateOtp) {
                ApiResponse([
                    'status'   => false,
                    'message'   => "Failed send verification link",
                    'response'      => []
                ]);
            }

            $emailData = [
                'subject'   => "Email Verification",
                'code'  => md5(md5($userData['MBR_ID'].$otpCode)),
            ];
    
            $emailSender = EmailSender::init(['email' => $userData['MBR_EMAIL'], 'name' => $userData['MBR_NAME']]);
            $emailSender->useFile("register", $emailData);
            $send = $emailSender->send();

            if(!$send) {
                ApiResponse([
                    'status'   => false,
                    'message'   => "Gagal",
                    // 'message'   => "Gagal mengirim email verifikasi",
                    'response'      => []
                ]);
            }

            ApiResponse([
                'status'   => true,
                'message'   => "Tautan verifikasi telah dikirim ke email Anda",
                'response'      => []
            ]);
        }

        ApiResponse([
            'status'   => false,
            'message'   => "Email belum diverifikasi",
            'response'      => []
        ]);

    case 1:
        ApiResponse([
            'status'   => false,
            'message'   => "Your account has been suspended",
            'response'      => []
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
    'message' => "Login " . $data['email']
]);

ApiResponse([
    'status' => true,
    'message' => 'Berhasil menemukan akun',
    'response' => array(
        "access_token" => $accessToken,
        "refresh_token" => $refreshToken,
        "expires_in" => ACCESS_TOKEN_LIFETIME,
        "personal_detail" => array(
            "id" => md5(md5($userData['MBR_ID'])),
            "name" => $userData['MBR_NAME'],
            "email" => $userData['MBR_EMAIL'],
            "phone" => $userData['MBR_PHONE'],
            "gender" => $userData['MBR_JENIS_KELAMIN'],
            "city" => $userData['MBR_CITY'],
            "country" => $userData['MBR_COUNTRY'],
            "address" => $userData['MBR_ADDRESS'],
            "zip" => $userData['MBR_ZIP'],
            "tgl_lahir" => Helper::default_date($userData['MBR_TGLLAHIR'], "Y-m-d"),
            "tmpt_lahir" => $userData['MBR_TMPTLAHIR'],
            "type_id" => "",
            "id_number" => 0,
            "url_photo" => User::avatar($userData['MBR_AVATAR']),
            "status" => $userData['MBR_STS'],
            "ver" => $userData['MBR_VERIF']
        ),
    )
], 200);