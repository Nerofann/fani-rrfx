<?php

    $dt->query('
        SELECT
            mt5_users.REGDATE,
            mt5_users.LOGIN,
            mt5_users.`NAME`,
            mt5_users.EMAIL,
            mt5_users.BALANCE,
            mt5_users.PREVBALANCE,
            mt5_users.PREVMONTHBALANCE
        FROM mt5_users
    ');

    $dt->edit('REGDATE', function($data){
        return '
            <div class="text-center">
                '.$data["REGDATE"].'
            </div>
        ';
    });

    $dt->edit('BALANCE', function($data){
        return '
            <div class="text-end">
                '.number_format(($data["BALANCE"] ?? 0), 2).'
            </div>
        ';
    });

    $dt->edit('PREVBALANCE', function($data){
        return '
            <div class="text-end">
                '.number_format(($data["PREVBALANCE"] ?? 0), 2).'
            </div>
        ';
    });

    $dt->edit('PREVMONTHBALANCE', function($data){
        return '
            <div class="text-end">
                '.number_format(($data["PREVMONTHBALANCE"] ?? 0), 2).'
            </div>
        ';
    });

    echo $dt->generate()->toJson();