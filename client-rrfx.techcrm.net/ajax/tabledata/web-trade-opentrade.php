<?php

use App\Models\Account;
use App\Models\Helper;
$login = Helper::form_input($_GET['account'] ?? 0);
$account = Account::realAccountDetail_byLogin($login);
$dt->query("
    SELECT 
        SYMBOL,
        TICKET,
        OPEN_TIME,
        CMD,
        VOLUME,
        OPEN_PRICE,
        SL,
        TP
    FROM mt5_trades
    WHERE LOGIN = '{$login}' 
    AND CLOSE_TIME IS NULL
");

$dt->edit('CMD', fn($col): string => ucwords($col['CMD']));
$dt->edit('OPEN_PRICE', fn($col): string => Helper::formatCurrency($col['OPEN_PRICE']));

$dt->add('ACTION', function($col) {
    return '<a class="btn btn-sm btn-danger close" data-ticket="'.$col['TICKET'].'"><i class="fas fa-close text-white"></i></a>'; 
});

echo $dt->generate()->toJson();