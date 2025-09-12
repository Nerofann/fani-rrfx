<?php

use App\Models\Regol;

    $dt->query('
        SELECT
            IFNULL(tr.ACC_TIMESTAMP, tr.ACC_DATETIME) as ACC_DATETIME,
            tr.ACC_FULLNAME,
            LOWER(tm.MBR_EMAIL) AS MBR_EMAIL,
            CONCAT(trt.RTYPE_NAME, "/", trt.RTYPE_KOMISI, "/", CASE WHEN RTYPE_ISFLOATING = 1 THEN "Floating" ELSE (RTYPE_RATE/1000) END) as ACC_TYPE,
            trt.RTYPE_TYPE_AS,
            IF(trt.RTYPE_ISFLOATING = 1, "Floating", RTYPE_RATE) as ACC_RATE,
            tr.ACC_WPCHECK,
            MD5(MD5(tr.ID_ACC)) AS ID_ACC
        FROM tb_racc tr
        JOIN tb_member tm ON (tm.MBR_ID = tr.ACC_MBR)
        JOIN tb_racctype trt ON (trt.ID_RTYPE = tr.ACC_TYPE)
        WHERE tr.ACC_DERE = 1
        AND tr.ACC_STS = 1
        AND ACC_WPCHECK != '.Regol::$statusWPCheckActive.'
        AND tr.ACC_LOGIN = 0
        AND ACC_F_DISC = 1        
    ');

    $dt->edit("ACC_WPCHECK", fn($col): string => Regol::wpCheckStatus($col['ACC_WPCHECK'])['html']);
    $dt->edit('ID_ACC', function($col){
        switch($col['ACC_WPCHECK']) {
            case Regol::$statusWPCheckBankVerification:
                return '
                    <div class="text-center">
                        <a href="/account/progress_real_account/bank_verification/'.$col['ID_ACC'].'" class="btn btn-sm btn-info">Detail</a>
                    </div>
                ';

            case Regol::$statusWPCheckRegister:
                return '
                    <div class="text-center">
                        <a href="/account/progress_real_account/wp_verification/'.$col['ID_ACC'].'" class="btn btn-sm btn-info">Detail</a>
                    </div>
                ';

            case Regol::$statusWPCheckGoodFund:
                return '
                    <div class="text-center">
                        <a href="/account/progress_real_account/dealer/'.$col['ID_ACC'].'" class="btn btn-sm btn-info">Detail</a>
                    </div>
                ';
        }
    });

    echo $dt->generate()->toJson();