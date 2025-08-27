<?php

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
if(empty($user)) {
    ApiResponse([
        'status' => false,
        'message' => 'Email not registered',
        'response' => []
    ], 400);
}

$new_pass = strtolower(uniqid());
$passwordHash   = password_hash($new_pass, PASSWORD_BCRYPT);

/** Update Password */
$sqlUpdate = $db->prepare("UPDATE tb_member SET MBR_PASS = ? WHERE ID_MBR = ?");
$sqlUpdate->bind_param("si", $passwordHash, $user['ID_MBR']);
if(!$sqlUpdate->execute()) {
    ApiResponse([
        'status'    => false,
        'message'   => "Failed to update password",
        'response'  => []
    ], 400);
}

/** Send Mail */
$emailData = [
    "name"          => $user["MBR_NAME"],
    "email"         => $user["MBR_EMAIL"],
    "newPassword"   => $new_pass,
    "subject"       => "Forget Password $web_name_full " .date("Y-m-d H:i:s"),
    "comp"          => $web_name_full
];

$sendEmail = new SendEmail();
$sendEmail->useDefault()->useFile("forget", $emailData)->destination($user["MBR_EMAIL"], $user["MBR_NAME"])->send();
ApiResponse([
    'status' => true,
    'message' => 'Successfully sent new password to email '.$email,
    'response' => []
], 200);