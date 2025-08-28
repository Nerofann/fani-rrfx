<?php
$accountDemo = $classAcc->getDemoAccount($userId);

$sqlGetAccounts = $db->query("
    SELECT
        tr.ID_ACC,
        tr.ACC_LOGIN,
        tr.ACC_DERE,
        RTYPE_TYPE,
        RTYPE_ISFLOATING,
        RTYPE_RATE,
        RTYPE_CURR,
        RTYPE_MINDEPOSIT,
        RTYPE_MINTOPUP,
        RTYPE_MINWITHDRAWAL,
        RTYPE_MAXWITHDRAWAL,
        IFNULL((
            SELECT 
                SUM(td.DPWD_AMOUNT_SOURCE)
            FROM tb_dpwd td
            WHERE td.DPWD_MBR = tr.ACC_MBR
            AND td.DPWD_RACC = tr.ID_ACC
            AND td.DPWD_TYPE = 1
            AND td.DPWD_STS = -1
        ), 0) as TOTAL_DEPOSIT,
        IFNULL((
            SELECT 
                SUM(td.DPWD_AMOUNT_SOURCE)
            FROM tb_dpwd td
            WHERE td.DPWD_MBR = tr.ACC_MBR
            AND td.DPWD_RACC = tr.ID_ACC
            AND td.DPWD_TYPE = 2
            AND td.DPWD_STS = -1
        ), 0) as TOTAL_WITHDRAWAL
    FROM tb_racc tr
    JOIN tb_racctype trc ON (trc.ID_RTYPE = tr.ACC_TYPE)
    WHERE tr.ACC_DERE = 1
    AND tr.ACC_LOGIN != 0
    AND tr.ACC_STS = -1
    AND tr.ACC_MBR = ".$userData['MBR_ID']."
");

$accounts = $sqlGetAccounts->fetch_all(MYSQLI_ASSOC) ?? [];
$realAccountLogin = array_map(fn($ar): int => $ar['ACC_LOGIN'], $accounts);
$detailMetatrader = $ApiMeta->accountGroupLogin(['logins' => $realAccountLogin]);
if($detailMetatrader->success) {
    foreach($detailMetatrader->message as $metaAcc) {
        $index = array_search($metaAcc->Login, array_column($accounts, "ACC_LOGIN"));
        if($index !== FALSE) {
            $accounts[$index]['BALANCE'] = $metaAcc->Balance;
            $accounts[$index]['LEVERAGE'] = $metaAcc->Leverage;
            $accounts[$index]['PNL'] = $metaAcc->PNL;
            $accounts[$index]['FREE_MARGIN'] = $metaAcc->FreeMargin;
        }
    }
}

/** Assign demo account, if available */
if(!empty($accountDemo)) {
    $accounts[] = $accountDemo;
}

$listAccount = [
    'real' => [],
    'demo' => []
];

foreach($accounts as $acc) {
    $marginPercent = 0;
    if($acc['BALANCE'] > 0 && $acc['FREE_MARGIN'] > 0) {
        $marginPercent = round(($acc['FREE_MARGIN'] / $acc['BALANCE']) * 100, 2);
    }

    $type = $acc['ACC_DERE'] == 1 ? "real" : "demo";
    $listAccount[ $type ][] = [
        'id'            => md5(md5($acc['ID_ACC'])),
        'login'         => $acc['ACC_LOGIN'],
        'type'          => ($acc['ACC_DERE'] == 1)? "real" : "demo",
        'nama_tipe_akun'=> $acc['RTYPE_TYPE'] ?? NULL,
        'rate'          => ($acc['RTYPE_ISFLOATING'] == 1)? "Floating" : $acc['RTYPE_RATE'],
        'margin_free'   => $acc['FREE_MARGIN'] ?? 0,
        'margin_free_percent' => $marginPercent,
        'balance'       => number_format($acc['BALANCE'] ?? 0, 2, ".", ""),
        'leverage'      => number_format($acc['LEVERAGE'] ?? 0, 2, ".", ""),
        'pnl'           => number_format($acc['PNL'] ?? 0, 2, ".", ""),
        'currency'      => $acc['RTYPE_CURR'],
        'total_deposit' => ($acc['TOTAL_DEPOSIT']),
        'total_withdrawal' => ($acc['TOTAL_WITHDRAWAL']),
        'min_deposit'   => number_format($acc['RTYPE_MINDEPOSIT'], 2, ".", ""),
        'min_topup'     => number_format($acc['RTYPE_MINTOPUP'], 2, ".", ""),
        'min_withdrawal'=> number_format($acc['RTYPE_MINWITHDRAWAL'], 2, ".", ""),
        'max_withdrawal'=> number_format($acc['RTYPE_MAXWITHDRAWAL'], 2, ".", "")
    ];
}

ApiResponse([
    'status' => true,
    'message' => "Berhasil",
    'response' => $listAccount
]);