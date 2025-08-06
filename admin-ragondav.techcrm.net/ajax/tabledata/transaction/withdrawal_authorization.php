<?php

    use App\Models\FileUpload;

    $dt->query('
        SELECT
            tb_dpwd.DPWD_DATETIME,
            tb_racc.ACC_FULLNAME,
            tb_member.MBR_EMAIL,
            tb_racc.ACC_LOGIN,
            tb_dpwd.DPWD_AMOUNT,			
            MD5(MD5(tb_dpwd.ID_DPWD)) AS ID_DPWD,
            JSON_OBJECT(
                "auth-login", tb_racc.ACC_LOGIN,
                "auth-name", tb_racc.ACC_FULLNAME,
                "auth-email", tb_member.MBR_EMAIL,
                "auth-amnt", CAST(FORMAT(tb_dpwd.DPWD_AMOUNT, 2) AS CHAR),
                "auth-bksrc", tb_dpwd.DPWD_BANKSRC,
                "auth-bkdst", tb_dpwd.DPWD_BANK,
                "auth-dpx", CAST(MD5(MD5(tb_dpwd.ID_DPWD)) AS CHAR)
            ) AS JSNDT
        FROM tb_member
        JOIN tb_racc
        JOIN tb_dpwd
        ON(tb_member.MBR_ID = tb_racc.ACC_MBR
        AND tb_dpwd.DPWD_MBR = tb_member.MBR_ID
        AND tb_dpwd.DPWD_RACC = tb_racc.ID_ACC)
        WHERE tb_dpwd.DPWD_STS = 0
        AND tb_dpwd.DPWD_STSVER = 0
        AND tb_dpwd.DPWD_TYPE = 2
    ');

    $dt->hide('JSNDT');

    $dt->edit('DPWD_AMOUNT', function($data){
        return '
            <div class="text-end">
                '.number_format(($data["DPWD_AMOUNT"] ?? 0), 2).'
            </div>
        ';
    });

    $dt->edit('ID_DPWD', function($data){
        return '
            <div class="text-center">
                <button type="button" class="btn btn-info edt-btn" data-bs-toggle="modal" data-bs-target="#myModalAuth" data-jsn="'.base64_encode($data["JSNDT"]).'">Detail</button>
            </div>
        ';
    });

    echo $dt->generate()->toJson();