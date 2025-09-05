<?php

$sqlGetAccounts = $db->query("
    SELECT
        tr.ID_ACC,
        tr.ACC_LOGIN,
        tr.ACC_DERE,
        tr.ACC_WPCHECK,
        tr.ACC_DATETIME,
        tr.ACC_STS,
        RTYPE_NAME,
        RTYPE_TYPE,
        RTYPE_ISFLOATING,
        RTYPE_RATE,
        RTYPE_CURR,
        RTYPE_MINDEPOSIT,
        RTYPE_MINTOPUP,
        RTYPE_MINWITHDRAWAL,
        RTYPE_MAXWITHDRAWAL,
        note.NOTE_NOTE
    FROM tb_racc tr
    JOIN tb_racctype trc ON (trc.ID_RTYPE = tr.ACC_TYPE)
    LEFT JOIN (
        SELECT 
            NOTE_RACC,
            NOTE_NOTE
        FROM tb_note
        GROUP BY NOTE_RACC
    ) as note ON (note.NOTE_RACC = tr.ID_ACC)
    WHERE tr.ACC_DERE = 1
    AND tr.ACC_STS IN (0, 1, 2)
    AND tr.ACC_MBR = ".$user['MBR_ID']."
");

$accounts = [];
foreach($sqlGetAccounts->fetch_all(MYSQLI_ASSOC) as $acc) {
    $status = "Regol belum selesai";
    if($acc['ACC_STS'] == 2) {
        $status = "Ditolak";
    
    }elseif($acc['ACC_STS'] == 1) {
        $status = "Waiting";
        switch($acc['ACC_WPCHECK']) {
            case 0: $status = "Register"; break; // Sesudah regol menunggu accept admin
            case 2: $status = "Deposit New Account"; break; // Sesudah di accept WPB, menunggu nasabah melakukan deposit new account
            case 3: $status = "Waiting Deposit"; break; // Nasabah sudah melakukan deposit new account, menunggu diaccept admin
            case 4: $status = "Already Deposit"; break; // Deposit new account nasabah sudah di accept admin
            case 5: $status = "GoodFund"; break; // Pemberian login, password, investor, dll.
            case 6: $status = "Active"; break; // Account sudah active
        }
    }

    $accounts[] = [
        'id' => md5(md5($acc['ID_ACC'])),
        'login' => $acc['ACC_LOGIN'],
        'type' => $acc['RTYPE_TYPE'],
        'product' => $acc['RTYPE_NAME'],
        'rate' => ($acc['RTYPE_ISFLOATING'])? "Floating" : $acc['RTYPE_RATE'],
        'currency' => $acc['RTYPE_CURR'],
        'min_deposit' => floatval($acc['RTYPE_MINDEPOSIT']),
        'status' => $status,
        'last_note' => $acc['NOTE_NOTE'],
        'date_created' => date("Y-m-d H:i:s", strtotime($acc['ACC_DATETIME']))
    ];
}

ApiResponse([
    'status'    => true,
    'message'   => "Success",
    'response'  => $accounts
]);