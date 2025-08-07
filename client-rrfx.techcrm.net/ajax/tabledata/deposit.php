<?php
$dt->query("
    SELECT 
        td.DPWD_DATETIME,
        tr.ACC_LOGIN,
        td.DPWD_AMOUNT_SOURCE,
        td.DPWD_AMOUNT,
        td.DPWD_CURR_FROM,
        td.DPWD_CURR_TO,
        td.DPWD_RATE,
        td.DPWD_PIC,
        td.DPWD_NOTE,
        td.DPWD_STS
    FROM tb_dpwd td
    JOIN tb_racc tr ON (tr.ID_ACC = td.DPWD_RACC) 
    WHERE td.DPWD_TYPE = 1
    AND td.DPWD_MBR = ".$user['MBR_ID']."
");

$dt->hide("DPWD_CURR_FROM");
$dt->hide("DPWD_CURR_TO");
$dt->hide("DPWD_NOTE");
$dt->edit("DPWD_AMOUNT_SOURCE", fn($col): string => App\Models\Helper::formatCurrency($col['DPWD_AMOUNT_SOURCE']) . " " . $col['DPWD_CURR_FROM']);
$dt->edit("DPWD_AMOUNT", fn($col): string => App\Models\Helper::formatCurrency($col['DPWD_AMOUNT']) . " " . $col['DPWD_CURR_TO']);
$dt->edit("DPWD_PIC", fn($col): string => '<a target="_blank" href="'.App\Models\FileUpload::awsFile($col['DPWD_PIC']).'"><i>Lihat</i></a>');
$dt->edit("DPWD_PIC", fn($col): string => '<a target="_blank" href="'.App\Models\FileUpload::awsFile($col['DPWD_PIC']).'"><i>Lihat</i></a>');

echo $dt->generate()->toJson();