<?php

use App\Models\Token;
use App\Models\TokenGenerator;

loadModel("Helper");
$helperClass = new Helper();

$data = $helperClass->getSafeInput($_POST);
foreach(['name', 'phone_code', 'phone', 'email', 'password', 'otp'] as $req) {
    if(empty($data[ $req ])) {
        ApiResponse([
            'status'    => false,
            'message'   => "{$req} is required",
            'response'  => []
        ], 400);
    }
}

$name           = preg_replace('/^[A-Za-z]+$/', '', $data['name']);
$phone_code     = preg_replace("/[^0-9]/", "", $data['phone_code']);
$phone          = $data['phone'];
$email          = form_input($_POST['email']);
$password       = form_input($_POST['password']);
$passwordHash   = password_hash($password, PASSWORD_BCRYPT);
$ibcode         = form_input($_POST['ibcode'] ?? "");
$otp            = $data['otp'];
$mbr_user       = str_replace(" ", "_", strtolower($name));
$mbr_idspn      = null;
$mbr_suffix     = null;
$mbr_code       = uniqid();

if(substr($phone, 0, 1) == '0') {
    $phone = substr($phone, 1);
}

/** Validate Phone length */
$final_phone  = "{$phone_code}{$phone}";
if(strlen($final_phone) < 10) {
    ApiResponse(array(
        'status' => false, 
        'message' => 'Nomor telepon tidak valid', 
        'response' => array()
    ), 400);
}

/** Validate Country Phone Code */
$sqlCheckCountry = $db->query("SELECT * FROM tb_country WHERE COUNTRY_PHONE_CODE = '+{$phone_code}' LIMIT 1");
if($sqlCheckCountry->num_rows != 1) {
    ApiResponse(array(
        'status' => false, 
        'message' => 'Kode Telepon tidak valid', 
        'response' => array()
    ), 400);
}

/** Check Country */
$country = $sqlCheckCountry->fetch_assoc();
$countryName = $country['COUNTRY_NAME'] ?? "Indonesia";

/** Validate email */
$SQL_EMAIL = mysqli_query($db,"SELECT MBR_EMAIL FROM tb_member WHERE LOWER(MBR_EMAIL) = '".strtolower($email)."'LIMIT 1");
if($SQL_EMAIL && mysqli_num_rows($SQL_EMAIL) != 0){
    ApiResponse(array(
        'status' => false, 
        'message' => 'email sudah terdaftar', 
        'response' => array()
    ), 400);
}

/** Validate Password */
$is_valid_password = validation_password($password);
if($is_valid_password !== TRUE) {
    ApiResponse(array(
        'status' => false, 
        'message' => $is_valid_password, 
        'response' => array()
    ), 400);
}

/** Validate Phone Number */
$sqlCheckOtp = $db->query("SELECT LOGVER_DATA FROM tb_log_verihub WHERE LOGVER_MBR = {$final_phone} AND (LOGVER_MODULE = '/v1/otp/send' OR LOGVER_MODULE = '/api/create-message') ORDER BY ID_LOGVER DESC LIMIT 1");
if($sqlCheckOtp->num_rows != 1) {
    ApiResponse(array(
        'status' => false, 
        'message' => "Nomor telepon tidak valid", 
        'response' => array()
    ), 400);
}

$logver = $sqlCheckOtp->fetch_assoc();
$logverdata = json_decode($logver['LOGVER_DATA'], true);
if($logverdata['otp'] != $otp) {
    ApiResponse(array(
        'status' => false, 
        'message' => "Kode OTP salah", 
        'response' => array()
    ), 400);
}

/** Create MBR_ID */
$sqlGet     = $db->query("SELECT UNIX_TIMESTAMP(NOW())+(SELECT IFNULL(MAX(tb1.ID_MBR),0) FROM tb_member tb1) AS MBR_ID");
$mbr_id     = $sqlGet->fetch_assoc()['MBR_ID'] ?? 0;
if($mbr_id == 0) {
    ApiResponse(array(
        'status' => false, 
        'message' => "Failed to create account, please try again", 
        'response' => array()
    ), 400);
}

/** Check referral if exits */
if(!empty($ibcode)) {
    $checkReferral = $helperClass->checkRefferal($ibcode);
    if(is_array($checkReferral)) {
        $mbr_idspn = $checkReferral['idspn'];
        $mbr_suffix = $checkReferral['suffix'];
    
    }else {
        ApiResponse(array(
            'status' => false, 
            'message' => $checkReferral, 
            'response' => array()
        ), 400);
    }
}

/** Validasi OTP dengan Verihubs */
$otpVerification = [];
if($logver['LOGVER_MODULE'] == "/v1/otp/send") {
    $verihub = new Verihubs();
    $otpVerification = $verihub->sendOtp_sms_verification([
        'mbrid'     => $final_phone,
        'msisdn'    => $final_phone,
        'otp'       => $otp
    ]);

    if(!$otpVerification['success']) {
        ApiResponse(array(
            'status' => false, 
            'message' => $otpVerification['message'], 
            'response' => array()
        ), 400);
    }
}


/** Insert Data */
$insert = $helperClass->insertWithArray("tb_member", [
    'MBR_ID' => $mbr_id,
    'MBR_IDSPN' => $mbr_idspn,
    'MBR_SUFFIX' => $mbr_suffix,
    'MBR_OTP' => $otp,
    'MBR_CODE' => $mbr_code,
    'MBR_COUNTRY' => $countryName,
    'MBR_NAME' => $name,
    'MBR_USER' => $mbr_user,
    'MBR_PHONE_CODE' => "+{$phone_code}",
    'MBR_PHONE' => $final_phone,
    'MBR_PASS' => $passwordHash,
    'MBR_EMAIL' => $email,
    'MBR_VERIF' => 1,
    'MBR_STS' => 2,
    'MBR_DATETIME' => date("Y-m-d H:i:s"),
    'MBR_IP' => $helperClass->get_ip_address(),
]);

if(!$insert || !mysqli_affected_rows($db)) {
    ApiResponse(array(
        'status' => false, 
        'message' => "Registration failed", 
        'response' => array()
    ), 400);
}

newInsertLog([
    'mbrid' => $mbr_id,
    'module' => "register",
    'ref' => $mbr_id,
    'device' => "mobile",
    'message' => "Register akun",
    'data'  => json_encode(array_merge($data, $otpVerification)),
    'ip' => $helperClass->get_ip_address() 
]);


$SQL_USER_INFO = mysqli_query($db,"SELECT * FROM tb_member WHERE MBR_ID = $mbr_id");
if($SQL_USER_INFO && mysqli_num_rows($SQL_USER_INFO) < 1){
    ApiResponse(array(
        'status' => false, 
        'message' => 'User tidak ditemukan', 
        'response' => array()
    ), 400);
}

// Generate tokens
$RESULT_USER_INFO = mysqli_fetch_assoc($SQL_USER_INFO);
$accessToken = TokenGenerator::generateAccessToken($user['MBR_ID']);
$refreshToken = TokenGenerator::generateRefreshToken($user['MBR_ID']);

// Save tokens
Token::saveTokens($RESULT_USER_INFO['MBR_ID'], $accessToken, $refreshToken);

ApiResponse([
    'status' => true,
    'message' => 'Berhasil membuat akun',
    'response' => array(
        "access_token" => $accessToken,
        "refresh_token" => $refreshToken,
        "expires_in" => ACCESS_TOKEN_LIFETIME,
        "personal_detail" => array(
            "id" => $RESULT_USER_INFO['MBR_ID'],
            "name" => $RESULT_USER_INFO['MBR_NAME'],
            "email" => $RESULT_USER_INFO['MBR_EMAIL'],
            "phone" => $RESULT_USER_INFO['MBR_PHONE'],
            "gender" => $RESULT_USER_INFO['MBR_JENIS_KELAMIN'],
            "city" => $RESULT_USER_INFO['MBR_CITY'],
            "country" => $RESULT_USER_INFO['MBR_COUNTRY'],
            "address" => $RESULT_USER_INFO['MBR_ADDRESS'],
            "zip" => $RESULT_USER_INFO['MBR_ZIP'],
            "tgl_lahir" => default_date($RESULT_USER_INFO['MBR_TGLLAHIR'], "Y-m-d"),
            "tmpt_lahir" => $RESULT_USER_INFO['MBR_TMPTLAHIR'],
            "type_id" => $RESULT_USER_INFO['MBR_TYPE_IDT'],
            "id_number" => $RESULT_USER_INFO['MBR_NO_IDT'],
            "url_photo" => $MBR_AVATAR,
            "status" => $RESULT_USER_INFO['MBR_STS'],
            "ver" => $RESULT_USER_INFO['MBR_VERIF']
        ),
    )
]);