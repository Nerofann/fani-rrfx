<?php

    $dt->query('
        SELECT 
            IT_DATETIME,    
            (
                    SELECT
                        tb_racc.ACC_FULLNAME
                    FROM tb_racc
                    JOIN tb_member
                    ON(tb_racc.ACC_MBR = tb_member.MBR_ID)
                    WHERE tb_racc.ACC_LOGIN = tb_internal_transfer.IT_FROM
                    LIMIT 1
            ) AS IT_FROM,
            (
                    SELECT
                        tb_member.MBR_EMAIL
                    FROM tb_racc
                    JOIN tb_member
                    ON(tb_racc.ACC_MBR = tb_member.MBR_ID)
                    WHERE tb_racc.ACC_LOGIN = tb_internal_transfer.IT_FROM
                    LIMIT 1
            ) AS IT_TO,
            IT_TICKET_FROM,
            IT_TICKET_TO,
            IT_AMOUNT
        FROM tb_internal_transfer
    ');

    $dt->edit('IT_DATETIME', function($data){
        return '
            <div class="text-center">
                '.$data["IT_DATETIME"].'
            </div>
        ';
    });

    $dt->edit('IT_AMOUNT', function($data){
        return '
            <div class="text-end">
                '.number_format(($data["IT_AMOUNT"] ?? 0), 2).'
            </div>
        ';
    });

    echo $dt->generate()->toJson();