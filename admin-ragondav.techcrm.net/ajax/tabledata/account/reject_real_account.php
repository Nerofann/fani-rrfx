<?php

    $old_query = '
        SELECT
            tb_racc.ACC_F_PROFILE_DATE,
            tb_racc.ACC_WPCHECK_DATE,
            tb_racc.ACC_FULLNAME,
            LOWER(tb_member.MBR_EMAIL) AS MBR_EMAIL,
            "Rejected" AS ACC_STATUS,
            (
                SELECT
                    tb_note.NOTE_NOTE
                FROM tb_note
                WHERE tb_note.NOTE_RACC = tb_racc.ID_ACC
                AND tb_note.NOTE_TYPE = "WP VER REJECT"
                LIMIT 1
            ) AS RJCT_NOTE,
            MD5(MD5(tb_racc.ID_ACC)) AS ID_ACC
        FROM tb_racc
        JOIN tb_member
        ON(tb_racc.ACC_MBR = tb_member.MBR_ID)
        WHERE tb_racc.ACC_WPCHECK = 6
        AND tb_racc.ACC_STS = -1
    ';
    $new_query = '
        SELECT
            tb_racc.ACC_F_PROFILE_DATE,
            tb_racc.ACC_WPCHECK_DATE,
            tb_racc.ACC_FULLNAME,
            LOWER(tb_member.MBR_EMAIL) AS MBR_EMAIL,
            "Rejected" AS ACC_STATUS,
            MD5(MD5(tb_racc.ID_ACC)) AS ID_ACC
        FROM tb_racc
        JOIN tb_member
        ON(tb_racc.ACC_MBR = tb_member.MBR_ID)
        WHERE tb_racc.ACC_WPCHECK = 6
        AND tb_racc.ACC_STS = -1
    ';
    $dt->query($new_query);
    $dt->edit('ACC_STATUS', function($data){ 
        if($data['ACC_STATUS'] == 'Rejected'){
            return "
                <div class='text-center'>
                    <span class='badge bg-danger h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>Rejected</span>
                </div>
            ";
        } else if($data['ACC_STATUS'] == 'REGISTER'){
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
                        <a href='/account/active_real_account/document1/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a>
                    </div>
                ";
            } else {
                return "
                    <div class='text-center'>
                        <a href='/account/active_real_account/document/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a> ||
                        <a href='/account/active_real_account/edit/".$data["ID_ACC"]."' class='btn btn-sm btn-primary'>Edit</a>
                    </div>
                ";
            }
        } else { 
            return "
                <div class='text-center'>
                    <a href='/account/reject_real_account/detail/".$data["ID_ACC"]."' class='btn btn-sm btn-info'>Detail</a>
                </div>
            "; 
        }
    });

    echo $dt->generate()->toJson();