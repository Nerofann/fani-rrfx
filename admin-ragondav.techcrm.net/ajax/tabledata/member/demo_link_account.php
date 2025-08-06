<?php

    $dt->query('
        SELECT
            mt5_users.REGDATE,
            mt5_users.LOGIN,
            tb_racc.ACC_FULLNAME,
            tb_member.MBR_EMAIL
        FROM tb_racc
        JOIN tb_member
        JOIN mt5_users
        ON(tb_racc.ACC_MBR = tb_member.MBR_ID
        AND tb_racc.ACC_LOGIN = mt5_users.LOGIN)
        WHERE tb_racc.ACC_DERE = 2
    ');

    $dt->edit('REGDATE', function($data){

        return '
            <div class="text-center">
                '.$data["REGDATE"].'
            </div>
        ';

    });

    echo $dt->generate()->toJson();