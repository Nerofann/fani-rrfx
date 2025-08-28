<?php

$userBanks = myBank($userData['MBR_ID']);
$result = [];
foreach($userBanks as $bank) {
    $result[] = [
        'id' => md5(md5($bank['ID_MBANK'])),
        'holder' => $bank['MBANK_HOLDER'],
        'name' => $bank['MBANK_NAME'],
        'currency' => $bank['MBANK_CURR'],
        'account' => $bank['MBANK_ACCOUNT'],
        'branch' => $bank['MBANK_BRANCH'],
        'type' => $bank['MBANK_TYPE']
    ];
}

ApiResponse([
    'status' => true,
    'message' => "My Bank",
    'response' => $result
]);