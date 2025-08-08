<?php

use App\Models\Account;
use App\Models\Helper;
$login = Helper::form_input($_GET['account'] ?? 0);
$account = Account::realAccountDetail_byLogin($login);
$dt->query("
    SELECT 
        OPEN_TIME,
        TICKET,
        SYMBOL,
        CMD,
        VOLUME,
        OPEN_PRICE,
        SL,
        TP,
        PROFIT
    FROM mt5_trades
    WHERE LOGIN = '{$login}' 
    AND CLOSE_TIME IS NOT NULL
");

$dt->edit('CMD', fn($col): string => ucwords($col['CMD']));
$dt->edit('OPEN_PRICE', fn($col): string => Helper::formatCurrency($col['OPEN_PRICE']));


echo $dt->generate()->toJson();