<?php
$dt->query("
    SELECT
        IT_DATETIME,
        IT_FROM,
        IT_TO,
        IT_AMOUNT,
        IT_CODE
    FROM tb_internal_transfer tit
    JOIN tb_racc trFrom ON (trFrom.ACC_LOGIN = tit.IT_FROM AND trFrom.ACC_DERE = 1 AND trFrom.ACC_MBR = ".$user['MBR_ID'].")
    JOIN tb_racc trTo ON (trTo.ACC_LOGIN = tit.IT_FROM AND trTo.ACC_DERE = 1 AND trTo.ACC_MBR = ".$user['MBR_ID'].")
");

$dt->edit("IT_AMOUNT", fn($col): string => App\Models\Helper::formatCurrency($col['IT_AMOUNT']));
$dt->edit("IT_CODE", fn($col): string => "IT-".$col['IT_CODE']);

echo $dt->generate()->toJson();