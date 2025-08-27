<?php

use App\Models\Token;
use App\Models\TokenGenerator;
use App\Models\User;

$email = form_input($_POST['email']);
if(empty($email)) {
    ApiResponse([
        'status' => false,
        'message' => 'Email is required',
        'response' => []
    ], 400);
}



$user = User::findByEmail($email);
switch(true) {
    case empty($user):
        /** Email belum terdaftar dan bisa didaftarkan */
        if(true) {
            $mbr_id     = mysqli_query($db, "SELECT UNIX_TIMESTAMP(NOW())+(SELECT IFNULL(MAX(tb1.ID_MBR),0) FROM tb_member tb1) AS MBR_ID");
            $mbr_id     = mysqli_fetch_assoc($mbr_id)['MBR_ID'] ?? 0;
            $name       = form_input((empty($_POST['display_name'])? "Unknown Name" : $_POST['display_name']));
            $picture    = form_input((empty($_POST['display_picture'])? "-" : $_POST['display_picture']));
            $password   = generateRandomString(10);
            $passwordHash   = password_hash($password, PASSWORD_BCRYPT);
            $mbr_otp    = random_int(1000,9999);
            $mbr_code   = random_int(10000,99999);

            if($mbr_id == 0) {
                ApiResponse([
                    'status' => false,
                    'message' => 'Gagal membuat ID',
                    'response' => []
                ], 400);
            }

            $register = mysqli_query($db, '
                INSERT INTO tb_member SET 
                tb_member.MBR_ID        = '.$mbr_id.', 
                tb_member.MBR_NAME      = "'.$name.'", 
                tb_member.MBR_IB_CODE   = "admin", 
                tb_member.MBR_EMAIL     = "'.$email.'", 
                tb_member.MBR_PASS      = "'.$passwordHash.'", 
                tb_member.MBR_OTP       = "'.$mbr_otp.'", 
                tb_member.MBR_CODE      = "'.$mbr_code.'",
                tb_member.MBR_IP        = "'.$IP_ADDRESS.'",
                tb_member.MBR_OAUTH     = "google",
                tb_member.MBR_OAUTH_ID  = "'.$passwordHash.'",
                tb_member.MBR_OAUTH_PIC = "'.$picture.'",
                tb_member.MBR_STS       = 2,
                tb_member.MBR_VERIF       = 1,
                tb_member.MBR_DATETIME  = "'.date("Y-m-d H:i:s").'"
            ');

            if(!$register || !mysqli_affected_rows($db)) {
                ApiResponse([
                    'status' => false,
                    'message' => 'Failed to create account',
                    'response' => []
                ], 400);
            }

            ApiResponse([
                'status' => true,
                'message' => 'Successfully registered account with Google',
                'response' => array(
                    "token" => md5(md5($user['MBR_ID'])),
                    "personal_detail" => array(
                        "id"            => $mbr_id,
                        "name"          => $name,
                        "email"         => $email,
                        "phone"         => "0",
                        "gender"        => NULL,
                        "city"          => NULL,
                        "country"       => NULL,
                        "address"       => NULL,
                        "zip"           => NULL,
                        "tgl_lahir"     => NULL,
                        "tmpt_lahir"    => NULL,
                        "type_id"       => NULL,
                        "id_number"     => NULL,
                        "url_photo"     => "https://my.gifx.co.id/assets/images/admin.png",
                        "status"        => "2",
                        "ver"           => "1"
                    )
                )
            ], 200);
        }
        break;

    case (!empty($user) && $user['MBR_OAUTH'] == "google"):
        /** Email sudah terdaftar dan terdaftar menggunakan gmail google */
        if(true) {
            switch(true) {
                case (!empty($user['MBR_OAUTH_PIC']) && $user['MBR_OAUTH_PIC'] != "-"):
                    $MBR_AVATAR = $user['MBR_OAUTH_PIC'];
                    break;

                case (!empty($user['MBR_AVATAR']) && $user['MBR_AVATAR'] != "-"):
                    $MBR_AVATAR = 'https://allmediaindo-2.s3.ap-southeast-1.amazonaws.com/gifx/'.$user['MBR_AVATAR'];
                    break;

                default : $MBR_AVATAR = 'https://my.gifx.co.id/assets/images/admin.png'; break;
            }

            /** Get Bank */
            $list_bank = [];
            $SQL_BANK = mysqli_query($db, 'SELECT * FROM tb_member_bank WHERE MBANK_MBR = '.$user['MBR_ID']);
            if($SQL_BANK && mysqli_num_rows($SQL_BANK)){
                while($RESULT_BANK = mysqli_fetch_assoc($SQL_BANK)){
                    $list_bank[] = array(
                        'id' => $RESULT_BANK['ID_MBANK'],
                        'name' => $RESULT_BANK['MBANK_NAME'],
                        'account' => $RESULT_BANK['MBANK_ACCOUNT'],
                        'branch' => $RESULT_BANK['MBANK_BRANCH'],
                        'type' => $RESULT_BANK['MBANK_TYPE']
                    );
                };
            };

            /** Get Account */
            $list_acc = array();
            $SQL_ACCT = mysqli_query($db,'SELECT * FROM tb_racc WHERE ACC_MBR = '.$user['MBR_ID'].' AND ACC_LOGIN > 0');
            if($SQL_ACCT && mysqli_num_rows($SQL_ACCT)){
                while($RESULT_ACCT = mysqli_fetch_assoc($SQL_ACCT)){
                    if($RESULT_ACCT['ACC_DERE'] == 1){
                        $ACC_DERE = 'real';
                    } else if($RESULT_ACCT['ACC_DERE'] == 2){
                        $ACC_DERE = 'demo';
                    } else { $ACC_DERE = 'unknown'; }

                    $list_acc[] = array(
                        'id' => md5(md5($RESULT_ACCT['ID_ACC'])),
                        'account' => $RESULT_ACCT['ACC_LOGIN'],
                        'type' => $ACC_DERE,
                        'datetime' => $RESULT_ACCT['ACC_DATETIME']
                    );
                };
            };

            // Generate tokens
            $accessToken = TokenGenerator::generateAccessToken($user['MBR_ID']);
            $refreshToken = TokenGenerator::generateRefreshToken($user['MBR_ID']);

            // Save tokens
            Token::saveTokens($user['MBR_ID'], $accessToken, $refreshToken);


            /** Update Last Login */
            $update = mysqli_query($db, "UPDATE tb_member SET MBR_IP = '".$IP_ADDRESS."' WHERE MBR_ID = ".$user['MBR_ID']."");

            /** Response */
            ApiResponse([
                'status' => true,
                'message' => 'Successfully logged in with Google',
                'response' => array(
                    "access_token" => $accessToken,
                    "refresh_token" => $refreshToken,
                    "expires_in" => ACCESS_TOKEN_LIFETIME,
                    "personal_detail" => array(
                        "id"            => $user['MBR_ID'],
                        "name"          => $user['MBR_NAME'],
                        "email"         => $user['MBR_EMAIL'],
                        "phone"         => $user['MBR_PHONE'],
                        "gender"        => $user['MBR_JENIS_KELAMIN'],
                        "city"          => $user['MBR_CITY'],
                        "country"       => $user['MBR_COUNTRY'],
                        "address"       => $user['MBR_ADDRESS'],
                        "zip"           => $user['MBR_ZIP'],
                        "tgl_lahir"     => default_date($user['MBR_TGLLAHIR'], "Y-m-d"),
                        "tmpt_lahir"    => $user['MBR_TMPTLAHIR'],
                        "type_id"       => $user['MBR_TYPE_IDT'],
                        "id_number"     => $user['MBR_NO_IDT'],
                        "url_photo"     => $MBR_AVATAR,
                        "status"        => $user['MBR_STS'],
                        "ver"           => $user['MBR_VERIF']
                    ),
                    // "account_detail"    => $list_acc,
                    // "bank"              => $list_bank
                )
            ], 200);
        }
        break;

    default:
        ApiResponse([
            'status' => false,
            'message' => 'Invalid Account',
            'response' => []
        ], 400);
        break;
}