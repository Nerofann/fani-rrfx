<?php

    use App\Models\FileUpload;

    $dt->query('
        SELECT
            tb_dpwd.DPWD_DATETIME,
            tb_racc.ACC_FULLNAME AS MBR_NAME,
            tb_member.MBR_EMAIL,
            tb_racc.ACC_LOGIN,
            tb_dpwd.DPWD_AMOUNT,
            tb_dpwd.DPWD_NOTE1,
            IF(tb_dpwd.DPWD_STSVER = -1, "Accept",
                IF(tb_dpwd.DPWD_STSVER = 1, "Reject",
                    IF(tb_dpwd.DPWD_STSVER = 0, "Pending", "Unknown")
                )
            ) AS DPWD_STSVER,
            IF(tb_dpwd.DPWD_STS = -1, "Accept",
                IF(tb_dpwd.DPWD_STS = 1, "Reject",
                    IF(tb_dpwd.DPWD_STS = 0, "Pending", "Unknown")
                )
            ) AS DPWD_STS,
            MD5(MD5(tb_dpwd.ID_DPWD)) AS ID_DPWD_HASH,
            tb_racctype.RTYPE_CURR_SYMBOL
        FROM tb_member
        JOIN tb_racc
        JOIN tb_dpwd
        JOIN tb_racctype
        ON(tb_member.MBR_ID = tb_racc.ACC_MBR
        AND tb_dpwd.DPWD_MBR = tb_member.MBR_ID
        AND tb_dpwd.DPWD_RACC = tb_racc.ID_ACC
		AND tb_racc.ACC_TYPE = tb_racctype.ID_RTYPE)
        WHERE tb_dpwd.DPWD_STS != 0
        AND tb_dpwd.DPWD_TYPE = 2
    ');

    
    $dt->edit('DPWD_DATETIME', function($data){
        return "<div class='text-center'>".$data['DPWD_DATETIME']."</div>";
    });
    $dt->edit('DPWD_STS', function($data){
        if($data['DPWD_STS'] == 'Accept'){
            return "
                <div class='text-center'>
                    <span class='badge bg-success h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STS']))."</span>
                </div>
            ";
        } else if($data['DPWD_STS'] == 'Reject'){
            return "
                <div class='text-center'>
                    <span class='badge bg-danger h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STS']))."</span>
                </div>
            ";
        } else {
            return "
                <div class='text-center'>
                    <span class='badge bg-secondary h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STS']))."</span>
                </div>
            ";
        };
    });
    $dt->edit('DPWD_STSVER', function($data){
        if($data['DPWD_STSVER'] == 'Accept'){
            return "
                <div class='text-center'>
                    <span class='badge bg-success h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STSVER']))."</span>
                </div>
            ";
        } else if($data['DPWD_STSVER'] == 'Reject'){
            return "
                <div class='text-center'>
                    <span class='badge bg-danger h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STSVER']))."</span>
                </div>
            ";
        } else {
            return "
                <div class='text-center'>
                    <span class='badge bg-secondary h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STSVER']))."</span>
                </div>
            ";
        };
    });
    $dt->edit('DPWD_AMOUNT', function($data){
        return "<div class='text-end'>".$data['RTYPE_CURR_SYMBOL'].". ".number_format($data['DPWD_AMOUNT'], 0)."</div>";
    });
    $dt->edit('ID_DPWD_HASH', function($data){
        return "<div class='text-center'><a target='_blank' href='/export/trans-withdrawal?acc=".$data['ID_DPWD_HASH']."'>Print</a></div>";
    });
    echo $dt->generate()->toJson();