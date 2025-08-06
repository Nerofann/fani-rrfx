<?php

    use App\Models\FileUpload;

    $dt->query('
        SELECT
            tb_dpwd.DPWD_DATETIME,
            tb_racc.ACC_FULLNAME AS MBR_NAME,
            tb_member.MBR_EMAIL,
            tb_racc.ACC_LOGIN,
            tb_dpwd.DPWD_AMOUNT,
            tb_dpwd.DPWD_PIC,
            tb_dpwd.DPWD_VOUCHER,
            tb_dpwd.DPWD_NOTE1,
            IF(tb_dpwd.DPWD_STSVER = -1, "Accept",
                IF(tb_dpwd.DPWD_STSVER = 1, "Reject",
                    IF(tb_dpwd.DPWD_STSVER = 0, "Pending", "Unknown")
                )
            ) AS DPWD_STSVER,
            IF(tb_dpwd.DPWD_STSACC = -1, "Accept",
                IF(tb_dpwd.DPWD_STSACC = 1, "Reject",
                    IF(tb_dpwd.DPWD_STSACC = 0, "Pending", "Unknown")
                )
            ) AS DPWD_STSACC,
            IF(tb_dpwd.DPWD_STS = -1, "Accept",
                IF(tb_dpwd.DPWD_STS = 1, "Reject",
                    IF(tb_dpwd.DPWD_STS = 0, "Pending", "Unknown")
                )
            ) AS DPWD_STS,
            MD5(MD5(tb_dpwd.ID_DPWD)) AS ID_DPWD_HASH
        FROM tb_member
        JOIN tb_racc
        JOIN tb_dpwd
        ON(tb_member.MBR_ID = tb_racc.ACC_MBR
        AND tb_dpwd.DPWD_MBR = tb_member.MBR_ID
        AND tb_dpwd.DPWD_RACC = tb_racc.ID_ACC)
        WHERE tb_dpwd.DPWD_STS != 0
        AND tb_dpwd.DPWD_TYPE = 1
    ');

    
    $dt->edit('DPWD_DATETIME', function($data){
        return "<div class='text-center'>".$data['DPWD_DATETIME']."</div>";
    });
    $dt->edit('DPWD_STSACC', function($data){
        if($data['DPWD_STSACC'] == 'Accept'){
            return "
                <div class='text-center'>
                    <span class='badge bg-success h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STSACC']))."</span>
                </div>
            ";
        } else if($data['DPWD_STSACC'] == 'Reject'){
            return "
                <div class='text-center'>
                    <span class='badge bg-danger h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STSACC']))."</span>
                </div>
            ";
        }else if($data['DPWD_STSACC'] == 'Pending'){
            return "
                <div class='text-center'>
                    <span class='badge bg-warning h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STSACC']))."</span>
                </div>
            ";
        }else{
            return "
                <div class='text-center'>
                    <span class='badge bg-secondary h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STSACC']))."</span>
                </div>
            ";
        };
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
        } else if($data['DPWD_STSACC'] == 'Pending'){
            return "
                <div class='text-center'>
                    <span class='badge bg-warning h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STS']))."</span>
                </div>
            ";
        } else {
            return "
                <div class='text-center'>
                    <span class='badge bg-secondary h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STSACC']))."</span>
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
        } else if($data['DPWD_STSACC'] == 'Pending'){
            return "
                <div class='text-center'>
                    <span class='badge bg-warning h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STSVER']))."</span>
                </div>
            ";
        } else {
            return "
                <div class='text-center'>
                    <span class='badge bg-secondary h-50 d-inline-block bg-opacity-15 text-white' style='font-size: 12px;'>".(($data['DPWD_STSACC']))."</span>
                </div>
            ";
        };
    });
    $dt->edit('DPWD_AMOUNT', function($data){
        return "<div class='text-right'>".number_format($data['DPWD_AMOUNT'], 0)."</div>";
    });
    $dt->edit('DPWD_PIC', function($data){
        if(!empty($data["DPWD_PIC"])){
            return "<div class='text-center'><a target='_blank' href='".FileUpload::awsFile($data["DPWD_PIC"])."'>Pic</a></div>";
        }
    });
    $dt->edit('ID_DPWD_HASH', function($data){
        return "<div class='text-center'><a target='_blank' href='/documents/trans-topup.php?acc=".$data['ID_DPWD_HASH']."'>Print</a></div>";
    });
    echo $dt->generate()->toJson();