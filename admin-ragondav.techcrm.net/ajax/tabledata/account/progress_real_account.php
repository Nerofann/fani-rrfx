<?php

    $dt->query('
        SELECT
            tb_racc.ACC_DATETIME,
            tb_racc.ACC_FULLNAME,
            LOWER(tb_member.MBR_EMAIL) AS MBR_EMAIL,
            (
                SELECT
                    CONCAT(
                        tb_racctype.RTYPE_NAME, "/",
                        tb_racctype.RTYPE_KOMISI, "/",
                        CASE
                            WHEN RTYPE_RATE = 0 THEN "Floating"
                            ELSE (RTYPE_RATE/1000)
                        END
                    )
                FROM tb_racctype
                WHERE tb_racctype.ID_RTYPE = tb_racc.ACC_TYPE
                LIMIT 1 
            ) AS ACC_TYPE,
            (
                SELECT
                    tb_racctype.RTYPE_TYPE_AS
                FROM tb_racctype
                WHERE tb_racctype.ID_RTYPE = tb_racc.ACC_TYPE
                LIMIT 1 
            ) AS RTYPE_TYPE_AS,
            (
                SELECT
                    IF((RTYPE_RATE = 0 OR RTYPE_ISFLOATING = 1), "Floating", FORMAT(tb_racctype.RTYPE_RATE, 0))
                FROM tb_racctype
                WHERE tb_racctype.ID_RTYPE = tb_racc.ACC_TYPE
                LIMIT 1 
            ) AS ACC_RATE,
            IF(tb_racc.ACC_LOGIN = 0 AND tb_racc.ACC_WPCHECK = 0, "REGISTER",
                IF(tb_racc.ACC_LOGIN = 0 AND tb_racc.ACC_WPCHECK = 1, "Verified",
                    IF(tb_racc.ACC_LOGIN = 0 AND tb_racc.ACC_WPCHECK = 2, "Deposit New Account",
                        IF(tb_racc.ACC_LOGIN = 0 AND tb_racc.ACC_WPCHECK = 3, "Waiting Depo",
                            IF(tb_racc.ACC_LOGIN = 0 AND tb_racc.ACC_WPCHECK = 4, "Waiting Depo.",
                                IF(tb_racc.ACC_LOGIN = 0 AND tb_racc.ACC_WPCHECK = 5, "GoodFund",
                                    IF(tb_racc.ACC_LOGIN <> 0 AND tb_racc.ACC_WPCHECK = 6, "Active", "Unknown")
                                )
                            )
                        )
                    )
                )
            ) AS ACC_STATUS,
            MD5(MD5(tb_racc.ID_ACC)) AS ID_ACC
        FROM tb_racc
        JOIN tb_member
        ON (tb_racc.ACC_MBR = tb_member.MBR_ID)
        WHERE tb_racc.ACC_DERE = 1
        #AND tb_racc.ACC_WPCHECK != 6
        AND tb_racc.ACC_STS = 1
        AND tb_racc.ACC_LOGIN = 0
        AND ACC_F_DISC = 1       
    ');
    $dt->edit('ACC_STATUS', function($data){ 
        if($data['ACC_STATUS'] == 'REGISTER'){
            return "
                <div class='text-center'>
                    <span class='badge bg-success h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>Register</span>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'Deposit New Account'){
            return "
                <div class='text-center'>
                    <span class='badge bg-primary h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['ACC_STATUS']))."</span>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'Waiting Depo'){
            return "
                <div class='text-center'>
                    <span class='badge h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px; background-color: purple;'>".(($data['ACC_STATUS']))."</span>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'Waiting Depo.'){
            return "
                <div class='text-center'>
                    <span class='badge bg-secondary h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>Waiting Finance</span>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'GoodFund'){
            return "
                <div class='text-center'>
                    <span class='badge bg-info h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['ACC_STATUS']))."</span>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'Active'){
            if($data['ACC_F_PROFILE_DATE'] < '2023-02-06 00:00:00'){
                return "
                    <div class='text-center'>
                        <span class='badge h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px; background: orange ;'>".(($data['ACC_STATUS']))."</span>
                    </div>
                ";
            } else {
                return "
                    <div class='text-center'>
                        <span class='badge bg-warning h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['ACC_STATUS']))."</span>
                    </div>
                ";
            }
        } else { 
            return "
                <div class='text-center'>
                    <span class='badge h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px; background-color: #184421;'>".(($data['ACC_STATUS']))."</span>
                </div>
            "; }
        return "<div class='text-center'>".$data['ACC_STATUS']."</div>";
    });
    $dt->edit('ID_ACC', function($data){
        if($data['ACC_STATUS'] == 'REGISTER'){
            return "
                <div class='text-center'>
                    <a href='/account/progress_real_account/wp_verification/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'Deposit New Account'){
            return "
                <div class='text-center'>
                    <a href='/account/progress_real_account/client_deposit/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'Waiting Depo'){
            return "
                <div class='text-center'>
                    <a href='/account/progress_real_account/wp_check/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'Waiting Depo.'){
            return "
                <div class='text-center'>
                    <a href='/account/progress_real_account/accounting/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'GoodFund'){
            return "
                <div class='text-center'>
                    <a href='/account/progress_real_account/dealer/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'Active'){
            if($data['ACC_F_PROFILE_DATE'] < '2023-02-06 00:00:00'){
                return "
                    <div class='text-center'>
                        <a href='/account/progress_real_account/document1/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a>
                    </div>
                ";
            } else {
                return "
                    <div class='text-center'>
                        <a href='/account/progress_real_account/document/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a>
                    </div>
                ";
            }
        } else { 
            return "
                <div class='text-center'>
                    <a href='/account/progress_real_account/temporary_detail/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a>
                </div>
            "; 
        }
    });

    echo $dt->generate()->toJson();