<?php
$dt->query("
    SELECT 
        tbi.BECOME_DATETIME,
        tm.MBR_NAME,
        tm.MBR_EMAIL,
        tbi.BECOME_NOTE,
        tbi.BECOME_STS,
        tbi.ID_BECOME
    FROM tb_become_ib tbi
    JOIN tb_member tm ON (tm.MBR_ID = tbi.BECOME_MBR)
    WHERE BECOME_STS != 0
");

$dt->edit("BECOME_STS", fn($col): string => App\Models\Ib::$status[ $col['BECOME_STS'] ]['html']);

echo $dt->generate()->toJson();