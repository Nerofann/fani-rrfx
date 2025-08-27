<?php
/** Check apakah sudah memiliki akun demo */
$checkDemo = $classAcc->getDemoAccount($userId);
if(!empty($checkDemo)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Already have a demo account",
        'response'  => []
    ], 400);
}

function generatePassword(int $len = 8) {
    $lower = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    $upper = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    $specials = array('!','#','$','%','&','(',')','*','+',',','-','.',':',';','=','?','@','[',']','^','_','{','|','}','~');
    $digits = array('0','1','2','3','4','5','6','7','8','9');
    $all = array($lower, $upper, $specials, $digits);

    $pwd = $lower[array_rand($lower, 1)];
    $pwd = $pwd . $upper[array_rand($upper, 1)];
    $pwd = $pwd . $specials[array_rand($specials, 1)];
    $pwd = $pwd . $digits[array_rand($digits, 1)];

    for($i = strlen($pwd); $i < max(8, $len); $i++)
    {
        $temp = $all[array_rand($all, 1)];
        $pwd = $pwd . $temp[array_rand($temp, 1)];
    }

    return str_shuffle($pwd);
} 

/** Create demo */
$init_margin   = 10000;
$meta_pass     = (generatePassword());
$meta_investor = (generatePassword());
$meta_phone    = (generatePassword());
$login          = create_demoacc($meta_pass, $meta_investor, $userData["MBR_NAME"], $userData["MBR_EMAIL"]);

/** Get Demo Type */
$sqlGetType = $db->query("SELECT ID_RTYPE FROM tb_racctype WHERE UPPER(RTYPE_TYPE) = 'DEMO' LIMIT 1");
$demoType = $sqlGetType->fetch_assoc()['ID_RTYPE'] ?? 0;
if($sqlGetType->num_rows == 0 || $demoType == 0) {
    ApiResponse([
        'status'    => false,
        'message'   => "Failed to create a demo account, an invalid account type",
        'response'  => []
    ], 400);
}

/** Insert Demo */
$insert = $helperClass->insertWithArray("tb_racc", [
    'ACC_DERE' => 2,
    'ACC_DEVICE' => 'mobile',
    'ACC_TYPE' => $demoType,
    'ACC_MBR' => $userData['MBR_ID'],
    'ACC_LOGIN' => $login,
    'ACC_PASS' => $meta_pass,
    'ACC_INVESTOR' => $meta_investor,
    'ACC_PASSPHONE' => $meta_phone,
    'ACC_INITIALMARGIN' => $init_margin,
]);

if(!$insert) {
    ApiResponse([
        'status'    => false,
        'message'   => "Failed to create a demo account",
        'response'  => []
    ], 400);
}

/** Send Notification Email */
$data   = [
    "name"          => $userData["MBR_NAME"],
    "login"         => $login,
    "metaPassword"  => $meta_pass,
    "metaInvestor"  => $meta_investor,
    "metaPassPhone" => $meta_phone,
    "subject"       => "Demo Account Information {$web_name_full} ".date('Y-m-d H:i:s')
];

$sendEmail = new SendEmail();
$sendEmail->useDefault()
    ->useFile("create-demo", $data)
    ->destination($userData['MBR_EMAIL'], ($userData['MBR_NAME'] ?? "user"))
    ->send();

newInsertLog([
    'mbrid' => $userData['MBR_ID'],
    'module' => "create-demo",
    'message' => "Create Demo Account {$login}",
    'data'  => json_encode($data)
]);

ApiResponse([
    'status'    => true,
    'message'   => "Successfully created a demo account",
    'response'  => []
]);