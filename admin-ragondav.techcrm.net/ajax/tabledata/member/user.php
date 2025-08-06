<?php
    $dt->query('
        SELECT
            tb_member.MBR_DATETIME,
            tb_member.MBR_NAME,
            tb_member.MBR_EMAIL,
            tb_member.MBR_STS,
            MD5(MD5(tb_member.ID_MBR)) AS ID_MBR
        FROM tb_member
    ');

    $dt->edit('MBR_STS', function($data){
        $STATS = [
            0  => ["clr" => "warning", "sts" => "Registered"],
            1  => ["clr" => "danger",  "sts" => "Disabled"],
            2  => ["clr" => "primary", "sts" => "Unverified"],
            -1 => ["clr" => "success", "sts" => "Verified"]
        ];
        return '
            <div class="text-center">
                <span class="badge bg-'.($STATS[$data["MBR_STS"]]["clr"] ??  'dark').'">'.($STATS[$data["MBR_STS"]]["sts"] ?? 'Unknown').'</span>
            </div>
        ';
    });

    $dt->edit('ID_MBR', function($data){
        return '
            <div class="text-center">
                <a class="btn btn-sm btn-info" href="/member/user/detail/'.$data["ID_MBR"].'">Detail</a> ||
                <a class="btn btn-sm btn-primary" href="/member/user/edit/'.$data["ID_MBR"].'">Edit</a>
            </div>
        ';
    });

    echo $dt->generate()->toJson();