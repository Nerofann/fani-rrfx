<?php
$list_bank = array();
foreach(myBank($userId) as $RESULT_BANK) {
    $list_bank[] = array(
        'id' => md5(md5($RESULT_BANK['ID_MBANK'])),
        'name' => $RESULT_BANK['MBANK_NAME'],
        'account' => $RESULT_BANK['MBANK_ACCOUNT'],
        'branch' => $RESULT_BANK['MBANK_BRANCH'],
        'type' => $RESULT_BANK['MBANK_TYPE'],
        'user_name' => $RESULT_BANK['MBANK_HOLDER'],
    );
}

ApiResponse([
    'status'    => true,
    'message'   => "Success",
    'response'  => $list_bank
]);