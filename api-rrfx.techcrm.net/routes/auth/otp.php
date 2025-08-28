<?php
$data = $helperClass->getSafeInput($_POST);
if(empty($data['email'])) {
    ApiResponse([
        'status' => false,
        'message' => "Email diperlukan",
        'response' => []
    ], 400);
}

if(empty($data['otp_code'])) {
    ApiResponse([
        'status' => false,
        'message' => "Kode OTP diperlukan",
        'response' => []
    ], 400);
}

$email = form_input($data['email']);
$otp_code = form_input($data['otp_code']);

/** Validate Email */    
$SQL_EMAIL = mysqli_query($db,"SELECT * FROM tb_member WHERE MBR_EMAIL = '{$email}' LIMIT 1");
if(mysqli_num_rows($SQL_EMAIL) != 1) {
    ApiResponse([
        'status' => false,
        'message' => "Email tidak terdaftar",
        'response' => []
    ], 400);
}

/** Validasi dengan database */
$RESULT_EMAIL = mysqli_fetch_assoc($SQL_EMAIL);
if($RESULT_EMAIL['MBR_OTP'] != $otp_code) {
    ApiResponse([
        'status' => false,
        'message' => "Kode OTP Salah",
        'response' => []
    ], 400);
}

/** Validasi dengan Verihub */
$sendOtp = $verihub->sendOtp_sms_verification([
    'mbrid'     => $RESULT_EMAIL['MBR_ID'],
    'msisdn'    => $RESULT_EMAIL['MBR_PHONE'],
    'otp'       => $otp_code
]);

if(!$sendOtp['success']) {
    ApiResponse([
        'status' => false,
        'message' => $sendOtp['message'],
        'response' => []
    ], 400);
}

/** Update */
$sqlUpdate = $db->prepare("UPDATE tb_member SET MBR_STS = 2 WHERE MBR_ID = ?");
$sqlUpdate->bind_param("i", $RESULT_EMAIL['MBR_ID']);
if(!$sqlUpdate->execute()) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal menggunakan kode OTP",
        'response' => []
    ], 400);
}


/** Default Avatar */
switch(true) {
    case (!empty($user1['MBR_OAUTH_PIC']) && $user1['MBR_OAUTH_PIC'] != "-"): 
        $MBR_AVATAR = $RESULT_EMAIL['MBR_OAUTH_PIC']; 
        break;

    case (!empty($RESULT_EMAIL['MBR_AVATAR']) && $RESULT_EMAIL['MBR_AVATAR'] != "-") : 
        $MBR_AVATAR = $aws_folder . $RESULT_EMAIL['MBR_AVATAR'];
        break;
        
    default: $MBR_AVATAR = "/assets/images/admin.png"; break;
}

/** Success Response */
ApiResponse([
    'status' => true,
    'message' => "Verifikasi OTP Berhasil",
    'response' => array(
        "personal_detail" => array(
            "id" => $RESULT_EMAIL['MBR_ID'],
            "name" => $RESULT_EMAIL['MBR_NAME'],
            "email" => $RESULT_EMAIL['MBR_EMAIL'],
            "phone" => $RESULT_EMAIL['MBR_PHONE'],
            "gender" => $RESULT_EMAIL['MBR_JENIS_KELAMIN'],
            "city" => $RESULT_EMAIL['MBR_CITY'],
            "country" => $RESULT_EMAIL['MBR_COUNTRY'],
            "address" => $RESULT_EMAIL['MBR_ADDRESS'],
            "zip" => $RESULT_EMAIL['MBR_ZIP'],
            "tgl_lahir" => $RESULT_EMAIL['MBR_TGLLAHIR'],
            "tmpt_lahir" => $RESULT_EMAIL['MBR_TMPTLAHIR'],
            "type_id" => $RESULT_EMAIL['MBR_TYPE_IDT'],
            "id_number" => $RESULT_EMAIL['MBR_NO_IDT'],
            "url_photo" => $MBR_AVATAR,
            "status" => $RESULT_EMAIL['MBR_STS'],
            "ver" => $RESULT_EMAIL['MBR_VERIF']
        ),
    ),
]);