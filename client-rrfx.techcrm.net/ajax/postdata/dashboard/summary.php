<?php

use App\Models\Helper;

$sqlGet = $db->query("
    SELECT 
        IFNULL(SUM(tb_dpwd.DPWD_AMOUNT_SOURCE), 0) as AMOUNT,
        tb_dpwd.DPWD_TYPE,
        tr.ACC_LOGIN,
        trc.RTYPE_CURR,
        DPWD_CURR_TO
    FROM tb_dpwd
    JOIN tb_racc tr ON (tr.ID_ACC = tb_dpwd.DPWD_RACC)
    JOIN tb_racctype trc ON (trc.ID_RTYPE = tr.ACC_TYPE)
    WHERE MD5(MD5(DPWD_MBR)) = '{$userid}'
    AND ((tb_dpwd.DPWD_TYPE = 1 AND tb_dpwd.DPWD_STS = -1) OR (tb_dpwd.DPWD_TYPE = 2 AND tb_dpwd.DPWD_STS != 1))
    GROUP BY DPWD_TYPE, tr.ACC_LOGIN
");

$deposit = ['IDR' => 0, 'USD' => 0];
$withdrawal = ['IDR' => 0, 'USD' => 0];
if($sqlGet) {
    foreach($sqlGet->fetch_all(MYSQLI_ASSOC) as $dpwd) {
        switch(true) {
            case ($dpwd['DPWD_TYPE'] == 1 && $dpwd['DPWD_CURR_TO'] == "IDR") : $deposit['IDR'] += $dpwd['AMOUNT']; break;
            case ($dpwd['DPWD_TYPE'] == 1 && $dpwd['DPWD_CURR_TO'] == "USD") : $deposit['USD'] += $dpwd['AMOUNT']; break;
            case ($dpwd['DPWD_TYPE'] == 2 && $dpwd['DPWD_CURR_TO'] == "IDR") : $withdrawal['IDR'] += $dpwd['AMOUNT']; break;
            case ($dpwd['DPWD_TYPE'] == 2 && $dpwd['DPWD_CURR_TO'] == "USD") : $withdrawal['USD'] += $dpwd['AMOUNT']; break;
        }
    }
}

/** Summ Account */
$sqlGetAccount = $db->query("SELECT ID_ACC FROM tb_racc WHERE ACC_DERE = 1 AND ACC_LOGIN != 0 AND ACC_WPCHECK = 6 AND MD5(MD5(ACC_MBR)) = '{$userid}'");
$totalAccount = $sqlGetAccount->num_rows ?? 0;

JsonResponse([
    'success' => true,
    'message' => "Successfull",
    'data' => [
        'account' => $totalAccount,
        'deposit' => [
            'idr' => "Rp " . Helper::formatCurrency($deposit['IDR']),
            'usd' => "$".Helper::formatCurrency($deposit['USD'])
        ],
        'withdrawal' => [
            'idr' => "Rp " . Helper::formatCurrency($withdrawal['IDR']),
            'usd' => "$".Helper::formatCurrency($withdrawal['USD'])
        ],
    ]
]);