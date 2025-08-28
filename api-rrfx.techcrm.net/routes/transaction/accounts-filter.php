<?php
$login = form_input($_POST['login'] ?? "");
if(empty($login)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Nomor Login diperlukan",
        'response'  => []
    ], 400);
}

$result = [];
$accounts = myAccount($userId, "real");

/** Check akun pengirim */
$loginDetail = [];
foreach($accounts as $a) {
    if($a['ACC_LOGIN'] == $login) {
        $loginDetail = $a;
        break;
    }
}

if(empty($loginDetail)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Nomor Login {$login} tidak ditemukan",
        'response'  => []
    ], 400);
}

/** Check akun real di metatrader */
$logins = array_map(fn($ar): int => $ar['ACC_LOGIN'], $accounts);
$accountsMeta = $ApiMeta->accountGroupLogin(['logins' => $logins]);
if($accountsMeta->success) {
    $list = $accountsMeta->message ?? [];
    foreach($list as $acc) {
        $index = array_search($acc->Login, array_column($accounts, "ACC_LOGIN"));
        if($index !== FALSE) {
            /** Check account rate */
            if($accounts[ $index ]['RTYPE_RATE'] == $loginDetail['RTYPE_RATE'] && $accounts[ $index ]['ACC_LOGIN'] != $login) {
                $result[] = [
                    'login' => $acc->Login,
                    'balance' => "{$acc->Balance} USD"
                ];
            }
        }
    }
}

ApiResponse([
    'status'    => true,
    'message'   => "Berhasil",
    'response'  => $result
]);