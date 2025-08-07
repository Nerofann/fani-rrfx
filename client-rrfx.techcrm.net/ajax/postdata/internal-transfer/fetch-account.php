<?php

use App\Models\Account;
use App\Models\Helper;

/** Check account */
$fromAccount = Helper::form_input($_POST['from'] ?? "");
$from = Account::realAccountDetail_byLogin($fromAccount);
if(!$from) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Account",
        'data' => []
    ]);
}

$result = [];
$mbrid = $user['MBR_ID'];
$rate = $from['RTYPE_RATE'];
$sqlGet = $db->query("SELECT ACC_LOGIN FROM tb_racc JOIN tb_racctype ON (ID_RTYPE = ACC_TYPE) WHERE ACC_MBR = {$mbrid} AND RTYPE_RATE = {$rate} AND ACC_LOGIN != '$fromAccount' AND ACC_DERE = 1 AND ACC_STS = -1 AND ACC_LOGIN != 0");
if($sqlGet->num_rows != 0) {
    foreach($sqlGet->fetch_all(MYSQLI_ASSOC) as $asoc) {
        $result[] = [
            'login' => $asoc['ACC_LOGIN'],
            'balance' => Helper::formatCurrency(0). " USD"
        ];
    }
}

JsonResponse([
    'success' => true,
    'message' => "Berhasil",
    'data' => $result
]);