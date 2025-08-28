<?php

use App\Models\Token;
use App\Models\TokenGenerator;
use App\Models\User;

$refreshToken = form_input($_POST['refresh_token']);
if(empty($refreshToken)) {
    ApiResponse([
        'status' => false,
        'message' => 'Refresh token is required',
        'response' => []
    ], 400);
}

// Verify refresh token
$payload = TokenGenerator::verifyToken($refreshToken);
if (!$payload) {
    ApiResponse([
        'status' => false,
        'message' => 'Invalid refresh token',
        'response' => []
    ], 400);
}

// Find token in database
$tokenRecord = Token::findValidRefreshToken($refreshToken);
if (!$tokenRecord) {
    ApiResponse([
        'status' => false,
        'message' => 'Refresh token expired or revoked',
        'response' => []
    ], 400);
}

/** Revoke old token  */
$revokeToken = Token::revokeToken($refreshToken);
if(!$revokeToken) {
    ApiResponse([
        'status' => false,
        'message' => 'Invalid Refresh',
        'response' => []
    ], 400);
}

/** Generate new token */
$accessToken = TokenGenerator::generateAccessToken($payload['user_id']);
$refreshToken = TokenGenerator::generateRefreshToken($payload['user_id']);

/** Save new token */
$saveToken = Token::saveTokens($payload['user_id'], $accessToken, $refreshToken);
if(!$saveToken) {
    ApiResponse([
        'status' => false,
        'message' => 'Invalid Save Token',
        'response' => []
    ], 400);
}

/** find user */
$user = User::findByID($payload['user_id']);
if(!$user) {
    ApiResponse([
        'status' => false,
        'message' => 'User not found',
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => 'Berhasil menemukan akun',
    'response' => array(
        "access_token" => $accessToken,
        "refresh_token" => $refreshToken,
        "expires_in" => ACCESS_TOKEN_LIFETIME,
        "personal_detail" => array(
            "id" => $user['MBR_ID'],
            "name" => $user['MBR_NAME'],
            "email" => $user['MBR_EMAIL'],
            "phone" => $user['MBR_PHONE'],
            "gender" => $user['MBR_JENIS_KELAMIN'],
            "city" => $user['MBR_CITY'],
            "country" => $user['MBR_COUNTRY'],
            "address" => $user['MBR_ADDRESS'],
            "zip" => $user['MBR_ZIP'],
            "tgl_lahir" => default_date($user['MBR_TGLLAHIR'], "Y-m-d"),
            "tmpt_lahir" => $user['MBR_TMPTLAHIR'],
            "type_id" => $user['MBR_TYPE_IDT'],
            "id_number" => $user['MBR_NO_IDT'],
            "url_photo" => mbr_avatar($user['MBR_OAUTH_PIC'], $user['MBR_OAUTH_PIC']),
            "status" => $user['MBR_STS'],
            "ver" => $user['MBR_VERIF']
        ),
    )
], 200);