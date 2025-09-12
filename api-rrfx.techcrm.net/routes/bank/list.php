<?php

use App\Models\User;

$userBanks = User::myBank($user['MBR_ID']);
$result = [];
foreach($userBanks as $bank) {
    $result[] = [
        'id' => md5(md5($bank['ID_MBANK'])),
        'holder' => $bank['MBANK_HOLDER'],
        'name' => $bank['MBANK_NAME'],
        'account' => $bank['MBANK_ACCOUNT'],
    ];
}

ApiResponse([
    'status' => true,
    'message' => "My Bank",
    'response' => $result
]);