<?php
$dt->query("
    SELECT 
        tbi.BECOME_DATETIME,
        tm.MBR_NAME,
        tm.MBR_EMAIL,
        acc.ACCOUNTS_ID,
        tbi.ID_BECOME
    FROM tb_become_ib tbi
    JOIN tb_member tm ON (tm.MBR_ID = tbi.BECOME_MBR)
    JOIN (
        SELECT
            tr.ACC_MBR,
            GROUP_CONCAT(tr.ID_ACC SEPARATOR ',') as ACCOUNTS_ID
        FROM tb_racc tr
        WHERE tr.ACC_DERE = 1 
        AND tr.ACC_WPCHECK = 6
        AND tr.ACC_STS = -1
        GROUP BY tr.ACC_MBR
    ) as acc ON (acc.ACC_MBR = tm.MBR_ID)
    WHERE BECOME_STS = 0
");

$dt->edit("ACCOUNTS_ID", function($col) {
    $list = [];
});

echo $dt->generate()->toJson();