<?php

    $dt->query('
        SELECT 
            tb_internal_transfer.IT_DATETIME,
            
            from_tb_member.MBR_NAME AS FROM_NAME,
            from_tb_member.MBR_EMAIL AS FROM_EMAIL,
            from_mt5_users.login AS FROM_LOGIN,
            
            to_tb_member.MBR_NAME AS TO_NAME,
            to_tb_member.MBR_EMAIL AS TO_EMAIL,
            to_mt5_users.login AS TO_LOGIN,
            
            IT_AMOUNT
        FROM tb_internal_transfer
        JOIN tb_racc from_tb_racc ON (from_tb_racc.ACC_LOGIN = tb_internal_transfer.IT_FROM)
        JOIN tb_member from_tb_member ON (from_tb_member.MBR_ID = from_tb_racc.ACC_MBR)
        JOIN meta_rrfxdemo.mt5_users from_mt5_users ON (from_mt5_users.LOGIN = tb_internal_transfer.IT_FROM)
        JOIN tb_racc to_tb_racc ON (to_tb_racc.ACC_LOGIN = tb_internal_transfer.IT_TO)
        JOIN tb_member to_tb_member ON (to_tb_member.MBR_ID = to_tb_racc.ACC_MBR)
        JOIN meta_rrfxdemo.mt5_users to_mt5_users ON (to_mt5_users.LOGIN = tb_internal_transfer.IT_TO)
    ');

    echo $dt->generate()->toJson();